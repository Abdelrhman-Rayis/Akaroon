<?php
/**
 * build_lisan_sudanese_lookup.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Converts the Lisan Sudanese Dialect Corpus into a PHP lookup used by
 * Akaroon's search expansion (Layer 3 — dialect bridging).
 *
 * Usage (run from project root):
 *   php tools/build_lisan_sudanese_lookup.php
 *
 * Or with explicit paths (used by Dockerfile):
 *   php tools/build_lisan_sudanese_lookup.php /path/to/Lisan-Sudanese-dataset.csv /path/to/output.php
 *
 * Input:  sudanese/Lisan-Sudanese-dataset.csv
 * Output: public_html/lib/lisan_sudanese_lookup.php  ← gitignored — do NOT commit
 *
 * Two lookup maps are built:
 *
 *   dialect_to_msa  — Sudanese dialect token/stem → MSA lemma
 *     e.g. "ضكور" (Sudanese: "males") → "ذكر" (MSA)
 *     Allows users who type dialect words to match MSA documents.
 *
 *   gloss_to_msa    — English gloss word → MSA lemma
 *     e.g. "education" → "تعليم"
 *     Enables basic English-language entry point for non-Arabic users.
 *
 * When the same token maps to multiple MSA lemmas across sentences, the
 * most-frequently-occurring MSA lemma wins (vote-based deduplication).
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * Lisan Corpus: Iraqi, Yemeni, Sudanese, and Libyan Dialect Corpora (CC-BY-4.0)
 * © SinaLab, Birzeit University — https://sina.birzeit.edu/currasat
 *
 * Cite:
 *   Jarrar et al., "Building Linguistically Motivated Arabic Dialect Corpora",
 *   ACL Anthology, 2023.
 * ─────────────────────────────────────────────────────────────────────────────
 */

$csvPath = $argv[1] ?? __DIR__ . '/../sudanese/Lisan-Sudanese-dataset.csv';
$outPath = $argv[2] ?? __DIR__ . '/../public_html/lib/lisan_sudanese_lookup.php';

if (!file_exists($csvPath)) {
    fwrite(STDERR, "ERROR: CSV not found at: $csvPath\n");
    fwrite(STDERR, "Place Lisan-Sudanese-dataset.csv in the sudanese/ folder and retry.\n");
    exit(1);
}

/* ── Same Arabic normalisation as search_expand.php + Qabas build ─────────── */
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

/**
 * Normalize an MSA lemma: apply Arabic normalisation then strip trailing digits.
 * MSALemma column sometimes appends a disambiguation number, e.g. "ذَكَر1".
 */
function normalizeMSA(string $text): string {
    $text = normalizeAr($text);
    $text = preg_replace('/\d+$/', '', $text); // strip trailing disambiguation digits
    return trim($text);
}

/* ── English gloss stop-words (skip these when parsing gloss phrases) ──────── */
$STOP_WORDS = array_flip([
    'a','an','the','to','of','and','in','on','for','by','with','our','us',
    'their','they','we','he','she','it','is','are','was','were','be','been',
    'from','at','as','or','but','not','no','nor','my','your','his','her','its',
    'me','him','this','that','these','those','who','which','what','when','how',
    'towards','toward','into','onto','upon','about','above','below','between',
]);

/* ── Parse CSV ──────────────────────────────────────────────────────────────── */
$dialectVotes = [];   // normalized_token => [ normalized_msa => count ]
$glossVotes   = [];   // english_word     => [ normalized_msa => count ]

$fh     = fopen($csvPath, 'r');
$header = fgetcsv($fh);
$colIdx = array_flip($header);

// Verify required columns exist
foreach (['rawToken', 'Stem', 'MSALemma', 'Gloss'] as $col) {
    if (!isset($colIdx[$col])) {
        fwrite(STDERR, "ERROR: expected '$col' column in CSV header.\n");
        fwrite(STDERR, "Header found: " . implode(', ', $header) . "\n");
        exit(1);
    }
}

$rows    = 0;
$skipped = 0;

while (($row = fgetcsv($fh)) !== false) {
    $rows++;

    $rawToken = trim($row[$colIdx['rawToken']] ?? '');
    $stem     = trim($row[$colIdx['Stem']]     ?? '');
    $msaLemma = trim($row[$colIdx['MSALemma']] ?? '');
    $gloss    = trim($row[$colIdx['Gloss']]    ?? '');

    // Skip rows with no MSA lemma
    if ($rawToken === '' || $msaLemma === '') { $skipped++; continue; }

    $msaNorm = normalizeMSA($msaLemma);
    if (mb_strlen($msaNorm, 'UTF-8') < 2) { $skipped++; continue; }

    /* ── Vote: rawToken → MSA ─────────────────────────────────────────── */
    $tokenNorm = normalizeAr($rawToken);
    if (mb_strlen($tokenNorm, 'UTF-8') >= 2) {
        $dialectVotes[$tokenNorm][$msaNorm] = ($dialectVotes[$tokenNorm][$msaNorm] ?? 0) + 1;
    }

    /* ── Vote: Stem → MSA (only if different from rawToken) ───────────── */
    if ($stem !== '' && $stem !== $rawToken) {
        $stemNorm = normalizeAr($stem);
        if (mb_strlen($stemNorm, 'UTF-8') >= 2) {
            $dialectVotes[$stemNorm][$msaNorm] = ($dialectVotes[$stemNorm][$msaNorm] ?? 0) + 1;
        }
    }

    /* ── Parse English gloss → MSA ────────────────────────────────────── */
    if ($gloss !== '') {
        // Split on separators: | / + , ; and whitespace
        $parts = preg_split('/[\|\/\+,;\s]+/', $gloss, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($parts as $part) {
            // Keep only ASCII letters, lowercase
            $word = strtolower(preg_replace('/[^a-zA-Z]/', '', $part));
            if (strlen($word) < 3)              continue;
            if (isset($STOP_WORDS[$word]))      continue;
            $glossVotes[$word][$msaNorm] = ($glossVotes[$word][$msaNorm] ?? 0) + 1;
        }
    }
}
fclose($fh);

/* ── Resolve votes → pick most-frequent MSA for each token/word ─────────── */
$dialectToMsa = [];
foreach ($dialectVotes as $token => $msaCounts) {
    arsort($msaCounts);
    $winner = array_key_first($msaCounts);
    // Skip trivial identity mappings (dialect form is already the MSA form)
    if ($token !== $winner) {
        $dialectToMsa[$token] = $winner;
    }
}

$glossToMsa = [];
foreach ($glossVotes as $word => $msaCounts) {
    arsort($msaCounts);
    $glossToMsa[$word] = array_key_first($msaCounts);
}

$totalDialect = count($dialectToMsa);
$totalGloss   = count($glossToMsa);

/* ── Write PHP lookup file ──────────────────────────────────────────────── */
$php  = "<?php\n";
$php .= "/**\n";
$php .= " * Lisan Sudanese dialect lookup — auto-generated, do NOT edit or commit.\n";
$php .= " * Run: php tools/build_lisan_sudanese_lookup.php\n";
$php .= " *\n";
$php .= " * Source:    Lisan Sudanese Corpus (CC-BY-4.0)\n";
$php .= " *            © SinaLab, Birzeit University\n";
$php .= " *            https://sina.birzeit.edu/currasat\n";
$php .= " * Generated: " . date('Y-m-d H:i:s') . "\n";
$php .= " * Dialect→MSA entries: $totalDialect  |  Gloss→MSA entries: $totalGloss\n";
$php .= " */\n";
$php .= "return [\n";
$php .= "  'dialect_to_msa' => " . var_export($dialectToMsa, true) . ",\n";
$php .= "  'gloss_to_msa'   => " . var_export($glossToMsa,   true) . ",\n";
$php .= "];\n";

// Ensure output directory exists
$outDir = dirname($outPath);
if (!is_dir($outDir)) { mkdir($outDir, 0755, true); }

file_put_contents($outPath, $php);

echo "✓ Processed $rows rows ($skipped skipped)\n";
echo "✓ Dialect→MSA entries: $totalDialect\n";
echo "✓ Gloss→MSA entries:   $totalGloss\n";
echo "✓ Written to: $outPath\n";
