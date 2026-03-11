<?php
$_db_socket = getenv('DB_SOCKET');
$_db_host   = getenv('DB_HOST')     ?: 'mysql';
$_db_name   = getenv('DB_NAME')     ?: 'akaroon_akaroondb';
$_db_user   = getenv('DB_USER')     ?: 'root';
$_db_pass   = getenv('DB_PASSWORD') ?: 'root';

if ($_db_socket) {
    $link = new mysqli();
    $link->init();
    $link->real_connect(null, $_db_user, $_db_pass, $_db_name, null, $_db_socket);
} else {
    $link = new mysqli($_db_host, $_db_user, $_db_pass, $_db_name);
}
unset($_db_socket, $_db_host, $_db_name, $_db_user, $_db_pass);
$link->set_charset("utf8mb4");

if($link->connect_error){
    die("Connection Failed: " . $link->connect_error);
}

require_once __DIR__ . '/../lib/search_expand.php';

/* ── Arabic-aware search normalization ─────────────────── */
function normalizeAr($text) {
    $text = trim($text);
    $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text); // strip diacritics
    $text = str_replace(['أ','إ','آ','ٱ'], 'ا', $text);  // unify alef forms
    $text = str_replace('ة', 'ه', $text);                 // taa marbuta → haa
    $text = str_replace('ى', 'ي', $text);                 // alef maqsura → yaa
    $text = str_replace('ؤ', 'و', $text);                 // hamza on waw
    $text = str_replace('ئ', 'ي', $text);                 // hamza on yaa
    return $text;
}
function sqlNorm($f) {
    foreach (['ة'=>'ه','أ'=>'ا','إ'=>'ا','آ'=>'ا','ٱ'=>'ا','ى'=>'ي','ؤ'=>'و','ئ'=>'ي'] as $from=>$to)
        $f = "REPLACE($f, '$from', '$to')";
    return $f;
}
function normConcat() {
    $cols = ['Category','The_Title_of_Paper_Book','The_number_of_the_Author',
             'Year_of_issue','Place_of_issue','Field_of_research','Key_words'];
    return "CONCAT_WS(' ', " . implode(', ', array_map('sqlNorm', $cols)) . ")";
}

$results        = [];
$total_rows     = 0;
$search_term    = '';
$expanded_terms = [];

// Semantic toggle: default ON; pass ?semantic=0 to disable
$semantic_on  = ($_GET['semantic'] ?? '1') !== '0';
$toggle_class = $semantic_on ? 'ak-mode-on'  : 'ak-mode-off';
$toggle_label = $semantic_on ? '🧠 دلالي' : '🔤 عادي';
$toggle_val   = $semantic_on ? '1'          : '0';

if (isset($_GET['search_btn'])) {
    $search_term    = substr(trim($_GET['search'] ?? ''), 0, 200);
    $norm           = normalizeAr($search_term);
    $expanded_terms = $semantic_on ? expandQuery($norm) : [$norm];
    $nc             = normConcat();
    $where          = buildLikeClause($nc, $expanded_terms, $link, 'mysqli');

    $tables = ['edu','soc','tas','pol','org','state','philo'];
    $parts  = array_map(fn($t) => "(SELECT * FROM `$t` WHERE $where)", $tables);
    $sql    = implode(" UNION ", $parts);

    if ($res = $link->query($sql)) {
        $results    = $res->fetch_all(MYSQLI_ASSOC);
        $total_rows = count($results);
    }
}
?>
<?php $cssV = @filemtime(__DIR__ . '/../css/akaroon-theme.css') ?: 1; ?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>البحث — مكتبة عكارون</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="../css/akaroon-theme.css?v=<?= $cssV ?>">
</head>
<body>

<!-- ── WordPress blog navigation bar ────────────────────── -->
<div class="ak-blog-bar">
  <span class="ak-blog-bar-label">🌐 الموقع:</span>
  <a href="/">الرئيسية</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="/blog/">المدونة</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="/blog/?page_id=103">التصنيفات</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="/blog/?page_id=7">عن الموقع</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="/blog/?page_id=207">معرض الصور</a>
  <span class="ak-blog-bar-sep">|</span>
  <a href="/blog/?page_id=8">تواصل معنا</a>
</div>

<nav class="ak-navbar">
  <a href="../" class="ak-logo"><span class="ak-logo-name">Akaroon || عكارون</span><span class="ak-logo-desc">by Ibrahim Ahmed Omer</span></a>
  <ul class="ak-nav-links">
    <li><a href="../files/التأصيل/search.php">التأصيل</a></li>
    <li><a href="../files/التعليم/search.php">التعليم</a></li>
    <li><a href="../files/الدولة/search.php">الدولة</a></li>
    <li><a href="../files/السياسة/search.php">السياسة</a></li>
    <li><a href="../files/الفلسفة/search.php">الفلسفة</a></li>
    <li><a href="../files/المجتمع/search.php">المجتمع</a></li>
    <li><a href="../files/منظمات/search.php">منظمات</a></li>
  </ul>
</nav>

<?php if (!isset($_GET['search_btn'])): ?>

<section class="ak-hero">
  <h1>مكتبة عكارون البحثية</h1>
  <p>ابحث في آلاف الأوراق البحثية والكتب عبر جميع التصنيفات</p>
  <form action="" method="get">
    <div class="ak-search-box">
      <div class="ak-search-box-row">
        <button type="submit" name="search_btn">بحث</button>
        <input type="text" name="search" placeholder="ابحث بالعنوان أو المؤلف أو الكلمات المفتاحية..." autocomplete="off" required>
      </div>
      <div class="ak-search-toggle-row">
        <label class="ak-switch" for="semantic_toggle_cb">
          <input type="checkbox" class="ak-switch-input" id="semantic_toggle_cb" <?= $semantic_on ? 'checked' : '' ?>>
          <span class="ak-switch-track"><span class="ak-switch-thumb"></span></span>
          <span class="ak-switch-label"><?= $toggle_label ?></span>
        </label>
        <span class="ak-info-icon" tabindex="0" data-tip="🧠 دلالي: يوسّع البحث تلقائياً ليشمل الجذور اللغوية والمرادفات واللهجة السودانية • 🔤 عادي: بحث نصي مباشر بالكلمة المُدخَلة فقط">i</span>
        <input type="hidden" name="semantic" id="semantic_val" value="<?= $toggle_val ?>">
      </div>
    </div>
  </form>
</section>

<div class="container py-5 text-center" style="color:var(--muted);">
  <span style="font-size:3.5rem;">📚</span>
  <p class="mt-3" style="font-size:1.05rem;">اكتب كلمة البحث أعلاه للعثور على الأوراق والكتب</p>
  <a href="../" class="mt-2 d-inline-block" style="color:var(--gold);">أو تصفح التصنيفات ←</a>
</div>

<?php else: ?>

<div class="ak-results-header">
  <form action="" method="get" style="display:flex;align-items:center;gap:0.5rem;flex:1;flex-wrap:wrap;">
    <button type="submit" name="search_btn">بحث</button>
    <input type="text" name="search" value="<?= htmlspecialchars($search_term, ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" style="flex:1;min-width:120px;">
    <label class="ak-switch" for="semantic_toggle_cb" style="margin:0 0.3rem;">
      <input type="checkbox" class="ak-switch-input" id="semantic_toggle_cb" <?= $semantic_on ? 'checked' : '' ?>>
      <span class="ak-switch-track"><span class="ak-switch-thumb"></span></span>
      <span class="ak-switch-label"><?= $toggle_label ?></span>
    </label>
    <span class="ak-info-icon" tabindex="0" data-tip="🧠 دلالي: يوسّع البحث تلقائياً ليشمل الجذور اللغوية والمرادفات واللهجة السودانية • 🔤 عادي: بحث نصي مباشر بالكلمة المُدخَلة فقط">i</span>
        <input type="hidden" name="semantic" id="semantic_val" value="<?= $toggle_val ?>">
  </form>
  <div class="ak-result-count">
    تم العثور على <strong><?= $total_rows ?></strong> نتيجة
    <?php if ($search_term): ?>لـ "<strong><?= htmlspecialchars($search_term, ENT_QUOTES, 'UTF-8') ?></strong>"<?php endif; ?>
    <?php if (count($expanded_terms) > 1): ?>
      <div class="ak-synonyms-hint">
        🔗 بحث موسّع يشمل المترادفات:
        <?php foreach (array_slice($expanded_terms, 1) as $syn): ?>
          <span class="ak-syn-tag"><?= htmlspecialchars($syn, ENT_QUOTES, 'UTF-8') ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<section class="ak-results-section">
  <div class="container">
    <?php if ($total_rows > 0): ?>
    <div class="row g-4">
      <?php foreach ($results as $row):
        $cat     = htmlspecialchars($row['Category'],                 ENT_QUOTES, 'UTF-8');
        $id      = htmlspecialchars($row['id'],                       ENT_QUOTES, 'UTF-8');
        $title   = htmlspecialchars($row['The_Title_of_Paper_Book'],  ENT_QUOTES, 'UTF-8');
        $author  = htmlspecialchars($row['The_number_of_the_Author'], ENT_QUOTES, 'UTF-8');
        $year    = htmlspecialchars($row['Year_of_issue'],            ENT_QUOTES, 'UTF-8');
        $field   = htmlspecialchars($row['Field_of_research'],        ENT_QUOTES, 'UTF-8');
        $_media_base = rtrim(getenv('MEDIA_BASE_URL') ?: '', '/');
        $pdfHref = $_media_base
            ? "{$_media_base}/files/{$cat}/files/{$id}.pdf"
            : "../files/{$cat}/files/{$id}.pdf";
        $imgSrc  = $_media_base
            ? "{$_media_base}/files/{$cat}/image/{$id}.jpg"
            : "../files/{$cat}/image/{$id}.jpg";
      ?>
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="ak-card">
          <div class="ak-card-img-wrap">
            <img src="<?= $imgSrc ?>" class="ak-card-img" alt="<?= $title ?>"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
            <div class="ak-card-img-placeholder" style="display:none;">📄</div>
          </div>
          <div class="ak-card-body">
            <a href="<?= $pdfHref ?>" class="ak-card-title"><?= $title ?></a>
            <span class="ak-badge"><?= $cat ?></span>
            <div class="ak-card-meta">
              <div>✍️ <?= $author ?></div>
              <div>📅 <?= $year ?></div>
              <?php if ($field): ?><div>🔬 <?= $field ?></div><?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="ak-empty">
      <span class="ak-empty-icon">🔍</span>
      <p>لا توجد نتائج لـ "<?= htmlspecialchars($search_term, ENT_QUOTES, 'UTF-8') ?>"</p>
      <p style="font-size:0.9rem; color:var(--muted);">جرب كلمات مختلفة أو <a href="../" style="color:var(--gold);">تصفح التصنيفات</a></p>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php endif; ?>

<footer class="ak-footer">
  <p>© عكارون — جميع الحقوق محفوظة | <a href="../">الصفحة الرئيسية</a></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
  var cb  = document.getElementById('semantic_toggle_cb');
  var val = document.getElementById('semantic_val');
  var lbl = document.querySelector('.ak-switch-label');
  if (!cb) return;
  cb.addEventListener('change', function() {
    var on = this.checked;
    if (val) val.value = on ? '1' : '0';
    if (lbl) lbl.textContent = on ? '🧠 دلالي' : '🔤 عادي';
  });
})();
</script>
</body>
</html>
