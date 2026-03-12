<?php
/**
 * Akaroon Admin — Document Upload Portal
 * ─────────────────────────────────────────────────────────────────────────────
 * Authentication : WordPress admin session (blog/wp-load.php)
 * File storage   : Google Cloud Storage in production (MEDIA_BASE_URL set)
 *                  Local filesystem in local dev (MEDIA_BASE_URL not set)
 * Cover image    : Manual upload  OR  auto-generated from PDF page 1 (Ghostscript)
 * DB             : akaroon_akaroondb — 7 real category tables (tas, edu, philo…)
 * UI             : Arabic / English bilingual, RTL, AJAX progress upload
 * ─────────────────────────────────────────────────────────────────────────────
 * BUG FIXES (v2):
 *   - org folder = 'منظمات' (NOT 'المنظمات') to match GCS bucket structure
 *   - status = 0 on INSERT (0 = published; fetch_data.php queries WHERE status=0)
 *   - AJAX upload with progress bar
 *   - Recent uploads table (last 10 across all tables)
 *   - Direct GCS PDF link in success response
 */

/* ── 0. WordPress authentication ─────────────────────────────────────────── */
require_once __DIR__ . '/../blog/wp-load.php';

if ( ! is_user_logged_in() || ! current_user_can('administrator') ) {
    auth_redirect();
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
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'DB error: ' . $e->getMessage()]);
        exit;
    }
    die('<p style="color:red;padding:2rem">DB connection failed: '
        . htmlspecialchars($e->getMessage()) . '</p>');
}

/* ── 2. Category map ──────────────────────────────────────────────────────── */
// table_key => [GCS/filesystem folder (Arabic), Arabic display label, English label]
// IMPORTANT: 'org' folder is 'منظمات' (NO 'ال') to match GCS bucket structure
const CATEGORIES = [
    'tas'   => ['التأصيل',   'الدراسات التأصيلية',   'Islamic Foundations'],
    'edu'   => ['التعليم',   'التعليم',               'Education'],
    'philo' => ['الفلسفة',   'الفلسفة',               'Philosophy'],
    'pol'   => ['السياسة',   'السياسة',               'Politics'],
    'soc'   => ['المجتمع',   'المجتمع',               'Society'],
    'state' => ['الدولة',    'الدولة',                'The State'],
    'org'   => ['منظمات',    'المنظمات',              'Organisations'],  // FIX: GCS folder = منظمات
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

/* ── 7. Helper: recent uploads across all 7 tables ───────────────────────── */
function getRecentUploads(PDO $pdo, int $limit = 10): array {
    $unions = [];
    foreach (array_keys(CATEGORIES) as $table) {
        $catFolder = CATEGORIES[$table][0];
        $catEn     = CATEGORIES[$table][2];
        $unions[]  = "SELECT id, '{$table}' AS tbl, '{$catFolder}' AS folder, '{$catEn}' AS cat_en,
                             The_Title_of_Paper_Book AS title, The_number_of_the_Author AS author, Year_of_issue AS year
                      FROM `{$table}` ORDER BY id DESC LIMIT {$limit}";
    }
    $sql = "SELECT * FROM (" . implode(" UNION ALL ", $unions) . ") t ORDER BY id DESC LIMIT {$limit}";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/* ── 8. Pre-load next IDs for JS hint ────────────────────────────────────── */
$nextIds = [];
foreach (array_keys(CATEGORIES) as $key) {
    $nextIds[$key] = nextId($pdo, $key);
}

/* ── 9. Handle AJAX POST ──────────────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    $tableKey = $_POST['category']  ?? '';
    $title    = trim($_POST['title']    ?? '');
    $author   = trim($_POST['author']   ?? '');
    $year     = trim($_POST['year']     ?? '');
    $place    = trim($_POST['place']    ?? '');
    $field    = trim($_POST['field']    ?? '');
    $keywords = trim($_POST['keywords'] ?? '');

    // Validate
    if (!array_key_exists($tableKey, CATEGORIES)) {
        echo json_encode(['ok' => false, 'msg' => 'يرجى اختيار تصنيف صحيح / Please select a valid category.']); exit;
    }
    if (empty($title)) {
        echo json_encode(['ok' => false, 'msg' => 'العنوان مطلوب / Title is required.']); exit;
    }
    if (empty($author)) {
        echo json_encode(['ok' => false, 'msg' => 'اسم المؤلف مطلوب / Author name is required.']); exit;
    }
    if (empty($_FILES['pdf']['name']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['ok' => false, 'msg' => 'يرجى رفع ملف PDF / Please upload a PDF file.']); exit;
    }
    if (strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION)) !== 'pdf') {
        echo json_encode(['ok' => false, 'msg' => 'الملف يجب أن يكون PDF / File must be a PDF.']); exit;
    }

    [$catFolder, $catAr, $catEn] = CATEGORIES[$tableKey];

    $pdo->beginTransaction();
    $tmpPdf = null;
    $tmpJpg = null;

    try {
        $newId  = nextId($pdo, $tableKey);
        $tmpPdf = sys_get_temp_dir() . "/{$newId}_upload.pdf";
        $tmpJpg = sys_get_temp_dir() . "/{$newId}_cover.jpg";

        if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $tmpPdf)) {
            throw new Exception('Could not save uploaded PDF / تعذّر حفظ ملف PDF');
        }

        // Cover image
        $hasCover = false;
        if (!empty($_FILES['cover']['name']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                throw new Exception('صورة الغلاف يجب أن تكون JPG أو PNG / Cover must be JPG or PNG.');
            }
            move_uploaded_file($_FILES['cover']['tmp_name'], $tmpJpg);
            $hasCover = true;
        } else {
            $hasCover = generateCoverFromPdf($tmpPdf, $tmpJpg);
        }

        $mediaBase = getenv('MEDIA_BASE_URL');
        $gcsBase   = 'https://storage.googleapis.com/akaroon-media';

        if ($mediaBase) {
            /* ── Production: upload to GCS ───────────────────────── */
            $pdfGcs = "files/{$catFolder}/files/{$newId}.pdf";
            if (!uploadToGcs($tmpPdf, $pdfGcs, 'application/pdf')) {
                throw new Exception('فشل رفع PDF إلى GCS / GCS PDF upload failed.');
            }
            if ($hasCover) {
                uploadToGcs($tmpJpg, "files/{$catFolder}/image/{$newId}.jpg", 'image/jpeg');
            }
            $pdfUrl = "{$gcsBase}/files/{$catFolder}/files/{$newId}.pdf";
        } else {
            /* ── Local dev: save to filesystem ───────────────────── */
            $pdfDir = __DIR__ . "/../files/{$catFolder}/files";
            $imgDir = __DIR__ . "/../files/{$catFolder}/image";
            @mkdir($pdfDir, 0755, true);
            @mkdir($imgDir, 0755, true);
            copy($tmpPdf, "{$pdfDir}/{$newId}.pdf");
            if ($hasCover) copy($tmpJpg, "{$imgDir}/{$newId}.jpg");
            $pdfUrl = "/files/{$catFolder}/files/{$newId}.pdf";
        }

        @unlink($tmpPdf);
        @unlink($tmpJpg);

        // FIX: status = 0 (published). fetch_data.php queries WHERE status = 0.
        $stmt = $pdo->prepare("
            INSERT INTO `{$tableKey}`
                (id, image, Category, The_Title_of_Paper_Book,
                 The_number_of_the_Author, Year_of_issue,
                 Place_of_issue, Field_of_research, Key_words, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
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

        echo json_encode([
            'ok'       => true,
            'id'       => $newId,
            'title'    => $title,
            'catAr'    => $catAr,
            'catEn'    => $catEn,
            'pdfUrl'   => $pdfUrl,
            'searchUrl'=> "../files/{$catFolder}/search.php",
            'nextId'   => $newId + 1,
            'tableKey' => $tableKey,
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        if ($tmpPdf) @unlink($tmpPdf);
        if ($tmpJpg) @unlink($tmpJpg);
        echo json_encode(['ok' => false, 'msg' => 'خطأ / Error: ' . $e->getMessage()]);
    }
    exit;
}

/* ── 10. Load recent uploads for the HTML page ───────────────────────────── */
$recentUploads = getRecentUploads($pdo, 10);
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>أكارون — رفع وثيقة | Akaroon Upload</title>
  <!-- Bootstrap 5 RTL -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="../css/akaroon-theme.css?v=<?= filemtime(__DIR__ . '/../css/akaroon-theme.css') ?>">
  <style>
    body { background: var(--black-bg); color: var(--text-light); }

    /* ── Navbar ── */
    .ak-admin-nav { display:flex; align-items:center; justify-content:space-between;
      padding:.8rem 2rem; background:rgba(0,0,0,.6); border-bottom:1px solid var(--border-dark); }
    .ak-admin-nav .brand { color:var(--gold); font-weight:700; font-size:1.2rem; text-decoration:none; }
    .ak-admin-nav .user-badge { font-size:.85rem; color:#aaa; }
    .ak-admin-nav .user-badge strong { color:var(--gold); }

    /* ── Card ── */
    .upload-card { max-width:860px; margin:2.5rem auto 1.5rem;
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

    /* ── Progress bar ── */
    #progressWrap { display:none; margin-top:1rem; }
    #progressWrap .prog-label { font-size:.85rem; color:#aaa; margin-bottom:.4rem; }
    .progress { height:10px; background:#1e1e1e; border-radius:6px; overflow:hidden; }
    .progress-bar { background:var(--gold); transition:width .2s ease; }

    /* ── Result boxes ── */
    #resultBox { display:none; margin-top:1rem; }
    .success-box { background:rgba(76,175,80,.1); border:1px solid #4caf50;
      border-radius:10px; padding:1.2rem 1.5rem; }
    .error-box { background:rgba(220,53,69,.1); border:1px solid #dc3545;
      border-radius:10px; padding:1.2rem 1.5rem; }
    .doc-link { color:#81c784; text-decoration:underline; }
    .doc-link:hover { color:#a5d6a7; }
    .gcs-link { color:#90caf9; text-decoration:underline; word-break:break-all; font-size:.85rem; }

    /* ── Section title ── */
    .section-title { font-size:.82rem; text-transform:uppercase; letter-spacing:.08em;
      color:#666; margin:1.4rem 0 .8rem; border-top:1px solid var(--border-dark);
      padding-top:1rem; }

    /* ── Recent uploads table ── */
    .recent-card { max-width:860px; margin:0 auto 4rem;
      background:rgba(255,255,255,.03); border:1px solid var(--border-dark);
      border-radius:14px; padding:1.5rem 2rem; }
    .recent-card h3 { color:#aaa; font-size:1rem; margin-bottom:1rem;
      border-bottom:1px solid var(--border-dark); padding-bottom:.7rem; }
    .recent-table { width:100%; border-collapse:collapse; font-size:.85rem; }
    .recent-table th { color:#666; font-weight:500; padding:.45rem .6rem;
      border-bottom:1px solid var(--border-dark); text-align:right; }
    .recent-table td { padding:.45rem .6rem; border-bottom:1px solid rgba(255,255,255,.04);
      color:#ccc; vertical-align:middle; }
    .recent-table tr:last-child td { border-bottom:none; }
    .recent-table .cat-badge { display:inline-block; background:rgba(198,168,124,.1);
      border:1px solid rgba(198,168,124,.3); color:var(--gold);
      border-radius:4px; padding:.1rem .4rem; font-size:.78rem; }
    .recent-table .id-num { color:#555; font-size:.8rem; }
    .recent-table a { color:#90caf9; text-decoration:none; }
    .recent-table a:hover { text-decoration:underline; }
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

    <!-- Result box (filled by JS after AJAX) -->
    <div id="resultBox"></div>

    <form id="uploadForm" enctype="multipart/form-data">

      <!-- Category -->
      <div class="mb-3">
        <label class="field-label">التصنيف <span class="en">/ Category *</span></label>
        <select name="category" class="form-select" id="categorySelect" required>
          <option value="">— اختر / Select —</option>
          <?php foreach (CATEGORIES as $key => [$folder, $arLabel, $enLabel]): ?>
          <option value="<?= $key ?>" data-next="<?= $nextIds[$key] ?>">
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
          placeholder="مثال: الوسطية التشريعية الإسلامية">
      </div>

      <!-- Author + Year -->
      <div class="row g-3 mb-3">
        <div class="col-md-7">
          <label class="field-label" for="author">اسم المؤلف <span class="en">/ Author *</span></label>
          <input type="text" name="author" id="author" class="form-control" required
            placeholder="مثال: أحمد عبد الله">
        </div>
        <div class="col-md-5">
          <label class="field-label" for="year">سنة الإصدار <span class="en">/ Year</span></label>
          <input type="text" name="year" id="year" class="form-control" placeholder="مثال: 2024">
        </div>
      </div>

      <!-- Place + Field -->
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="field-label" for="place">مكان الإصدار <span class="en">/ Place of Issue</span></label>
          <input type="text" name="place" id="place" class="form-control" placeholder="مثال: الخرطوم">
        </div>
        <div class="col-md-6">
          <label class="field-label" for="field">مجال البحث <span class="en">/ Field of Research</span></label>
          <input type="text" name="field" id="field" class="form-control" placeholder="مثال: الدراسات الإسلامية">
        </div>
      </div>

      <!-- Keywords -->
      <div class="mb-3">
        <label class="field-label" for="keywords">الكلمات المفتاحية <span class="en">/ Keywords</span></label>
        <input type="text" name="keywords" id="keywords" class="form-control"
          placeholder="مثال: فقه، شريعة، تشريع (مفصولة بفاصلة)">
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

      <!-- Progress bar (shown during upload) -->
      <div id="progressWrap">
        <div class="prog-label">
          <span id="progText">جارٍ الرفع... / Uploading...</span>
          <span id="progPct" style="float:left;color:var(--gold);font-weight:600"></span>
        </div>
        <div class="progress">
          <div class="progress-bar" id="progressBar" role="progressbar" style="width:0%"></div>
        </div>
      </div>

      <!-- Submit -->
      <button type="submit" class="btn-upload mt-3" id="submitBtn">
        🚀 &nbsp; رفع وإضافة إلى المكتبة / Upload &amp; Add to Library
      </button>
      <p class="mt-2 text-center" style="font-size:.78rem;color:#555">
        سيتم تخصيص رقم معرّف تلقائي وتسمية الملفات وحفظها في المكان الصحيح
        / ID assigned automatically, files named and stored correctly
      </p>

    </form>
  </div><!-- /upload-card -->

  <!-- ── Recent Uploads ─────────────────────────────────────────────────── -->
  <div class="recent-card" id="recentCard">
    <h3>🕒 آخر الوثائق المضافة <span style="font-size:.85rem;color:#555">/ Recently Added (last 10)</span></h3>
    <?php if (empty($recentUploads)): ?>
      <p style="color:#555;font-size:.88rem">لا توجد وثائق حتى الآن / No documents yet.</p>
    <?php else: ?>
    <table class="recent-table" id="recentTable">
      <thead>
        <tr>
          <th>#</th>
          <th>العنوان / Title</th>
          <th>المؤلف / Author</th>
          <th>التصنيف / Category</th>
          <th>PDF</th>
        </tr>
      </thead>
      <tbody id="recentBody">
        <?php foreach ($recentUploads as $r):
          $gcsUrl = 'https://storage.googleapis.com/akaroon-media/files/'
                  . urlencode($r['folder']) . '/files/' . $r['id'] . '.pdf';
        ?>
        <tr>
          <td class="id-num"><?= (int)$r['id'] ?></td>
          <td><?= htmlspecialchars(mb_strimwidth($r['title'] ?? '—', 0, 55, '…')) ?></td>
          <td><?= htmlspecialchars(mb_strimwidth($r['author'] ?? '—', 0, 30, '…')) ?></td>
          <td><span class="cat-badge"><?= htmlspecialchars($r['cat_en']) ?></span></td>
          <td><a href="<?= htmlspecialchars($gcsUrl) ?>" target="_blank">PDF ↗</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

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
      label.innerHTML = zoneId === 'pdfZone'
        ? 'اضغط لاختيار ملف PDF<br><small>Click to choose PDF</small>'
        : 'اضغط لاختيار صورة<br><small>Click to choose image</small>';
      zone.classList.remove('has-file');
    }
  });
}
wireDropZone('pdfInput',   'pdfZone',   'pdfLabel');
wireDropZone('coverInput', 'coverZone', 'coverLabel');

/* ── AJAX Upload with progress bar ──────────────────────────────────────── */
document.getElementById('uploadForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const form       = this;
  const submitBtn  = document.getElementById('submitBtn');
  const progWrap   = document.getElementById('progressWrap');
  const progBar    = document.getElementById('progressBar');
  const progPct    = document.getElementById('progPct');
  const progText   = document.getElementById('progText');
  const resultBox  = document.getElementById('resultBox');

  // Reset UI
  resultBox.style.display = 'none';
  resultBox.innerHTML = '';
  submitBtn.disabled = true;
  submitBtn.textContent = '⏳ جارٍ الرفع... / Uploading...';
  progWrap.style.display = 'block';
  progBar.style.width = '0%';
  progPct.textContent = '0%';

  const data = new FormData(form);
  const xhr  = new XMLHttpRequest();

  // Progress event — tracks bytes sent to server
  xhr.upload.addEventListener('progress', function (ev) {
    if (ev.lengthComputable) {
      const pct = Math.round((ev.loaded / ev.total) * 100);
      progBar.style.width = pct + '%';
      progPct.textContent = pct + '%';
      if (pct >= 100) {
        progText.textContent = 'جارٍ المعالجة... / Processing on server...';
      }
    }
  });

  xhr.addEventListener('load', function () {
    progWrap.style.display = 'none';
    submitBtn.disabled = false;
    submitBtn.textContent = '🚀   رفع وإضافة إلى المكتبة / Upload & Add to Library';

    let res;
    try { res = JSON.parse(xhr.responseText); }
    catch (err) {
      showError('خطأ في الاستجابة / Unexpected server response.');
      return;
    }

    if (res.ok) {
      showSuccess(res);
      updateNextId(res.tableKey, res.nextId);
      prependToRecentTable(res);
      form.reset();
      // Reset drop zones
      ['pdfZone','coverZone'].forEach(id => document.getElementById(id).classList.remove('has-file'));
      document.getElementById('pdfLabel').innerHTML = 'اضغط لاختيار ملف PDF<br><small>Click to choose PDF</small>';
      document.getElementById('coverLabel').innerHTML = 'اضغط لاختيار صورة<br><small>Click to choose image</small>';
      idRow.style.display = 'none';
    } else {
      showError(res.msg || 'خطأ غير معروف / Unknown error.');
    }
  });

  xhr.addEventListener('error', function () {
    progWrap.style.display = 'none';
    submitBtn.disabled = false;
    submitBtn.textContent = '🚀   رفع وإضافة إلى المكتبة / Upload & Add to Library';
    showError('فشل الاتصال / Network error. Please try again.');
  });

  xhr.open('POST', '?ajax=1');
  xhr.send(data);
});

function showSuccess(res) {
  const resultBox = document.getElementById('resultBox');
  resultBox.style.display = 'block';
  resultBox.innerHTML = `
    <div class="success-box">
      <strong style="color:#81c784">✓ تمت الإضافة بنجاح / Successfully added — ID #${res.id}</strong>
      <div class="mt-2" style="font-size:.88rem">
        <strong style="color:#ccc">${escHtml(res.title)}</strong>
        &nbsp;·&nbsp; <span style="color:#888">${escHtml(res.catEn)}</span>
      </div>
      <div class="mt-2" style="font-size:.83rem">
        📄 <a class="gcs-link" href="${escHtml(res.pdfUrl)}" target="_blank">${escHtml(res.pdfUrl)}</a>
      </div>
      <div class="mt-2" style="font-size:.83rem">
        <a class="doc-link" href="${escHtml(res.searchUrl)}" target="_blank">عرض الفئة / View Category ↗</a>
      </div>
    </div>`;
  resultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function showError(msg) {
  const resultBox = document.getElementById('resultBox');
  resultBox.style.display = 'block';
  resultBox.innerHTML = `<div class="error-box"><strong style="color:#ef9a9a">✗ ${escHtml(msg)}</strong></div>`;
  resultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function updateNextId(tableKey, nextId) {
  document.querySelectorAll('#categorySelect option').forEach(opt => {
    if (opt.value === tableKey) opt.dataset.next = nextId;
  });
  if (categorySelect.value === tableKey) idBadge.textContent = '#' + nextId;
}

function prependToRecentTable(res) {
  const tbody = document.getElementById('recentBody');
  if (!tbody) return;

  // Build GCS URL
  const catFolder = document.querySelector(`#categorySelect option[value="${res.tableKey}"]`)
                              ?.text.split(' / ')[0] || '';
  const gcsUrl = res.pdfUrl;

  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td class="id-num">${res.id}</td>
    <td>${escHtml(res.title.substring(0, 55))}</td>
    <td>—</td>
    <td><span class="cat-badge">${escHtml(res.catEn)}</span></td>
    <td><a href="${escHtml(gcsUrl)}" target="_blank">PDF ↗</a></td>`;

  tbody.insertBefore(tr, tbody.firstChild);
  // Remove the last row if > 10
  while (tbody.rows.length > 10) tbody.deleteRow(tbody.rows.length - 1);
}

function escHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}
</script>
</body>
</html>
