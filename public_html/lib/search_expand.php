<?php
/**
 * search_expand.php — Arabic semantic + morphological search expansion
 * ─────────────────────────────────────────────────────────────────────────────
 * Combines two expansion layers when semantic mode is ON:
 *
 *   1. Ontology synonyms  — Arabic Ontology (Birzeit) via ontology_lookup.php
 *      "منظمة" → [جمعية، هيئة، نظام، شركة، …]   (conceptually related words)
 *
 *   2. Root siblings      — Qabas lexical DB (Birzeit) via qabas_lookup.php
 *      "تعليم" → root "علم" → [علم، معلم، عالم، تعلم، …]   (same word family)
 *
 * Both lookup files are auto-generated (gitignored); missing files degrade
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

/**
 * Expand a normalised Arabic search string using ontology synonyms AND
 * root-based morphological siblings from Qabas.
 *
 * Layer 1 — Ontology (up to 8 terms total):
 *   Each query word is looked up in the Arabic Ontology synonym map.
 *   "منظمه" → [جمعيه، هييه، نظام، شركه، موسسه، تنظيم، منشاه]
 *
 * Layer 2 — Root siblings (up to 12 terms total):
 *   Each query word's root is looked up in Qabas.  All lemmas sharing
 *   that root are added as additional search candidates.
 *   "تعليم" (root "علم") → [علم، معلم، عالم، تعلم، مَعلومه، …]
 *
 * The original query is always first (highest relevance).
 * Total result is capped at 12 terms to keep SQL short and fast.
 *
 * @param  string $norm  Already-normalised Arabic text
 * @return string[]      Array of normalised terms, length 1..12
 */
function expandQuery(string $norm): array {
    $terms = [$norm];   // original always first
    $words = preg_split('/\s+/u', $norm, -1, PREG_SPLIT_NO_EMPTY);

    /* ── Layer 1: ontology synonyms ─────────────────────────────────────── */
    $ontology = _ak_getOntologyLookup();
    if (!empty($ontology)) {
        foreach ($words as $word) {
            if (mb_strlen($word, 'UTF-8') < 2) continue;
            if (!isset($ontology[$word]))       continue;
            foreach ($ontology[$word] as $syn) {
                if (!in_array($syn, $terms, true)) {
                    $terms[] = $syn;
                    if (count($terms) >= 8) break 2;
                }
            }
        }
    }

    /* ── Layer 2: root siblings (Qabas) ─────────────────────────────────── */
    if (count($terms) < 12) {
        $qabas = _ak_getQabasLookup();
        if (!empty($qabas)) {
            $lemmaToRoot  = $qabas['lemma_to_root']  ?? [];
            $rootToLemmas = $qabas['root_to_lemmas'] ?? [];
            foreach ($words as $word) {
                if (mb_strlen($word, 'UTF-8') < 2)    continue;
                if (!isset($lemmaToRoot[$word]))        continue;
                $root = $lemmaToRoot[$word];
                foreach ($rootToLemmas[$root] ?? [] as $sibling) {
                    if (!in_array($sibling, $terms, true)) {
                        $terms[] = $sibling;
                        if (count($terms) >= 12) break 2;
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
