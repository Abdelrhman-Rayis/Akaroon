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

/* ── OCR snippet extractor for عميق mode ─────────────────── */
function makeSnippet(string $ocr, string $term, int $radius = 160): string {
    if (empty($ocr) || empty($term)) return '';
    $sepPos = mb_strpos($ocr, "\n---\n", 0, 'UTF-8');
    $text   = ($sepPos !== false)
            ? trim(mb_substr($ocr, $sepPos + 5, null, 'UTF-8'))
            : $ocr;
    if (empty($text)) $text = $ocr;
    $text = trim(preg_replace('/\s+/u', ' ', $text));
    $pos = mb_stripos($text, $term, 0, 'UTF-8');
    if ($pos === false) {
        return mb_substr($text, 0, $radius * 2, 'UTF-8') . '…';
    }
    $termLen = mb_strlen($term, 'UTF-8');
    $total   = mb_strlen($text, 'UTF-8');
    $start   = max(0, $pos - $radius);
    $end     = min($total, $pos + $termLen + $radius);
    $snippet = ($start > 0 ? '…' : '')
             . mb_substr($text, $start, $end - $start, 'UTF-8')
             . ($end < $total ? '…' : '');
    $snippet = htmlspecialchars($snippet, ENT_QUOTES, 'UTF-8');
    $pat     = '/' . preg_quote(htmlspecialchars($term, ENT_QUOTES, 'UTF-8'), '/') . '/ui';
    $snippet = preg_replace($pat, '<mark>$0</mark>', $snippet);
    return $snippet;
}

// Search mode: normal | semantic (default) | deep
$mode = $_GET['mode'] ?? 'semantic';

if (isset($_GET['search_btn'])) {
    $search_term    = substr(trim($_GET['search'] ?? ''), 0, 200);
    $norm           = normalizeAr($search_term);
    $expanded_terms = [];

    if ($mode === 'deep') {
        // Full-text search inside OCR text (metadata header + full document body)
        $ft    = $link->real_escape_string($norm);
        $where = "ocr_text IS NOT NULL AND MATCH(ocr_text) AGAINST ('$ft' IN BOOLEAN MODE)";
        $score = "MATCH(ocr_text) AGAINST ('$ft' IN BOOLEAN MODE)";
    } else {
        $expanded_terms = ($mode === 'semantic') ? expandQuery($norm) : [$norm];
        $nc             = normConcat();
        $normTitle      = sqlNorm('The_Title_of_Paper_Book');
        $normKw         = sqlNorm('Key_words');
        $where          = buildLikeClause($nc, $expanded_terms, $link, 'mysqli');
        $score          = buildRelevanceScore($expanded_terms, $link, 'mysqli', $nc, $normTitle, $normKw);
    }

    $tables = ['edu','soc','tas','pol','org','state','philo'];
    $parts  = array_map(fn($t) => "(SELECT *, {$score} AS _rel FROM `$t` WHERE $where)", $tables);
    $sql    = "SELECT * FROM (" . implode(" UNION ALL ", $parts) . ") AS _u ORDER BY _rel DESC";

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
        <div class="ak-mode-seg">
          <button type="button" class="ak-mode-btn <?= $mode === 'normal'   ? 'ak-mode-active' : '' ?>" data-mode="normal">🔤 عادي</button>
          <button type="button" class="ak-mode-btn <?= $mode === 'semantic' ? 'ak-mode-active' : '' ?>" data-mode="semantic">🧠 دلالي</button>
          <button type="button" class="ak-mode-btn <?= $mode === 'deep'     ? 'ak-mode-active' : '' ?>" data-mode="deep">🔬 عميق</button>
        </div>
        <input type="hidden" name="mode" id="mode_val" value="<?= htmlspecialchars($mode, ENT_QUOTES, 'UTF-8') ?>">
        <span class="ak-info-icon" tabindex="0" data-tip="🔤 عادي: بحث مباشر بالنص • 🧠 دلالي: يوسّع بالجذور والمرادفات واللهجة السودانية • 🔬 عميق: يبحث داخل نص الوثيقة كاملاً عبر OCR">i</span>
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
  <form action="" method="get">
    <div class="ak-search-box">
      <div class="ak-search-box-row">
        <button type="submit" name="search_btn">بحث</button>
        <input type="text" name="search" value="<?= htmlspecialchars($search_term, ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
      </div>
      <div class="ak-search-toggle-row">
        <div class="ak-mode-seg">
          <button type="button" class="ak-mode-btn <?= $mode === 'normal'   ? 'ak-mode-active' : '' ?>" data-mode="normal">🔤 عادي</button>
          <button type="button" class="ak-mode-btn <?= $mode === 'semantic' ? 'ak-mode-active' : '' ?>" data-mode="semantic">🧠 دلالي</button>
          <button type="button" class="ak-mode-btn <?= $mode === 'deep'     ? 'ak-mode-active' : '' ?>" data-mode="deep">🔬 عميق</button>
        </div>
        <input type="hidden" name="mode" id="mode_val" value="<?= htmlspecialchars($mode, ENT_QUOTES, 'UTF-8') ?>">
        <span class="ak-info-icon" tabindex="0" data-tip="🔤 عادي: بحث مباشر • 🧠 دلالي: يوسّع بالجذور والمرادفات • 🔬 عميق: يبحث داخل نص الوثيقة كاملاً">i</span>
      </div>
    </div>
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
            <?php if ($mode === 'deep' && !empty($row['ocr_text'])): ?>
              <?php $_ocr_dl = $_media_base ? "{$_media_base}/ocr/{$row['Category']}/{$id}.md" : ''; ?>
              <div class="ak-ocr-snippet">
                <div class="ak-ocr-header">
                  <span class="ak-ocr-label">🔬 من نص الوثيقة</span>
                  <?php if ($_ocr_dl): ?>
                    <a href="<?= htmlspecialchars($_ocr_dl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="ak-ocr-dl" title="تحميل ملف النص">⬇ النص الكامل</a>
                  <?php endif; ?>
                </div>
                <?= makeSnippet($row['ocr_text'], $norm) ?>
              </div>
            <?php endif; ?>
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
  // 3-way mode segmented control — updates hidden input before form submit
  var modeVal = document.getElementById('mode_val');
  document.querySelectorAll('.ak-mode-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.ak-mode-btn').forEach(function(b) { b.classList.remove('ak-mode-active'); });
      this.classList.add('ak-mode-active');
      if (modeVal) modeVal.value = this.getAttribute('data-mode');
    });
  });
})();
(function() {
  var popup = null;
  var icons = document.querySelectorAll('.ak-info-icon');
  function showTip(icon) {
    hideTip();
    popup = document.createElement('div');
    popup.className = 'ak-tooltip-popup';
    popup.textContent = icon.getAttribute('data-tip');
    document.body.appendChild(popup);
    var r    = icon.getBoundingClientRect();
    var top  = r.bottom + window.scrollY + 8;
    var left = r.left + window.scrollX + r.width / 2 - popup.offsetWidth / 2;
    left = Math.max(8, Math.min(left, window.innerWidth - popup.offsetWidth - 8));
    popup.style.top  = top + 'px';
    popup.style.left = left + 'px';
  }
  function hideTip() { if (popup) { popup.remove(); popup = null; } }
  icons.forEach(function(icon) {
    icon.addEventListener('mouseenter', function() { showTip(icon); });
    icon.addEventListener('mouseleave', hideTip);
    icon.addEventListener('focus',      function() { showTip(icon); });
    icon.addEventListener('blur',       hideTip);
  });
})();
</script>
</body>
</html>
