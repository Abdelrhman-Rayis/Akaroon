<?php
/**
 * build_qabas_lookup.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Converts Qabas Arabic Lexicographic Database into a PHP root-based lookup
 * array used by Akaroon's search expansion feature.
 *
 * Usage (run from project root):
 *   php tools/build_qabas_lookup.php
 *
 * Or with explicit paths (used by Dockerfile):
 *   php tools/build_qabas_lookup.php /path/to/Qabas-dataset.csv /path/to/output.php
 *
 * Input:  Qabas/Qabas-dataset.csv
 * Output: public_html/lib/qabas_lookup.php   ← gitignored — do NOT commit
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * Qabas Arabic Lexicographic Database (CC-BY-ND-4.0)
 * © 2024 SinaLab, Birzeit University — https://sina.birzeit.edu/qabas
 *
 * Cite:
 *   [1] Jarrar & Hammouda, LREC-COLING 2024, pages 13363-13370
 *   [2] Jarrar & Amayreh, NLDB 2019, LNCS 11608, Springer
 *   [3] Jarrar, Applied Ontology Journal, 16:1, 1-26, IOS Press, 2021
 * ─────────────────────────────────────────────────────────────────────────────
 */

$csvPath = $argv[1] ?? __DIR__ . '/../Qabas/Qabas-dataset.csv';
$outPath = $argv[2] ?? __DIR__ . '/../public_html/lib/qabas_lookup.php';

if (!file_exists($csvPath)) {
    fwrite(STDERR, "ERROR: CSV not found at: $csvPath\n");
    fwrite(STDERR, "Place Qabas-dataset.csv in the Qabas/ folder and retry.\n");
    exit(1);
}

/* ── Same normalisation as search_expand.php ────────────────────────────── */
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

function normalizeRoot(string $root): string {
    // Roots are stored as space-separated consonants: "ع ل م" → "علم"
    return normalizeAr(str_replace(' ', '', $root));
}

/* ── Parse Qabas-dataset.csv ─────────────────────────────────────────────── */
$rootToLemmas = [];   // normalized_root  => [normalized_lemma, ...]
$lemmaToRoot  = [];   // normalized_lemma => normalized_root

$fh = fopen($csvPath, 'r');
$header = fgetcsv($fh);

$lemmaIdx = array_search('lemma', $header);
$rootIdx  = array_search('root',  $header);

if ($lemmaIdx === false || $rootIdx === false) {
    fwrite(STDERR, "ERROR: expected 'lemma' and 'root' columns in CSV header.\n");
    fwrite(STDERR, "Header found: " . implode(', ', $header) . "\n");
    exit(1);
}

$rows    = 0;
$skipped = 0;

while (($row = fgetcsv($fh)) !== false) {
    $rows++;
    $lemmaRaw = trim($row[$lemmaIdx] ?? '');
    $rootRaw  = trim($row[$rootIdx]  ?? '');

    // Skip rows with no root (function words, foreign loanwords, etc.)
    if ($lemmaRaw === '' || $rootRaw === '') { $skipped++; continue; }

    // Multiple roots separated by "|" — use only the first
    $firstRoot = explode('|', $rootRaw)[0];
    $rootNorm  = normalizeRoot(trim($firstRoot));
    if (mb_strlen($rootNorm, 'UTF-8') < 2) { $skipped++; continue; }

    // Multiple spellings separated by "|" — index each separately
    $spellings = array_map('trim', explode('|', $lemmaRaw));
    foreach ($spellings as $spelling) {
        $lemNorm = normalizeAr($spelling);
        if (mb_strlen($lemNorm, 'UTF-8') < 2) continue;

        $lemmaToRoot[$lemNorm] = $rootNorm;

        if (!isset($rootToLemmas[$rootNorm])) {
            $rootToLemmas[$rootNorm] = [];
        }
        if (!in_array($lemNorm, $rootToLemmas[$rootNorm], true)) {
            $rootToLemmas[$rootNorm][] = $lemNorm;
        }
    }
}
fclose($fh);

$totalRoots  = count($rootToLemmas);
$totalLemmas = count($lemmaToRoot);

/* ── Write PHP lookup file ─────────────────────────────────────────────── */
$php  = "<?php\n";
$php .= "/**\n";
$php .= " * Qabas root-based lookup — auto-generated, do NOT edit or commit.\n";
$php .= " * Run: php tools/build_qabas_lookup.php\n";
$php .= " *\n";
$php .= " * Source:    Qabas Arabic Lexicographic Database (CC-BY-ND-4.0)\n";
$php .= " *            © 2024 SinaLab, Birzeit University\n";
$php .= " *            https://sina.birzeit.edu/qabas\n";
$php .= " * Generated: " . date('Y-m-d H:i:s') . "\n";
$php .= " * Roots:     $totalRoots  |  Indexed lemmas: $totalLemmas\n";
$php .= " */\n";
$php .= "return [\n";
$php .= "  'lemma_to_root'  => " . var_export($lemmaToRoot,  true) . ",\n";
$php .= "  'root_to_lemmas' => " . var_export($rootToLemmas, true) . ",\n";
$php .= "];\n";

// Ensure output directory exists
$outDir = dirname($outPath);
if (!is_dir($outDir)) { mkdir($outDir, 0755, true); }

file_put_contents($outPath, $php);

echo "✓ Processed $rows rows ($skipped skipped)\n";
echo "✓ Roots: $totalRoots  |  Lemmas: $totalLemmas\n";
echo "✓ Written to: $outPath\n";
