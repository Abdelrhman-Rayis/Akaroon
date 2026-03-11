<?php

//fetch_data.php

include('database_connection.php');
require_once __DIR__ . '/../../lib/search_expand.php';

// ── GCS media URLs (env-aware: uses GCS in Cloud Run, relative paths locally) ──
$_media_base = rtrim(getenv('MEDIA_BASE_URL') ?: '', '/');
$_category   = basename(__DIR__);  // e.g. 'التأصيل'
$_img_base   = $_media_base ? "{$_media_base}/files/{$_category}/image" : 'image';
$_pdf_base   = $_media_base ? "{$_media_base}/files/{$_category}/files" : 'files';


/* ── Arabic-aware search normalization ─────────────────── */
function normalizeAr($text) {
	$text = trim($text);
	$text = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text); // strip diacritics
	$text = str_replace(['أ','إ','آ','ٱ'], 'ا', $text);  // unify alef
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

if(isset($_POST["action"]))
{
	$query = "
		SELECT * FROM pol WHERE 	status = '0'
	";
	if(isset($_POST["brand"]) && !empty($_POST["brand"]))
	{
		$brand_filter = implode(",", array_map(function($v) use ($connect) { return $connect->quote(strip_tags(substr($v,0,100))); }, $_POST["brand"]));
		$query .= "
		 AND The_number_of_the_Author IN(".$brand_filter.")
		";
	}
	if(isset($_POST["ram"]) && !empty($_POST["ram"]))
	{
		$ram_filter = implode(",", array_map(function($v) use ($connect) { return $connect->quote(strip_tags(substr($v,0,100))); }, $_POST["ram"]));
		$query .= "
		 AND Field_of_research IN(".$ram_filter.")
		";
	}
	if(isset($_POST["storage"]) && !empty($_POST["storage"]))
	{
		$storage_filter = implode(",", array_map(function($v) use ($connect) { return $connect->quote(strip_tags(substr($v,0,100))); }, $_POST["storage"]));
		$query .= "
		 AND Place_of_issue IN(".$storage_filter.")
		";
	}
	if(!empty($_POST["search_text"]))
	{
		$norm        = normalizeAr(strip_tags(substr($_POST["search_text"], 0, 200)));
		$mode = $_POST['mode'] ?? 'semantic';
		if ($mode === 'deep') {
			// Full-text search inside OCR body (metadata header + full document text)
			$ft    = $connect->quote($norm);
			$query .= " AND ocr_text IS NOT NULL AND MATCH(ocr_text) AGAINST ($ft IN BOOLEAN MODE)";
			$score  = "MATCH(ocr_text) AGAINST ($ft IN BOOLEAN MODE)";
		} else {
			$terms     = ($mode === 'semantic') ? expandQuery($norm) : [$norm];
			$nc        = "CONCAT_WS(' ', " . sqlNorm("Category") . ", " . sqlNorm("The_Title_of_Paper_Book") . ", " . sqlNorm("The_number_of_the_Author") . ", " . sqlNorm("Year_of_issue") . ", " . sqlNorm("Place_of_issue") . ", " . sqlNorm("Field_of_research") . ", " . sqlNorm("Key_words") . ")";
			$normTitle = sqlNorm("The_Title_of_Paper_Book");
			$normKw    = sqlNorm("Key_words");
			$query    .= " AND (" . buildLikeClause($nc, $terms, $connect, 'pdo') . ")";
			$score     = buildRelevanceScore($terms, $connect, 'pdo', $nc, $normTitle, $normKw);
		}
	}
	$query .= isset($score) ? " ORDER BY {$score} DESC" : " ORDER BY id ASC";

	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$total_row = $statement->rowCount();
	$output = '';
	if($total_row > 0)
	{
		foreach($result as $row)
		{
			$output .= '
			<div class="col-lg-4 col-md-6 col-sm-12 mb-4">
				<div class="ak-card">
					<div class="ak-card-img-wrap">
						<img src="'. htmlspecialchars($_img_base, ENT_QUOTES, 'UTF-8') .'/'. htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8') .'" class="ak-card-img" alt=""
						     onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">
						<div class="ak-card-img-placeholder" style="display:none;align-items:center;justify-content:center;height:165px;width:100%;font-size:3rem;">📄</div>
					</div>
					<div class="ak-card-body">
						<a href="'. htmlspecialchars($_pdf_base, ENT_QUOTES, 'UTF-8') .'/'. htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') .'.pdf" class="ak-card-title">'. htmlspecialchars($row['The_Title_of_Paper_Book'], ENT_QUOTES, 'UTF-8') .'</a>
						<span class="ak-badge">'. htmlspecialchars($row['Category'], ENT_QUOTES, 'UTF-8') .'</span>
						<div class="ak-card-meta">
							<div>✍️ '. htmlspecialchars($row['The_number_of_the_Author'], ENT_QUOTES, 'UTF-8') .'</div>
							<div>📅 '. htmlspecialchars($row['Year_of_issue'], ENT_QUOTES, 'UTF-8') .'</div>
							<div>🔬 '. htmlspecialchars($row['Field_of_research'], ENT_QUOTES, 'UTF-8') .'</div>
						</div>
					</div>
				</div>
			</div>
			';
		}
	}
	else
	{
		$output = (\$_POST['mode'] ?? '') === 'deep' ? '<div class="ak-empty col-12"><span class="ak-empty-icon">🔬</span><p>لا توجد نتائج في نص الوثائق — قد لا تكون بعض الوثائق مفهرسة بعد</p></div>' : '<div class="ak-empty col-12"><span class="ak-empty-icon">🔍</span><p>لا توجد نتائج</p></div>';
	}
	echo $output;
}

?>
