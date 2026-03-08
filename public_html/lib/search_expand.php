<?php
/**
 * search_expand.php — Arabic semantic + morphological search expansion
 * ─────────────────────────────────────────────────────────────────────────────
 * Combines three expansion layers when semantic mode is ON:
 *
 *   1. Dialect bridging   — Lisan Sudanese Corpus (Birzeit) via lisan_sudanese_lookup.php
 *      "ضكور" (Sudanese dialect) → "ذكر" (MSA)   (dialect word → standard Arabic)
 *      "education" (English gloss) → "تعليم"      (English → Arabic)
 *      Runs FIRST so the MSA result feeds into layers 2 and 3.
 *
 *   2. Ontology synonyms  — Arabic Ontology (Birzeit) via ontology_lookup.php
 *      "منظمة" → [جمعية، هيئة، نظام، شركة، …]   (conceptually related words)
 *
 *   3. Root siblings      — Qabas lexical DB (Birzeit) via qabas_lookup.php
 *      "تعليم" → root "علم" → [علم، معلم، عالم، تعلم، …]   (same word family)
 *
 * All lookup files are auto-generated (gitignored); missing files degrade
 * gracefully — search still works as plain text. No errors, no changed code.
 *
 * Usage:
 *   require_once '/path/to/lib/search_expand.php';
 *   $terms = expandQuery($normalizedTerm);   // array of terms for OR-LIKE SQL
 * ─────────────────────────────────────────────────────────────────────────────
 */

/* ── Ontology lookup (synonyms) ─────────────────────────────────────────── */
function _ak_getOntologyLookup(): array {
    static $lookup = null;
    if ($lookup !== null) return $lookup;
    $file = __DIR__ . '/ontology_lookup.php';
    $lookup = file_exists($file) ? (include $file) : [];
    return $lookup;
}

/* ── Qabas lookup (root siblings) ──────────────────────────────────────── */
function _ak_getQabasLookup(): array {
    static $data = null;
    if ($data !== null) return $data;
    $file = __DIR__ . '/qabas_lookup.php';
    $data = file_exists($file) ? (include $file) : [];
    return $data;
}

/* ── Lisan Sudanese lookup (dialect→MSA + English gloss→MSA) ───────────── */
function _ak_getLisanSudaneseLookup(): array {
    static $data = null;
    if ($data !== null) return $data;
    $file = __DIR__ . '/lisan_sudanese_lookup.php';
    $data = file_exists($file) ? (include $file) : [];
    return $data;
}

/**
 * Expand a normalised search string using three layers:
 *
 * Layer 1 — Dialect bridging (Lisan Sudanese — runs FIRST):
 *   Maps Sudanese dialect tokens to their MSA lemma, then adds the MSA lemma
 *   to $words so it is picked up by the root and ontology layers below.
 *   "ضكور" → "ذكر"  |  English: "education" → "تعليم"
 *
 * Layer 2 — Ontology synonyms (up to 10 terms total):
 *   Each word (including MSA lemmas from Layer 1) is looked up in the
 *   Arabic Ontology synonym map.
 *   "منظمه" → [جمعيه، هييه، نظام، شركه، موسسه، تنظيم، منشاه]
 *
 * Layer 3 — Root siblings (up to 16 terms total):
 *   Each word's root is looked up in Qabas.  All lemmas sharing that root
 *   are added.  MSA lemmas bridged in from Layer 1 get root-expanded here.
 *   "تعليم" (root "علم") → [علم، معلم، عالم، تعلم، مَعلومه، …]
 *
 * The original query is always first (highest relevance).
 * Total result is capped at 16 terms to keep SQL short and fast.
 *
 * @param  string $norm  Already-normalised search text
 * @return string[]      Array of normalised terms, length 1..16
 */
function expandQuery(string $norm): array {
    $terms = [$norm];   // original always first
    $words = preg_split('/\s+/u', $norm, -1, PREG_SPLIT_NO_EMPTY);

    /* ── Layer 1: Sudanese dialect → MSA bridging ───────────────────────── */
    $lisan = _ak_getLisanSudaneseLookup();
    if (!empty($lisan)) {
        $dialectToMsa = $lisan['dialect_to_msa'] ?? [];
        $glossToMsa   = $lisan['gloss_to_msa']   ?? [];

        foreach ($words as $word) {
            if (mb_strlen($word, 'UTF-8') < 2) continue;
            if (!isset($dialectToMsa[$word]))   continue;
            $msa = $dialectToMsa[$word];
            if (!in_array($msa, $terms, true)) {
                $terms[] = $msa;
                $words[] = $msa;  // feed MSA into layers 2 & 3
            }
        }

        // English gloss lookup — only when the query contains no Arabic characters
        if (!preg_match('/[\x{0600}-\x{06FF}]/u', $norm)) {
            $key = strtolower(trim($norm));
            if (isset($glossToMsa[$key])) {
                $msa = $glossToMsa[$key];
                if (!in_array($msa, $terms, true)) {
                    $terms[] = $msa;
                    $words[] = $msa;  // feed MSA into layers 2 & 3
                }
            }
        }
    }

    /* ── Layer 2: ontology synonyms ─────────────────────────────────────── */
    if (count($terms) < 10) {
        $ontology = _ak_getOntologyLookup();
        if (!empty($ontology)) {
            foreach ($words as $word) {
                if (mb_strlen($word, 'UTF-8') < 2) continue;
                if (!isset($ontology[$word]))       continue;
                foreach ($ontology[$word] as $syn) {
                    if (!in_array($syn, $terms, true)) {
                        $terms[] = $syn;
                        if (count($terms) >= 10) break 2;
                    }
                }
            }
        }
    }

    /* ── Layer 3: root siblings (Qabas) ─────────────────────────────────── */
    if (count($terms) < 16) {
        $qabas = _ak_getQabasLookup();
        if (!empty($qabas)) {
            $lemmaToRoot  = $qabas['lemma_to_root']  ?? [];
            $rootToLemmas = $qabas['root_to_lemmas'] ?? [];
            foreach ($words as $word) {
                if (mb_strlen($word, 'UTF-8') < 2) continue;
                if (!isset($lemmaToRoot[$word]))    continue;
                $root = $lemmaToRoot[$word];
                foreach ($rootToLemmas[$root] ?? [] as $sibling) {
                    if (!in_array($sibling, $terms, true)) {
                        $terms[] = $sibling;
                        if (count($terms) >= 16) break 2;
                    }
                }
            }
        }
    }

    return $terms;
}

/**
 * Build a SQL WHERE fragment that ORs multiple LIKE conditions.
 *
 * @param  string   $nc      The normalised SQL expression (output of normConcat())
 * @param  string[] $terms   Array from expandQuery()
 * @param  object   $db      mysqli or PDO instance (for escaping)
 * @param  string   $dbType  'mysqli' or 'pdo'
 * @return string            e.g. "expr LIKE '%a%' OR expr LIKE '%b%'"
 */
function buildLikeClause(string $nc, array $terms, $db, string $dbType = 'mysqli'): string {
    $clauses = [];
    foreach ($terms as $term) {
        if ($dbType === 'mysqli') {
            $escaped = $db->real_escape_string($term);
            $clauses[] = "$nc LIKE '%{$escaped}%'";
        } else {
            // PDO — caller should use $db->quote() and pass pre-quoted terms
            $quoted    = $db->quote('%' . $term . '%');
            $clauses[] = "$nc LIKE {$quoted}";
        }
    }
    return implode(' OR ', $clauses);
}
