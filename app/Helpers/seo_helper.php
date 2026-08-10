<?php

/**
 * SEO helpers for KotSultan.com public URLs.
 * Pattern: {name}-in-kot-sultan
 */

if (! function_exists('seo_place_suffix')) {
    function seo_place_suffix(): string
    {
        return 'in-kot-sultan';
    }
}

if (! function_exists('seo_is_ascii_slug')) {
    function seo_is_ascii_slug(?string $slug): bool
    {
        return is_string($slug)
            && $slug !== ''
            && (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug);
    }
}

if (! function_exists('seo_base_slug')) {
    /**
     * ASCII-only slug from a name (English preferred).
     */
    function seo_base_slug(string $name): string
    {
        $name = trim($name);
        $slug = url_title($name, '-', true);

        if ($slug === '' && $name !== '' && function_exists('iconv')) {
            $translit = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
            $slug     = url_title((string) $translit, '-', true);
        }

        return $slug !== '' ? $slug : '';
    }
}

if (! function_exists('seo_strip_place_suffix')) {
    function seo_strip_place_suffix(string $slug): string
    {
        $suffix = preg_quote(seo_place_suffix(), '/');
        $out    = preg_replace('/-?' . $suffix . '(?:-\d+)?$/i', '', $slug);
        $out    = trim((string) $out, '-');

        return $out !== '' ? $out : $slug;
    }
}

if (! function_exists('seo_with_place')) {
    function seo_with_place(string $base, ?int $id = null): string
    {
        $base = seo_strip_place_suffix(trim($base, '-'));
        $base = trim($base, '-');
        if ($base === '') {
            $base = 'listing';
        }

        $slug = $base . '-' . seo_place_suffix();
        if ($id !== null && $id > 0) {
            $slug .= '-' . $id;
        }

        return $slug;
    }
}

if (! function_exists('seo_extract_trailing_id')) {
    function seo_extract_trailing_id(string $slug): ?int
    {
        if (preg_match('/-(\d+)$/', $slug, $m)) {
            return (int) $m[1];
        }

        return null;
    }
}

if (! function_exists('seo_listing_slug_from_row')) {
    /**
     * Public listing slug for a business row (ASCII + place suffix).
     */
    function seo_listing_slug_from_row(array $row): string
    {
        $id   = (int) ($row['id'] ?? 0);
        $slug = (string) ($row['slug'] ?? '');

        if (seo_is_ascii_slug($slug) && str_contains($slug, seo_place_suffix())) {
            return $slug;
        }

        $base = seo_base_slug((string) ($row['name_en'] ?? ''));
        if ($base === '') {
            $base = seo_is_ascii_slug($slug) ? seo_strip_place_suffix($slug) : 'listing';
        }

        // Always pin id when regenerating from a bad/legacy slug so URLs stay unique
        // and reverse-lookup always works.
        return seo_with_place($base, $id > 0 ? $id : null);
    }
}

if (! function_exists('make_unique_listing_seo_slug')) {
    /**
     * Build a unique DB slug: {name}-in-kot-sultan (or ...-{id} if taken).
     */
    function make_unique_listing_seo_slug(string $nameEn, int $id, $db = null): string
    {
        $db   = $db ?? \Config\Database::connect();
        $base = seo_base_slug($nameEn);
        if ($base === '') {
            return seo_with_place('listing', $id);
        }

        $desired = seo_with_place($base);
        $row     = $db->table('businesses')
            ->select('id')
            ->where('slug', $desired)
            ->get()
            ->getRowArray();

        if ($row && (int) $row['id'] !== $id) {
            return seo_with_place($base, $id);
        }

        return $desired;
    }
}

if (! function_exists('make_unique_category_seo_slug')) {
    /**
     * Category public path segment: {name}-in-kot-sultan
     * Stored category.slug stays the short ASCII base (hospitals).
     */
    function make_unique_category_seo_slug(string $nameEn, int $id, $db = null): string
    {
        $db   = $db ?? \Config\Database::connect();
        $base = seo_base_slug($nameEn);
        if ($base === '') {
            $base = 'category-' . $id;
        }

        $row = $db->table('categories')
            ->select('id')
            ->where('slug', $base)
            ->get()
            ->getRowArray();

        if ($row && (int) $row['id'] !== $id) {
            $base .= '-' . $id;
        }

        return $base;
    }
}

if (! function_exists('seo_category_path_slug')) {
    function seo_category_path_slug(array $row): string
    {
        $base = (string) ($row['slug'] ?? '');
        if (! seo_is_ascii_slug($base)) {
            $base = make_unique_category_seo_slug((string) ($row['name_en'] ?? 'category'), (int) ($row['id'] ?? 0));
        }

        return seo_with_place(seo_strip_place_suffix($base));
    }
}
