<?php
/**
 * Akaroon Admin — Document Upload Portal
 * ─────────────────────────────────────────────────────────────────────────────
 * Authentication : WordPress admin session (blog/wp-load.php)
 * File storage   : Google Cloud Storage in production (MEDIA_BASE_URL set)
 *                  Local filesystem in local dev (MEDIA_BASE_URL not set)
 * Cover image    : Manual upload  OR  auto-generated from PDF page 1 (Ghostscript)
 * DB             : akaroon_akaroondb — 7 real category tables (tas, edu, philo…)
 * UI             : Arabic / English bilingual, RTL
 * ─────────────────────────────────────────────────────────────────────────────
 */

/* ── 0. WordPress authentication ─────────────────────────────────────────── */
require_once __DIR__ . '/../blog/wp-load.php';

if ( ! is_user_logged_in() || ! current_user_can('administrator') ) {
    auth_redirect();   // WordPress built-in: redirects to /blog/wp-login.php
    exit;
}
$wpUser = wp_get_current_user()->display_name;

/* ── 1. Library DB connection ─────────────────────────────────────────────── */
$_host = getenv('DB_HOST') ?: (getenv('WP_DB_HOST') ?: 'mysql');
$_user = getenv('DB_USER')     ?: 'root';
$_pass = getenv('DB_PASSWORD') ?: 'root';

try {
    $pdo = new PDO(
        "mysql:host={$_host};dbname=akaroon_akaroondb;charset=utf8mb4",
        $_user, $_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('<p style="color:red;padding:2rem">DB connection failed: '
        . htmlspecialchars($e->getMessage()) . '</p>');
}

/* ── 2. Category map ──────────────────────────────────────────────────────── */
// table_key => [GCS/filesystem folder (Arabic), Arabic label, English label]
const CATEGORIES = [
    'tas'   => ['التأصيل',   'الدراسات التأصيلية',   'Islamic Foundations'],
    'edu'   => ['التعليم',   'التعليم',               'Education'],
    'philo' => ['الفلسفة',   'الفلسفة',               'Philosophy'],
    'pol'   => ['السياسة',   'السياسة',               'Politics'],
    'soc'   => ['المجتمع',   'المجتمع',               'Society'],
    'state' => ['الدولة',    'الدولة',                'The State'],
    'org'   => ['المنظمات',  'المنظمات',              'Organisations'],
];

/* ── 3. Helper: next safe ID for a table ─────────────────────────────────── */
function nextId(PDO $pdo, string $table): int {
    $stmt = $pdo->query("SELECT COALESCE(MAX(id), 0) FROM `{$table}`");
    return (int)$stmt->fetchColumn() + 1;
}

/* ── 4. Helper: GCS access token (Cloud Run metadata server) ─────────────── */
function gcsToken(): ?string {
    static $tok = null;
    if ($tok !== null) return $tok;
    $ch = curl_init(
        'http://metadata.google.internal/computeMetadata/v1/instance/service-accounts/default/token'
    );
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER     => ['Metadata-Flavor: Google'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 3,
    ]);
    $r = @curl_exec($ch);
    curl_close($ch);
    if (!$r) return ($tok = null);
    $tok = json_decode($r, true)['access_token'] ?? null;
    return $tok;
}

/* ── 5. Helper: stream a local file to GCS ───────────────────────────────── */
function uploadToGcs(string $localFile, string $gcsObject, string $mime): bool {
    $token = gcsToken();
    if (!$token) return false;

    $url = 'https://storage.googleapis.com/upload/storage/v1/b/akaroon-media/o?'
         . http_build_query(['uploadType' => 'media', 'name' => $gcsObject, 'predefinedAcl' => 'publicRead']);

    $fp = fopen($localFile, 'r');
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_PUT            => true,
        CURLOPT_INFILE         => $fp,
        CURLOPT_INFILESIZE     => filesize($localFile),
        CURLOPT_HTTPHEADER     => ["Authorization: Bearer {$token}", "Content-Type: {$mime}"],
        CURLOPT_RETURNTRANSFER => true,
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    return $code >= 200 && $code < 300;
}

/* ── 6. Helper: auto-generate cover JPG from PDF page 1 (Ghostscript) ────── */
function generateCoverFromPdf(string $pdfPath, string $outJpg): bool {
    $gs = trim(@shell_exec('which gs 2>/dev/null'));
    if (!$gs) return false;
    $cmd = sprintf(
        '%s -dNOPAUSE -dBATCH -sDEVICE=jpeg -dFirstPage=1 -dLastPage=1 -r150 -dJPEGQ=85 -sOutputFile=%s %s 2>/dev/null',
        escapeshellcmd($gs),
        escapeshellarg($outJpg),
        escapeshellarg($pdfPath)
    );
    exec($cmd, $out, $code);
    return $code === 0 && file_exists($outJpg) && filesize($outJpg) > 0;
}

/* ── 7. Pre-load next IDs for JS hint ────────────────────────────────────── */
$nextIds = [];
foreach (array_keys(CATEGORIES) as $key) {
    $nextIds[$key] = nextId($pdo, $key);
}

/* ── 8. Handle POST ───────────────────────────────────────────────────────── */
$msg = '';
$msgType = '';
$newDocId = null;
$newDocCategory = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tableKey = $_POST['category']  ?? '';
    $title    = trim($_POST['title']    ?? '');
    $author   = trim($_POST['author']   ?? '');
    $year     = trim($_POST['year']     ?? '');
    $place    = trim($_POST['place']    ?? '');
    $field    = trim($_POST['field']    ?? '');
    $keywords = trim($_POST['keywords'] ?? '');

    // Validate
    if (!array_key_exists($tableKey, CATEGORIES)) {
        $msg = 'يرجى اختيار تصنيف صحيح / Please select a valid category.';
        $msgType = 'danger';
    } elseif (empty($title)) {
        $msg = 'العنوان مطلوب / Title is required.';
        $msgType = 'danger';
    } elseif (empty($author)) {
        $msg = 'اسم المؤلف مطلوب / Author name is required.';
        $msgType = 'danger';
    } elseif (empty($_FILES['pdf']['name']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
        $msg = 'يرجى رفع ملف PDF / Please upload a PDF file.';
        $msgType = 'danger';
    } elseif (strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION)) !== 'pdf') {
        $msg = 'الملف يجب أن يكون PDF / File must be a PDF.';
        $msgType = 'danger';
    } else {
        [$catFolder, $catAr, $catEn] = CATEGORIES[$tableKey];

        $pdo->beginTransaction();
        $tmpPdf = null;
        $tmpJpg = null;

        try {
            $newId  = nextId($pdo, $tableKey);
            $tmpPdf = sys_get_temp_dir() . "/{$newId}_upload.pdf";
            $tmpJpg = sys_get_temp_dir() . "/{$newId}_cover.jpg";

            // Save PDF to tmp
            if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $tmpPdf)) {
                throw new Exception('Could not save uploaded PDF / تعذّر حفظ ملف PDF');
            }

            // Cover image: manual upload OR auto-generate via Ghostscript
            $hasCover = false;
            if (!empty($_FILES['cover']['name']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    throw new Exception('صورة الغلاف يجب أن تكون JPG أو PNG / Cover must be JPG or PNG.');
                }
                move_uploaded_file($_FILES['cover']['tmp_name'], $tmpJpg);
                $hasCover = true;
            } else {
                // Auto-generate from PDF page 1
                $hasCover = generateCoverFromPdf($tmpPdf, $tmpJpg);
            }

            $mediaBase = getenv('MEDIA_BASE_URL');

            if ($mediaBase) {
                /* ── Production: upload to GCS ───────────────────────── */
                $pdfGcs = "files/{$catFolder}/files/{$newId}.pdf";
                if (!uploadToGcs($tmpPdf, $pdfGcs, 'application/pdf')) {
                    throw new Exception('فشل رفع PDF إلى GCS / GCS PDF upload failed.');
                }
                if ($hasCover) {
                    uploadToGcs($tmpJpg, "files/{$catFolder}/image/{$newId}.jpg", 'image/jpeg');
                }
            } else {
                /* ── Local dev: save to filesystem ───────────────────── */
                $pdfDir = __DIR__ . "/../files/{$catFolder}/files";
                $imgDir = __DIR__ . "/../files/{$catFolder}/image";
                @mkdir($pdfDir, 0755, true);
                @mkdir($imgDir, 0755, true);
                copy($tmpPdf, "{$pdfDir}/{$newId}.pdf");
                if ($hasCover) copy($tmpJpg, "{$imgDir}/{$newId}.jpg");
            }

            // Clean tmp files
            @unlink($tmpPdf);
            @unlink($tmpJpg);

            // Insert DB record — use real table columns
            $stmt = $pdo->prepare("
                INSERT INTO `{$tableKey}`
                    (id, image, Category, The_Title_of_Paper_Book,
                     The_number_of_the_Author, Year_of_issue,
                     Place_of_issue, Field_of_research, Key_words, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([
                $newId,
                $hasCover ? "{$newId}.jpg" : null,
                $catFolder,
                $title,
                $author,
                $year,
                $place,
                $field,
                $keywords,
            ]);

            $pdo->commit();

            $newDocId       = $newId;
            $newDocCategory = $catFolder;
            $msg     = "تمت الإضافة بنجاح / Successfully added — ID #{$newId}: {$title}";
            $msgType = 'success';

            // Refresh next IDs after successful insert
            $nextIds[$tableKey] = $newId + 1;

        } catch (Exception $e) {
            $pdo->rollBack();
            if ($tmpPdf) @unlink($tmpPdf);
            if ($tmpJpg) @unlink($tmpJpg);
            $msg     = 'خطأ / Error: ' . htmlspecialchars($e->getMessage());
            $msgType = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>أكارون — رفع وثيقة | Akaroon Upload</title>
  <!-- Bootstrap 5 RTL -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="../css/akaroon-theme.css">
  <style>
    body { background: var(--black-bg); color: var(--text-light); }

    /* ── Navbar ── */
    .ak-admin-nav { display:flex; align-items:center; justify-content:space-between;
      padding:.8rem 2rem; background:rgba(0,0,0,.6); border-bottom:1px solid var(--border-dark); }
    .ak-admin-nav .brand { color:var(--gold); font-weight:700; font-size:1.2rem; text-decoration:none; }
    .ak-admin-nav .user-badge { font-size:.85rem; color:#aaa; }
    .ak-admin-nav .user-badge strong { color:var(--gold); }

    /* ── Card ── */
    .upload-card { max-width:820px; margin:2.5rem auto 4rem;
      background:rgba(255,255,255,.04); border:1px solid var(--border-dark);
      border-radius:14px; padding:2.2rem 2.5rem; }
    .upload-card h2 { color:var(--gold); border-bottom:1px solid var(--border-dark);
      padding-bottom:.9rem; margin-bottom:1.8rem; font-size:1.4rem; }

    /* ── Labels ── */
    .field-label { display:block; color:var(--gold); font-weight:600;
      margin-bottom:.3rem; font-size:.92rem; }
    .field-label .en { color:#888; font-weight:400; font-size:.82rem; margin-right:.4rem; }

    /* ── Inputs ── */
    .form-control, .form-select {
      background:#141414; border:1px solid var(--border-dark);
      color:#eee; border-radius:8px; }
    .form-control:focus, .form-select:focus {
      background:#1c1c1c; border-color:var(--gold);
      box-shadow:0 0 0 .2rem rgba(198,168,124,.2); color:#fff; }
    .form-select option { background:#1c1c1c; }

    /* ── File drop zones ── */
    .drop-zone { border:2px dashed var(--border-dark); border-radius:10px;
      padding:1.4rem 1rem; text-align:center; background:rgba(0,0,0,.25);
      transition:border-color .2s; cursor:pointer; }
    .drop-zone:hover { border-color:var(--gold); }
    .drop-zone .dz-icon { font-size:2rem; display:block; margin-bottom:.4rem; }
    .drop-zone .dz-label { font-size:.88rem; color:#bbb; }
    .drop-zone .dz-sublabel { font-size:.78rem; color:#666; margin-top:.25rem; }
    .drop-zone input[type=file] { display:none; }
    .drop-zone.has-file { border-color:#4caf50; background:rgba(76,175,80,.06); }
    .drop-zone.has-file .dz-label { color:#81c784; }

    /* ── ID badge ── */
    .id-badge { display:inline-block; background:rgba(198,168,124,.12);
      border:1px solid var(--gold); color:var(--gold); border-radius:6px;
      padding:.15rem .65rem; font-size:.85rem; font-weight:600; }

    /* ── Submit button ── */
    .btn-upload { background:var(--gold); color:#000; font-weight:700;
      border:none; border-radius:8px; padding:.75rem 2rem;
      font-size:1rem; width:100%; transition:opacity .2s; }
    .btn-upload:hover { opacity:.88; }
    .btn-upload:disabled { opacity:.45; cursor:not-allowed; }

    /* ── Success box ── */
    .success-box { background:rgba(76,175,80,.1); border:1px solid #4caf50;
      border-radius:10px; padding:1.2rem 1.5rem; margin-bottom:1.5rem; }
    .success-box .doc-link { color:#81c784; text-decoration:underline; }
    .success-box .doc-link:hover { color:#a5d6a7; }

    /* ── Section title ── */
    .section-title { font-size:.82rem; text-transform:uppercase; letter-spacing:.08em;
      color:#666; margin:1.4rem 0 .8rem; border-top:1px solid var(--border-dark);
      padding-top:1rem; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="ak-admin-nav">
  <a href="../" class="brand">أكارون | Akaroon</a>
  <span class="user-badge">
    مرحباً / Welcome, <strong><?= htmlspecialchars($wpUser) ?></strong> &nbsp;·&nbsp;
    <a href="<?= wp_logout_url('../') ?>" style="color:#888;font-size:.8rem;">تسجيل خروج / Logout</a>
  </span>
</nav>

<div class="container">
  <div class="upload-card">

    <h2>📤 رفع وثيقة جديدة <span style="font-size:.85rem;font-weight:400;color:#888">/ Add New Document</span></h2>

    <!-- Success -->
    <?php if ($msgType === 'success' && $newDocId): ?>
    <div class="success-box">
      <strong style="color:#81c784">✓ <?= htmlspecialchars($msg) ?></strong><br>
      <div class="mt-2" style="font-size:.88rem">
        <?php
          $searchUrl = "../files/{$newDocCategory}/search.php";
        ?>
        <a class="doc-link" href="<?= htmlspecialchars($searchUrl) ?>" target="_blank">
          عرض الفئة / View Category ↗
        </a>
      </div>
    </div>
    <?php elseif ($msg): ?>
    <div class="alert alert-<?= $msgType ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($msg) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" id="uploadForm">

      <!-- Category -->
      <div class="mb-3">
        <label class="field-label">التصنيف <span class="en">/ Category *</span></label>
        <select name="category" class="form-select" id="categorySelect" required>
          <option value="">— اختر / Select —</option>
          <?php foreach (CATEGORIES as $key => [$folder, $arLabel, $enLabel]): ?>
          <option value="<?= $key ?>"
            data-next="<?= $nextIds[$key] ?>"
            <?= (($_POST['category'] ?? '') === $key) ? 'selected' : '' ?>>
            <?= htmlspecialchars($arLabel) ?> / <?= htmlspecialchars($enLabel) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Suggested ID -->
      <div class="mb-3" id="idRow" style="display:none">
        <span class="field-label">المعرّف التالي <span class="en">/ Next ID (auto-assigned)</span></span>
        <span class="id-badge" id="idBadge">#—</span>
      </div>

      <!-- Title -->
      <div class="mb-3">
        <label class="field-label" for="title">عنوان البحث / الكتاب <span class="en">/ Title *</span></label>
        <input type="text" name="title" id="title" class="form-control" required
          placeholder="مثال: الوسطية التشريعية الإسلامية"
          value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
      </div>

      <!-- Author + Year -->
      <div class="row g-3 mb-3">
        <div class="col-md-7">
          <label class="field-label" for="author">اسم المؤلف <span class="en">/ Author *</span></label>
          <input type="text" name="author" id="author" class="form-control" required
            placeholder="مثال: أحمد عبد الله"
            value="<?= htmlspecialchars($_POST['author'] ?? '') ?>">
        </div>
        <div class="col-md-5">
          <label class="field-label" for="year">سنة الإصدار <span class="en">/ Year</span></label>
          <input type="text" name="year" id="year" class="form-control"
            placeholder="مثال: 2024"
            value="<?= htmlspecialchars($_POST['year'] ?? '') ?>">
        </div>
      </div>

      <!-- Place + Field -->
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="field-label" for="place">مكان الإصدار <span class="en">/ Place of Issue</span></label>
          <input type="text" name="place" id="place" class="form-control"
            placeholder="مثال: الخرطوم"
            value="<?= htmlspecialchars($_POST['place'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="field-label" for="field">مجال البحث <span class="en">/ Field of Research</span></label>
          <input type="text" name="field" id="field" class="form-control"
            placeholder="مثال: الدراسات الإسلامية"
            value="<?= htmlspecialchars($_POST['field'] ?? '') ?>">
        </div>
      </div>

      <!-- Keywords -->
      <div class="mb-3">
        <label class="field-label" for="keywords">الكلمات المفتاحية <span class="en">/ Keywords</span></label>
        <input type="text" name="keywords" id="keywords" class="form-control"
          placeholder="مثال: فقه، شريعة، تشريع (مفصولة بفاصلة)"
          value="<?= htmlspecialchars($_POST['keywords'] ?? '') ?>">
      </div>

      <!-- File uploads -->
      <p class="section-title">الملفات / Files</p>

      <div class="row g-3 mb-4">

        <!-- PDF -->
        <div class="col-md-6">
          <label class="field-label mb-2">ملف PDF <span class="en">/ PDF File *</span></label>
          <div class="drop-zone" id="pdfZone" onclick="document.getElementById('pdfInput').click()">
            <span class="dz-icon">📄</span>
            <input type="file" name="pdf" id="pdfInput" accept="application/pdf" required>
            <div class="dz-label" id="pdfLabel">اضغط لاختيار ملف PDF<br><small>Click to choose PDF</small></div>
          </div>
        </div>

        <!-- Cover image -->
        <div class="col-md-6">
          <label class="field-label mb-2">
            صورة الغلاف <span class="en">/ Cover Image</span>
            <span style="color:#555;font-size:.75rem"> (اختياري / optional)</span>
          </label>
          <div class="drop-zone" id="coverZone" onclick="document.getElementById('coverInput').click()">
            <span class="dz-icon">🖼️</span>
            <input type="file" name="cover" id="coverInput" accept="image/jpeg,image/png">
            <div class="dz-label" id="coverLabel">اضغط لاختيار صورة<br><small>Click to choose image</small></div>
            <div class="dz-sublabel">إذا تركت فارغاً سيتم توليد الغلاف تلقائياً من الصفحة الأولى<br>
              <em>Leave blank — cover auto-generated from PDF page 1</em></div>
          </div>
        </div>

      </div>

      <!-- Submit -->
      <button type="submit" class="btn-upload" id="submitBtn">
        🚀 &nbsp; رفع وإضافة إلى المكتبة / Upload & Add to Library
      </button>
      <p class="mt-2 text-center" style="font-size:.78rem;color:#555">
        سيتم تخصيص رقم معرّف تلقائي وتسمية الملفات وحفظها في المكان الصحيح
        / ID assigned automatically, files named and stored correctly
      </p>

    </form>
  </div><!-- /upload-card -->
</div><!-- /container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ── Category → show next ID ────────────────────────────────────────────── */
const categorySelect = document.getElementById('categorySelect');
const idRow          = document.getElementById('idRow');
const idBadge        = document.getElementById('idBadge');

categorySelect.addEventListener('change', function () {
  const opt = this.options[this.selectedIndex];
  if (this.value) {
    idBadge.textContent = '#' + opt.dataset.next;
    idRow.style.display = 'block';
  } else {
    idRow.style.display = 'none';
  }
});
// Init on page load (for retained POST value)
if (categorySelect.value) categorySelect.dispatchEvent(new Event('change'));

/* ── File drop zone labels ───────────────────────────────────────────────── */
function wireDropZone(inputId, zoneId, labelId) {
  const input = document.getElementById(inputId);
  const zone  = document.getElementById(zoneId);
  const label = document.getElementById(labelId);
  input.addEventListener('change', function () {
    if (this.files.length) {
      label.textContent = '✓ ' + this.files[0].name;
      zone.classList.add('has-file');
    } else {
      zone.classList.remove('has-file');
    }
  });
}
wireDropZone('pdfInput',   'pdfZone',   'pdfLabel');
wireDropZone('coverInput', 'coverZone', 'coverLabel');

/* ── Prevent double-submit ───────────────────────────────────────────────── */
document.getElementById('uploadForm').addEventListener('submit', function () {
  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.textContent = '⏳ جارٍ الرفع... / Uploading...';
});
</script>
</body>
</html>
