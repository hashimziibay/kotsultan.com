<?php

/**
 * Fuzzy / tolerant search helpers for directory + emergency APIs.
 * Supports English, Urdu, Roman-Urdu typos (e.g. "hasptal" → hospital).
 * Synonym packs live in App\Libraries\SearchSynonyms (all categories).
 */

if (! function_exists('search_term_variants')) {
    /**
     * Expand a user query into likely match terms.
     *
     * @return list<string>
     */
    function search_term_variants(string $query): array
    {
        $raw = trim($query);
        if ($raw === '') {
            return [];
        }

        $q = mb_strtolower($raw, 'UTF-8');
        $variants = [$raw, $q];
        $groups = \App\Libraries\SearchSynonyms::forSearch();

        foreach ($groups as $group) {
            $hit = false;
            foreach ($group as $term) {
                $t = mb_strtolower($term, 'UTF-8');
                if ($t === '') {
                    continue;
                }
                if ($q === $t || str_contains($t, $q) || str_contains($q, $t)) {
                    $hit = true;
                    break;
                }
            }
            if ($hit) {
                foreach ($group as $term) {
                    $variants[] = $term;
                    $variants[] = mb_strtolower($term, 'UTF-8');
                }
            }
        }

        // Light consonant-skeleton match against group roots (hasptal ≈ hospital).
        $skeleton = search_consonant_key($q);
        if (mb_strlen($skeleton) >= 4) {
            foreach ($groups as $group) {
                foreach ($group as $term) {
                    $termKey = search_consonant_key($term);
                    if ($termKey === '') {
                        continue;
                    }
                    $a = substr($skeleton, 0, 8);
                    $b = substr($termKey, 0, 8);
                    $distance = (strlen($a) < 255 && strlen($b) < 255) ? levenshtein($a, $b) : 99;
                    if (str_contains($termKey, $skeleton) || str_contains($skeleton, $termKey) || $distance <= 2) {
                        foreach ($group as $add) {
                            $variants[] = $add;
                        }
                        break 2;
                    }
                }
            }
        }

        $variants = array_values(array_unique(array_filter(array_map(static function ($v) {
            return trim((string) $v);
        }, $variants))));

        // Keep SQL OR groups bounded but allow more coverage across categories.
        return array_slice($variants, 0, 36);
    }
}

if (! function_exists('search_consonant_key')) {
    function search_consonant_key(string $value): string
    {
        $value = mb_strtolower(trim($value), 'UTF-8');
        // Keep latin letters only, drop vowels for rough phonetic compare.
        $value = preg_replace('/[^a-z]/', '', $value) ?? '';
        $value = preg_replace('/[aeiou]/', '', $value) ?? '';
        return $value;
    }
}

if (! function_exists('apply_fuzzy_search')) {
    /**
     * Apply OR LIKE / SOUNDEX conditions across columns for all query variants.
     *
     * @param object $builder  CI query builder
     * @param list<string> $columns
     */
    function apply_fuzzy_search($builder, array $columns, string $query): void
    {
        $variants = search_term_variants($query);
        if ($variants === [] || $columns === []) {
            return;
        }

        $db = \Config\Database::connect();
        $builder->groupStart();
        $first = true;

        foreach ($variants as $variant) {
            foreach ($columns as $column) {
                if ($first) {
                    $builder->like($column, $variant);
                    $first = false;
                } else {
                    $builder->orLike($column, $variant);
                }
            }
        }

        // SOUNDEX helps latin typos like hasptal/hospital when available.
        $primary = mb_strtolower(trim($query), 'UTF-8');
        if (preg_match('/^[a-z0-9\s\-]{3,}$/i', $primary)) {
            $escaped = $db->escape($primary);
            foreach ($columns as $column) {
                // Only soundex latin-friendly columns (skip obvious Urdu-named cols still OK in MySQL).
                if (str_ends_with($column, '_ur')) {
                    continue;
                }
                $builder->orWhere("SOUNDEX({$column}) = SOUNDEX({$escaped})", null, false);
            }
        }

        $builder->groupEnd();
    }
}
