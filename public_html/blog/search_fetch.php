<?php
/**
 * search_fetch.php — AJAX endpoint for parallel per-category search
 * Called 7 times simultaneously by ibrahimfinalsearch.php JS.
 * Returns JSON: { count, html, expanded }
 */
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['count' => 0, 'html' => '', 'expanded' => []]);
    exit;
}

// ── Category whitelist ────────────────────────────────────────
$TABLE_MAP = [
    'edu'   => ['table' => 'edu',   'label' => 'التعليم',  'folder' => 'التعليم'],
    'soc'   => ['table' => 'soc',   'label' => 'المجتمع',  'folder' => 'المجتمع'],
    'tas'   => ['table' => 'tas',   'label' => 'التأصيل',  'folder' => 'التأصيل'],
    'pol'   => ['table' => 'pol',   'label' => 'السياسة',  'folder' => 'السياسة'],
    'org'   => ['table' => 'org',   'label' => 'منظمات',   'folder' => 'منظمات'],
    'state' => ['table' => 'state', 'label' => 'الدولة',   'folder' => 'الدولة'],
    'philo' => ['table' => 'philo', 'label' => 'الفلسفة',  'folder' => 'الفلسفة'],
];

$cat_key = $_POST['category'] ?? '';
if (!array_key_exists($cat_key, $TABLE_MAP)) {
    http_response_code(400);
    echo json_encode(['count' => 0, 'html' => '', 'expanded' => []]);
    exit;
}
$cat = $TABLE_MAP[$cat_key];

// ── DB connection ─────────────────────────────────────────────
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
$link->set_charset('utf8mb4');

if ($link->connect_error) {
    echo json_encode(['count' => 0, 'html' => '', 'expanded' => [], 'error' => 'db_error']);
    exit;
}

require_once __DIR__ . '/../lib/search_expand.php';

// ── Helpers (mirrors ibrahimfinalsearch.php) ──────────────────
function normalizeAr($text) {
    $text = trim($text);
    $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text);
    $text = str_replace(['أ','إ','آ','ٱ'], 'ا', $text);
    $text = str_replace('ة', 'ه', $text);
    $text = str_replace('ى', 'ي', $text);
    $text = str_replace('ؤ', 'و', $text);
    $text = str_replace('ئ', 'ي', $text);
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

// ── Build query ───────────────────────────────────────────────
$mode        = $_POST['mode']   ?? 'semantic';
$search_term = substr(trim($_POST['search'] ?? ''), 0, 200);
$norm        = normalizeAr($search_term);

if (empty($search_term)) {
    echo json_encode(['count' => 0, 'html' => '', 'expanded' => []]);
    exit;
}

$expanded_terms = [];
$table          = $cat['table'];

if ($mode === 'deep') {
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

// Count total (for "show all" threshold) then fetch top 12
$count_sql = "SELECT COUNT(*) FROM `{$table}` WHERE {$where}";
$total_in_cat = 0;
if ($cres = $link->query($count_sql)) {
    $total_in_cat = (int)$cres->fetch_row()[0];
}

$sql     = "SELECT *, {$score} AS _rel FROM `{$table}` WHERE {$where} ORDER BY _rel DESC LIMIT 12";
$results = [];
if ($res = $link->query($sql)) {
    $results = $res->fetch_all(MYSQLI_ASSOC);
}

// ── Build HTML cards ──────────────────────────────────────────
$_media_base = rtrim(getenv('MEDIA_BASE_URL') ?: '', '/');

ob_start();
foreach ($results as $row) {
    $catName = htmlspecialchars($row['Category'],                 ENT_QUOTES, 'UTF-8');
    $id      = htmlspecialchars($row['id'],                       ENT_QUOTES, 'UTF-8');
    $title   = htmlspecialchars($row['The_Title_of_Paper_Book'],  ENT_QUOTES, 'UTF-8');
    $author  = htmlspecialchars($row['The_number_of_the_Author'], ENT_QUOTES, 'UTF-8');
    $year    = htmlspecialchars($row['Year_of_issue'],            ENT_QUOTES, 'UTF-8');
    $field   = htmlspecialchars($row['Field_of_research'],        ENT_QUOTES, 'UTF-8');
    $pdfHref = $_media_base
        ? "{$_media_base}/files/{$catName}/files/{$id}.pdf"
        : "../files/{$catName}/files/{$id}.pdf";
    $imgSrc  = $_media_base
        ? "{$_media_base}/files/{$catName}/image/{$id}.jpg"
        : "../files/{$catName}/image/{$id}.jpg";
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
          <span class="ak-badge"><?= $catName ?></span>
          <div class="ak-card-meta">
            <div>✍️ <?= $author ?></div>
            <div>📅 <?= $year ?></div>
            <?php if ($field): ?><div>🔬 <?= $field ?></div><?php endif; ?>
          </div>
          <?php if ($mode === 'deep' && !empty($row['ocr_text'])): ?>
            <?php $ocr_dl = $_media_base ? "{$_media_base}/ocr/{$row['Category']}/{$id}.md" : ''; ?>
            <div class="ak-ocr-snippet">
              <div class="ak-ocr-header">
                <span class="ak-ocr-label">🔬 من نص الوثيقة</span>
                <?php if ($ocr_dl): ?>
                  <a href="<?= htmlspecialchars($ocr_dl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="ak-ocr-dl">⬇ النص الكامل</a>
                <?php endif; ?>
              </div>
              <?= makeSnippet($row['ocr_text'], $norm) ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php
}
$html = ob_get_clean();

echo json_encode([
    'count'       => count($results),
    'total_in_cat'=> $total_in_cat,
    'html'        => $html,
    'expanded'    => $expanded_terms,
    'cat_label'   => $cat['label'],
    'cat_folder'  => $cat['folder'],
], JSON_UNESCAPED_UNICODE);
