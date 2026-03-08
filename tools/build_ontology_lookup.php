<?php
/**
 * build_ontology_lookup.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Converts the Arabic Ontology CSV (Birzeit University) into a PHP synonym
 * lookup array used by Akaroon's search expansion feature.
 *
 * Usage (run once from project root):
 *   php tools/build_ontology_lookup.php
 *
 * Input:  ArabicOntology 2/Concepts.csv
 * Output: public_html/lib/ontology_lookup.php
 *
 * The output file is gitignored — re-run this script after updating the CSV.
 * ─────────────────────────────────────────────────────────────────────────────
 * Arabic Ontology © Birzeit University (Mustafa Jarrar). All rights reserved.
 * Cite: Jarrar, M. "The Arabic Ontology - An Arabic Wordnet with Ontologically
 *       Clean Content." Applied Ontology Journal, 16:1, 1-26. IOS Press. 2021.
 */

$csvPath = __DIR__ . '/../ArabicOntology 2/Concepts.csv';
$outPath = __DIR__ . '/../public_html/lib/ontology_lookup.php';

if (!file_exists($csvPath)) {
    fwrite(STDERR, "ERROR: CSV not found at: $csvPath\n");
    fwrite(STDERR, "Place the Arabic Ontology Concepts.csv in 'ArabicOntology 2/' and retry.\n");
    exit(1);
}

/* ── Same normalisation as the search code ─────────────────────────────── */
function normalizeAr(string $text): string {
    $text = trim($text);
    $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text); // strip diacritics
    $text = str_replace(['أ','إ','آ','ٱ'], 'ا', $text);   // unify alef forms
    $text = str_replace('ة', 'ه', $text);                  // taa marbuta → haa
    $text = str_replace('ى', 'ي', $text);                  // alef maqsura → yaa
    $text = str_replace('ؤ', 'و', $text);                  // hamza on waw
    $text = str_replace('ئ', 'ي', $text);                  // hamza on yaa
    return $text;
}

/* ── Parse CSV ──────────────────────────────────────────────────────────── */
$map    = [];   // normalized_term => [normalized_synonym, ...]
$total  = 0;
$handle = fopen($csvPath, 'r');
fgetcsv($handle);   // skip header row

while (($row = fgetcsv($handle)) !== false) {
    if (count($row) < 2) continue;

    // columns: conceptId, arabicSynset, englishSynset, gloss, example, dataSourceId
    $arabicSynset = $row[1];
    $dataSourceId = isset($row[5]) ? trim($row[5]) : '';

    // Only use dataSourceId=200 — "well-designed and well-studied" (per README)
    // Other source IDs are draft concepts and may contain errors
    if ($dataSourceId !== '200') continue;

    // Split pipe-separated synonyms, normalise each
    $rawTerms  = array_map('trim', explode('|', $arabicSynset));
    $normTerms = array_values(array_unique(
        array_filter(array_map('normalizeAr', $rawTerms), fn($t) => mb_strlen($t, 'UTF-8') > 1)
    ));

    if (count($normTerms) < 2) continue;   // single term — no expansion possible

    // Map every term in the synset to its full synonym set
    foreach ($normTerms as $term) {
        if (!isset($map[$term])) {
            $map[$term] = $normTerms;
        } else {
            $map[$term] = array_values(array_unique(array_merge($map[$term], $normTerms)));
        }
    }
    $total++;
}
fclose($handle);

/* ── Write PHP lookup file ─────────────────────────────────────────────── */
$termCount = count($map);
$php  = "<?php\n";
$php .= "/**\n";
$php .= " * Arabic Ontology synonym lookup — auto-generated, do not edit.\n";
$php .= " * Run: php tools/build_ontology_lookup.php\n";
$php .= " *\n";
$php .= " * Source:    Arabic Ontology © Birzeit University (Mustafa Jarrar)\n";
$php .= " * Generated: " . date('Y-m-d H:i:s') . "\n";
$php .= " * Synsets:   $total  |  Indexed terms: $termCount\n";
$php .= " */\n";
$php .= "return " . var_export($map, true) . ";\n";

file_put_contents($outPath, $php);

echo "✓ Processed $total synsets → $termCount indexed terms\n";
echo "✓ Written to: $outPath\n";
