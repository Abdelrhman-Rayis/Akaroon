<?php
$link = new mysqli('mysql','root','root','akaroon_akaroondb');
$link->set_charset("utf8mb4");

if($link->connect_error){
    die("Connection Failed: " . $link->connect_error);
}

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

$results     = [];
$total_rows  = 0;
$search_term = '';

if (isset($_GET['search_btn'])) {
    $search_term = substr(trim($_GET['search'] ?? ''), 0, 200);
    $norm        = normalizeAr($search_term);
    $find        = '%' . $link->real_escape_string($norm) . '%';
    $nc          = normConcat();

    $tables = ['edu','soc','tas','pol','org','state','philo'];
    $parts  = array_map(fn($t) => "(SELECT * FROM `$t` WHERE $nc LIKE '$find')", $tables);
    $sql    = implode(" UNION ", $parts);

    if ($res = $link->query($sql)) {
        $results    = $res->fetch_all(MYSQLI_ASSOC);
        $total_rows = count($results);
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>البحث — مكتبة عكارون</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="../css/akaroon-theme.css">
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
      <button type="submit" name="search_btn">بحث</button>
      <input type="text" name="search" placeholder="ابحث بالعنوان أو المؤلف أو الكلمات المفتاحية..." autocomplete="off" required>
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
  <form action="" method="get">
    <button type="submit" name="search_btn">بحث</button>
    <input type="text" name="search" value="<?= htmlspecialchars($search_term, ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
  </form>
  <div class="ak-result-count">
    تم العثور على <strong><?= $total_rows ?></strong> نتيجة
    <?php if ($search_term): ?>لـ "<strong><?= htmlspecialchars($search_term, ENT_QUOTES, 'UTF-8') ?></strong>"<?php endif; ?>
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
        $pdfHref = "../files/{$cat}/files/{$id}.pdf";
        $imgSrc  = "../files/{$cat}/image/{$id}.jpg";
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
</body>
</html>
