<?php
/**
 * Akaroon Admin — Document Upload Portal
 * ─────────────────────────────────────────────────────────────────────────────
 * Authentication : Standalone session-based login (NO WordPress dependency)
 *                  Credentials: ADMIN_USERNAME + ADMIN_PASSWORD_HASH in env
 * File storage   : Google Cloud Storage in production (MEDIA_BASE_URL set)
 *                  Local filesystem in local dev (MEDIA_BASE_URL not set)
 * Cover image    : Manual upload  OR  auto-generated from PDF page 1 (Ghostscript)
 * DB             : akaroon_akaroondb — 7 real category tables
 */

session_start();

// ── Load env from .env file if running locally ─────────────────────────────
$envFile = __DIR__ . '/../../tools/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        if (!getenv(trim($k))) putenv(trim($k) . '=' . trim($v));
    }
}

// ── Auth credentials from env ───────────────────────────────────────────────
define('ADMIN_USERNAME',      getenv('ADMIN_USERNAME')      ?: 'admin');
define('ADMIN_PASSWORD_HASH', getenv('ADMIN_PASSWORD_HASH') ?: '');  // bcrypt hash

// ── Logout ──────────────────────────────────────────────────────────────────
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: upload.php');
    exit;
}

// ── Handle login POST ────────────────────────────────────────────────────────
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_login'])) {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    if ($u === ADMIN_USERNAME && ADMIN_PASSWORD_HASH && password_verify($p, ADMIN_PASSWORD_HASH)) {
        $_SESSION['akaroon_admin'] = true;
        $_SESSION['akaroon_user']  = $u;
        header('Location: upload.php');
        exit;
    }
    $loginError = 'اسم المستخدم أو كلمة المرور غير صحيحة / Invalid username or password.';
    // Small delay to slow brute force
    sleep(1);
}

// ── Gate: show login page if not authenticated ───────────────────────────────
if (empty($_SESSION['akaroon_admin'])) {
    showLoginPage($loginError);
    exit;
}

$loggedInUser = $_SESSION['akaroon_user'] ?? 'admin';

// ─────────────────────────────────────────────────────────────────────────────
// From here on the user is authenticated
// ─────────────────────────────────────────────────────────────────────────────

/* ── DB connection ─────────────────────────────────────────────────────────── */
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

/* ── Category map ─────────────────────────────────────────────────────────── */
// [GCS/filesystem folder, Arabic display label, English label]
// NOTE: 'org' folder = 'منظمات' (NO 'ال') — matches GCS bucket structure
const CATEGORIES = [
    'tas'   => ['التأصيل',   'الدراسات التأصيلية',   'Islamic Foundations'],
    'edu'   => ['التعليم',   'التعليم',               'Education'],
    'philo' => ['الفلسفة',   'الفلسفة',               'Philosophy'],
    'pol'   => ['السياسة',   'السياسة',               'Politics'],
    'soc'   => ['المجتمع',   'المجتمع',               'Society'],
    'state' => ['الدولة',    'الدولة',                'The State'],
    'org'   => ['منظمات',    'المنظمات',              'Organisations'],
];

/* ── Helper: next safe ID ─────────────────────────────────────────────────── */
function nextId(PDO $pdo, string $table): int {
    $stmt = $pdo->query("SELECT COALESCE(MAX(id), 0) FROM `{$table}`");
    return (int)$stmt->fetchColumn() + 1;
}

/* ── Helper: GCS token ────────────────────────────────────────────────────── */
function gcsToken(): ?string {
    static $tok = null;
    if ($tok !== null) return $tok;
    $ch = curl_init('http://metadata.google.internal/computeMetadata/v1/instance/service-accounts/default/token');
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER     => ['Metadata-Flavor: Google'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 3,
    ]);
    $r = @curl_exec($ch); curl_close($ch);
    if (!$r) return ($tok = null);
    $tok = json_decode($r, true)['access_token'] ?? null;
    return $tok;
}

/* ── Helper: upload file to GCS ───────────────────────────────────────────── */
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
    curl_close($ch); fclose($fp);
    return $code >= 200 && $code < 300;
}

/* ── Helper: Ghostscript cover ────────────────────────────────────────────── */
function generateCoverFromPdf(string $pdfPath, string $outJpg): bool {
    $gs = trim(@shell_exec('which gs 2>/dev/null'));
    if (!$gs) return false;
    $cmd = sprintf('%s -dNOPAUSE -dBATCH -sDEVICE=jpeg -dFirstPage=1 -dLastPage=1 -r150 -dJPEGQ=85 -sOutputFile=%s %s 2>/dev/null',
        escapeshellcmd($gs), escapeshellarg($outJpg), escapeshellarg($pdfPath));
    exec($cmd, $out, $code);
    return $code === 0 && file_exists($outJpg) && filesize($outJpg) > 0;
}

/* ── Helper: recent uploads ───────────────────────────────────────────────── */
function getRecentUploads(PDO $pdo, int $limit = 10): array {
    $unions = [];
    foreach (array_keys(CATEGORIES) as $table) {
        [$folder, , $catEn] = CATEGORIES[$table];
        // Each SELECT must be wrapped in () when using ORDER BY/LIMIT inside a UNION derived table
        $unions[] = "(SELECT id, '{$table}' AS tbl, '{$folder}' AS folder, '{$catEn}' AS cat_en,
                             The_Title_of_Paper_Book AS title, The_number_of_the_Author AS author
                      FROM `{$table}` ORDER BY id DESC LIMIT {$limit})";
    }
    $sql = "SELECT * FROM (" . implode(" UNION ALL ", $unions) . ") t ORDER BY id DESC LIMIT {$limit}";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/* ── Next IDs for JS ──────────────────────────────────────────────────────── */
$nextIds = [];
foreach (array_keys(CATEGORIES) as $key) {
    $nextIds[$key] = nextId($pdo, $key);
}

/* ── Handle AJAX POST ─────────────────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    $tableKey = $_POST['category']  ?? '';
    $title    = trim($_POST['title']    ?? '');
    $author   = trim($_POST['author']   ?? '');
    $year     = trim($_POST['year']     ?? '');
    $place    = trim($_POST['place']    ?? '');
    $field    = trim($_POST['field']    ?? '');
    $keywords = trim($_POST['keywords'] ?? '');

    if (!array_key_exists($tableKey, CATEGORIES)) {
        echo json_encode(['ok'=>false,'msg'=>'يرجى اختيار تصنيف / Please select a category.']); exit;
    }
    if (empty($title))  { echo json_encode(['ok'=>false,'msg'=>'العنوان مطلوب / Title is required.']); exit; }
    if (empty($author)) { echo json_encode(['ok'=>false,'msg'=>'المؤلف مطلوب / Author is required.']); exit; }
    if (empty($_FILES['pdf']['name']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['ok'=>false,'msg'=>'يرجى رفع ملف PDF / Please upload a PDF.']); exit;
    }
    if (strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION)) !== 'pdf') {
        echo json_encode(['ok'=>false,'msg'=>'الملف يجب أن يكون PDF / File must be PDF.']); exit;
    }

    [$catFolder, $catAr, $catEn] = CATEGORIES[$tableKey];
    $pdo->beginTransaction();
    $tmpPdf = $tmpJpg = null;

    try {
        $newId  = nextId($pdo, $tableKey);
        $tmpPdf = sys_get_temp_dir() . "/{$newId}_upload.pdf";
        $tmpJpg = sys_get_temp_dir() . "/{$newId}_cover.jpg";

        if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $tmpPdf)) {
            throw new Exception('Could not save PDF / تعذّر حفظ الملف');
        }

        // Cover
        $hasCover = false;
        if (!empty($_FILES['cover']['name']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png'])) throw new Exception('Cover must be JPG or PNG.');
            move_uploaded_file($_FILES['cover']['tmp_name'], $tmpJpg);
            $hasCover = true;
        } else {
            $hasCover = generateCoverFromPdf($tmpPdf, $tmpJpg);
        }

        $mediaBase = getenv('MEDIA_BASE_URL');
        $gcsBase   = 'https://storage.googleapis.com/akaroon-media';

        if ($mediaBase) {
            if (!uploadToGcs($tmpPdf, "files/{$catFolder}/files/{$newId}.pdf", 'application/pdf')) {
                throw new Exception('GCS PDF upload failed / فشل رفع PDF إلى GCS');
            }
            if ($hasCover) uploadToGcs($tmpJpg, "files/{$catFolder}/image/{$newId}.jpg", 'image/jpeg');
            $pdfUrl = "{$gcsBase}/files/{$catFolder}/files/{$newId}.pdf";
        } else {
            $pdfDir = __DIR__ . "/../files/{$catFolder}/files";
            $imgDir = __DIR__ . "/../files/{$catFolder}/image";
            @mkdir($pdfDir, 0755, true);
            @mkdir($imgDir, 0755, true);
            copy($tmpPdf, "{$pdfDir}/{$newId}.pdf");
            if ($hasCover) copy($tmpJpg, "{$imgDir}/{$newId}.jpg");
            $pdfUrl = "/files/{$catFolder}/files/{$newId}.pdf";
        }

        @unlink($tmpPdf); @unlink($tmpJpg);

        // status = 0 (published — fetch_data.php queries WHERE status=0)
        $pdo->prepare("
            INSERT INTO `{$tableKey}`
                (id, image, Category, The_Title_of_Paper_Book,
                 The_number_of_the_Author, Year_of_issue,
                 Place_of_issue, Field_of_research, Key_words, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
        ")->execute([$newId, $hasCover ? "{$newId}.jpg" : null, $catFolder,
                     $title, $author, $year, $place, $field, $keywords]);

        $pdo->commit();
        echo json_encode(['ok'=>true,'id'=>$newId,'title'=>$title,'catAr'=>$catAr,'catEn'=>$catEn,
                          'pdfUrl'=>$pdfUrl,'searchUrl'=>"../files/{$catFolder}/search.php",
                          'nextId'=>$newId+1,'tableKey'=>$tableKey]);
    } catch (Exception $e) {
        $pdo->rollBack();
        if ($tmpPdf) @unlink($tmpPdf);
        if ($tmpJpg) @unlink($tmpJpg);
        echo json_encode(['ok'=>false,'msg'=>'خطأ / Error: '.$e->getMessage()]);
    }
    exit;
}

$recentUploads = getRecentUploads($pdo, 10);

// ─────────────────────────────────────────────────────────────────────────────
// Login page function
// ─────────────────────────────────────────────────────────────────────────────
function showLoginPage(string $error = ''): void {
    ?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>أكارون — تسجيل الدخول | Akaroon Admin Login</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: #0e0e0e; color: #eee; font-family: system-ui, sans-serif;
           display: flex; align-items: center; justify-content: center;
           min-height: 100vh; }
    .login-box { background: rgba(255,255,255,.04); border: 1px solid #2a2a2a;
                 border-radius: 14px; padding: 2.5rem 2.2rem; width: 360px;
                 box-shadow: 0 8px 32px rgba(0,0,0,.5); }
    .login-logo { color: #c6a87c; font-size: 1.5rem; font-weight: 700;
                  text-align: center; margin-bottom: 1.8rem; letter-spacing: .03em; }
    .login-logo small { display: block; color: #666; font-size: .8rem; font-weight: 400; margin-top: .3rem; }
    label { display: block; color: #c6a87c; font-size: .85rem; font-weight: 600; margin-bottom: .35rem; }
    input[type=text], input[type=password] {
      width: 100%; padding: .65rem .9rem; background: #141414;
      border: 1px solid #2a2a2a; border-radius: 8px; color: #eee;
      font-size: .95rem; outline: none; transition: border-color .2s; }
    input:focus { border-color: #c6a87c; }
    .field { margin-bottom: 1.1rem; }
    .btn-login { width: 100%; padding: .75rem; background: #c6a87c; color: #000;
                 font-weight: 700; font-size: 1rem; border: none; border-radius: 8px;
                 cursor: pointer; margin-top: .4rem; transition: opacity .2s; }
    .btn-login:hover { opacity: .88; }
    .error { background: rgba(220,53,69,.12); border: 1px solid #dc3545;
             border-radius: 8px; padding: .7rem 1rem; margin-bottom: 1.2rem;
             color: #ef9a9a; font-size: .88rem; }
  </style>
</head>
<body>
  <div class="login-box">
    <div class="login-logo">
      أكارون | Akaroon
      <small>Admin Upload Portal</small>
    </div>
    <?php if ($error): ?>
    <div class="error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <input type="hidden" name="_login" value="1">
      <div class="field">
        <label for="username">اسم المستخدم / Username</label>
        <input type="text" id="username" name="username" autocomplete="username" autofocus
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>
      <div class="field">
        <label for="password">كلمة المرور / Password</label>
        <input type="password" id="password" name="password" autocomplete="current-password">
      </div>
      <button type="submit" class="btn-login">🔐 دخول / Login</button>
    </form>
  </div>
</body>
</html>
    <?php
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>أكارون — رفع وثيقة | Akaroon Upload</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="../css/akaroon-theme.css?v=<?= filemtime(__DIR__ . '/../css/akaroon-theme.css') ?>">
  <style>
    body { background: var(--black-bg); color: var(--text-light); }

    .ak-admin-nav { display:flex; align-items:center; justify-content:space-between;
      padding:.8rem 2rem; background:rgba(0,0,0,.6); border-bottom:1px solid var(--border-dark); }
    .ak-admin-nav .brand { color:var(--gold); font-weight:700; font-size:1.2rem; text-decoration:none; }
    .ak-admin-nav .user-badge { font-size:.85rem; color:#aaa; }
    .ak-admin-nav .user-badge strong { color:var(--gold); }

    .upload-card { max-width:860px; margin:2.5rem auto 1.5rem;
      background:rgba(255,255,255,.04); border:1px solid var(--border-dark);
      border-radius:14px; padding:2.2rem 2.5rem; }
    .upload-card h2 { color:var(--gold); border-bottom:1px solid var(--border-dark);
      padding-bottom:.9rem; margin-bottom:1.8rem; font-size:1.4rem; }

    .field-label { display:block; color:var(--gold); font-weight:600;
      margin-bottom:.3rem; font-size:.92rem; }
    .field-label .en { color:#888; font-weight:400; font-size:.82rem; margin-right:.4rem; }

    .form-control, .form-select { background:#141414; border:1px solid var(--border-dark);
      color:#eee; border-radius:8px; }
    .form-control:focus, .form-select:focus { background:#1c1c1c; border-color:var(--gold);
      box-shadow:0 0 0 .2rem rgba(198,168,124,.2); color:#fff; }
    .form-select option { background:#1c1c1c; }

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

    .id-badge { display:inline-block; background:rgba(198,168,124,.12);
      border:1px solid var(--gold); color:var(--gold); border-radius:6px;
      padding:.15rem .65rem; font-size:.85rem; font-weight:600; }

    .btn-upload { background:var(--gold); color:#000; font-weight:700;
      border:none; border-radius:8px; padding:.75rem 2rem;
      font-size:1rem; width:100%; transition:opacity .2s; }
    .btn-upload:hover { opacity:.88; }
    .btn-upload:disabled { opacity:.45; cursor:not-allowed; }

    #progressWrap { display:none; margin-top:1rem; }
    #progressWrap .prog-label { font-size:.85rem; color:#aaa; margin-bottom:.4rem; }
    .progress { height:10px; background:#1e1e1e; border-radius:6px; overflow:hidden; }
    .progress-bar { background:var(--gold); transition:width .2s ease; }

    #resultBox { display:none; margin-top:1rem; }
    .success-box { background:rgba(76,175,80,.1); border:1px solid #4caf50;
      border-radius:10px; padding:1.2rem 1.5rem; }
    .error-box { background:rgba(220,53,69,.1); border:1px solid #dc3545;
      border-radius:10px; padding:1.2rem 1.5rem; }
    .doc-link { color:#81c784; text-decoration:underline; }
    .gcs-link { color:#90caf9; text-decoration:underline; word-break:break-all; font-size:.85rem; }

    .section-title { font-size:.82rem; text-transform:uppercase; letter-spacing:.08em;
      color:#666; margin:1.4rem 0 .8rem; border-top:1px solid var(--border-dark); padding-top:1rem; }

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

<nav class="ak-admin-nav">
  <a href="../" class="brand">أكارون | Akaroon</a>
  <span class="user-badge">
    مرحباً / Welcome, <strong><?= htmlspecialchars($loggedInUser) ?></strong> &nbsp;·&nbsp;
    <a href="?logout=1" style="color:#888;font-size:.8rem;">تسجيل خروج / Logout</a>
  </span>
</nav>

<div class="container">
  <div class="upload-card">
    <h2>📤 رفع وثيقة جديدة <span style="font-size:.85rem;font-weight:400;color:#888">/ Add New Document</span></h2>

    <div id="resultBox"></div>

    <form id="uploadForm" enctype="multipart/form-data">

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

      <div class="mb-3" id="idRow" style="display:none">
        <span class="field-label">المعرّف التالي <span class="en">/ Next ID (auto-assigned)</span></span>
        <span class="id-badge" id="idBadge">#—</span>
      </div>

      <div class="mb-3">
        <label class="field-label" for="title">عنوان البحث / الكتاب <span class="en">/ Title *</span></label>
        <input type="text" name="title" id="title" class="form-control" required
          placeholder="مثال: الوسطية التشريعية الإسلامية">
      </div>

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

      <div class="mb-3">
        <label class="field-label" for="keywords">الكلمات المفتاحية <span class="en">/ Keywords</span></label>
        <input type="text" name="keywords" id="keywords" class="form-control"
          placeholder="مثال: فقه، شريعة، تشريع (مفصولة بفاصلة)">
      </div>

      <p class="section-title">الملفات / Files</p>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="field-label mb-2">ملف PDF <span class="en">/ PDF File *</span></label>
          <div class="drop-zone" id="pdfZone" onclick="document.getElementById('pdfInput').click()">
            <span class="dz-icon">📄</span>
            <input type="file" name="pdf" id="pdfInput" accept="application/pdf" required>
            <div class="dz-label" id="pdfLabel">اضغط لاختيار ملف PDF<br><small>Click to choose PDF</small></div>
          </div>
        </div>
        <div class="col-md-6">
          <label class="field-label mb-2">
            صورة الغلاف <span class="en">/ Cover Image</span>
            <span style="color:#555;font-size:.75rem"> (اختياري / optional)</span>
          </label>
          <div class="drop-zone" id="coverZone" onclick="document.getElementById('coverInput').click()">
            <span class="dz-icon">🖼️</span>
            <input type="file" name="cover" id="coverInput" accept="image/jpeg,image/png">
            <div class="dz-label" id="coverLabel">اضغط لاختيار صورة<br><small>Click to choose image</small></div>
            <div class="dz-sublabel">إذا تركت فارغاً سيتم توليد الغلاف تلقائياً<br>
              <em>Leave blank — auto-generated from PDF page 1</em></div>
          </div>
        </div>
      </div>

      <div id="progressWrap">
        <div class="prog-label">
          <span id="progText">جارٍ الرفع... / Uploading...</span>
          <span id="progPct" style="float:left;color:var(--gold);font-weight:600"></span>
        </div>
        <div class="progress">
          <div class="progress-bar" id="progressBar" role="progressbar" style="width:0%"></div>
        </div>
      </div>

      <button type="submit" class="btn-upload mt-3" id="submitBtn">
        🚀 &nbsp; رفع وإضافة إلى المكتبة / Upload &amp; Add to Library
      </button>
      <p class="mt-2 text-center" style="font-size:.78rem;color:#555">
        سيتم تخصيص رقم معرّف تلقائي وتسمية الملفات وحفظها في المكان الصحيح
        / ID assigned automatically, files named and stored correctly
      </p>
    </form>
  </div>

  <div class="recent-card">
    <h3>🕒 آخر الوثائق المضافة <span style="font-size:.85rem;color:#555">/ Recently Added (last 10)</span></h3>
    <?php if (empty($recentUploads)): ?>
      <p style="color:#555;font-size:.88rem">لا توجد وثائق / No documents yet.</p>
    <?php else: ?>
    <table class="recent-table" id="recentTable">
      <thead><tr><th>#</th><th>العنوان / Title</th><th>المؤلف / Author</th><th>التصنيف</th><th>PDF</th></tr></thead>
      <tbody id="recentBody">
        <?php foreach ($recentUploads as $r):
          $gcsUrl = 'https://storage.googleapis.com/akaroon-media/files/'
                  . rawurlencode($r['folder']) . '/files/' . $r['id'] . '.pdf';
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const categorySelect = document.getElementById('categorySelect');
const idRow  = document.getElementById('idRow');
const idBadge = document.getElementById('idBadge');

categorySelect.addEventListener('change', function () {
  const opt = this.options[this.selectedIndex];
  if (this.value) { idBadge.textContent = '#' + opt.dataset.next; idRow.style.display = 'block'; }
  else { idRow.style.display = 'none'; }
});

function wireDropZone(inputId, zoneId, labelId, defaultHtml) {
  const input = document.getElementById(inputId);
  const zone  = document.getElementById(zoneId);
  const label = document.getElementById(labelId);
  input.addEventListener('change', function () {
    if (this.files.length) { label.textContent = '✓ ' + this.files[0].name; zone.classList.add('has-file'); }
    else { label.innerHTML = defaultHtml; zone.classList.remove('has-file'); }
  });
}
wireDropZone('pdfInput',   'pdfZone',   'pdfLabel',   'اضغط لاختيار ملف PDF<br><small>Click to choose PDF</small>');
wireDropZone('coverInput', 'coverZone', 'coverLabel', 'اضغط لاختيار صورة<br><small>Click to choose image</small>');

document.getElementById('uploadForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const submitBtn = document.getElementById('submitBtn');
  const progWrap  = document.getElementById('progressWrap');
  const progBar   = document.getElementById('progressBar');
  const progPct   = document.getElementById('progPct');
  const progText  = document.getElementById('progText');
  const resultBox = document.getElementById('resultBox');

  resultBox.style.display = 'none'; resultBox.innerHTML = '';
  submitBtn.disabled = true;
  submitBtn.textContent = '⏳ جارٍ الرفع... / Uploading...';
  progWrap.style.display = 'block';
  progBar.style.width = '0%'; progPct.textContent = '0%';

  const xhr = new XMLHttpRequest();
  xhr.upload.addEventListener('progress', function (ev) {
    if (ev.lengthComputable) {
      const pct = Math.round(ev.loaded / ev.total * 100);
      progBar.style.width = pct + '%'; progPct.textContent = pct + '%';
      if (pct >= 100) progText.textContent = 'جارٍ المعالجة... / Processing...';
    }
  });
  xhr.addEventListener('load', function () {
    progWrap.style.display = 'none';
    submitBtn.disabled = false;
    submitBtn.textContent = '🚀   رفع وإضافة إلى المكتبة / Upload & Add to Library';
    let res;
    try { res = JSON.parse(xhr.responseText); } catch(err) { showError('Unexpected server response.'); return; }
    if (res.ok) {
      showSuccess(res); updateNextId(res.tableKey, res.nextId); prependRecent(res);
      document.getElementById('uploadForm').reset();
      ['pdfZone','coverZone'].forEach(id => document.getElementById(id).classList.remove('has-file'));
      document.getElementById('pdfLabel').innerHTML = 'اضغط لاختيار ملف PDF<br><small>Click to choose PDF</small>';
      document.getElementById('coverLabel').innerHTML = 'اضغط لاختيار صورة<br><small>Click to choose image</small>';
      idRow.style.display = 'none';
    } else { showError(res.msg || 'Unknown error.'); }
  });
  xhr.addEventListener('error', function () {
    progWrap.style.display = 'none'; submitBtn.disabled = false;
    submitBtn.textContent = '🚀   رفع وإضافة إلى المكتبة / Upload & Add to Library';
    showError('Network error / فشل الاتصال');
  });
  xhr.open('POST', '?ajax=1');
  xhr.send(new FormData(this));
});

function showSuccess(res) {
  const b = document.getElementById('resultBox');
  b.style.display = 'block';
  b.innerHTML = `<div class="success-box">
    <strong style="color:#81c784">✓ تمت الإضافة — ID #${res.id}: ${escHtml(res.title)}</strong>
    <div class="mt-2" style="font-size:.83rem">
      📄 <a class="gcs-link" href="${escHtml(res.pdfUrl)}" target="_blank">${escHtml(res.pdfUrl)}</a>
    </div>
    <div class="mt-2" style="font-size:.83rem">
      <a class="doc-link" href="${escHtml(res.searchUrl)}" target="_blank">عرض الفئة ↗</a>
    </div></div>`;
  b.scrollIntoView({ behavior:'smooth', block:'nearest' });
}

function showError(msg) {
  const b = document.getElementById('resultBox');
  b.style.display = 'block';
  b.innerHTML = `<div class="error-box"><strong style="color:#ef9a9a">✗ ${escHtml(msg)}</strong></div>`;
  b.scrollIntoView({ behavior:'smooth', block:'nearest' });
}

function updateNextId(tableKey, nextId) {
  document.querySelectorAll('#categorySelect option').forEach(opt => {
    if (opt.value === tableKey) opt.dataset.next = nextId;
  });
  if (categorySelect.value === tableKey) idBadge.textContent = '#' + nextId;
}

function prependRecent(res) {
  const tbody = document.getElementById('recentBody');
  if (!tbody) return;
  const tr = document.createElement('tr');
  tr.innerHTML = `<td class="id-num">${res.id}</td><td>${escHtml(res.title.substring(0,55))}</td>
    <td>—</td><td><span class="cat-badge">${escHtml(res.catEn)}</span></td>
    <td><a href="${escHtml(res.pdfUrl)}" target="_blank">PDF ↗</a></td>`;
  tbody.insertBefore(tr, tbody.firstChild);
  while (tbody.rows.length > 10) tbody.deleteRow(tbody.rows.length - 1);
}

function escHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
</body>
</html>
