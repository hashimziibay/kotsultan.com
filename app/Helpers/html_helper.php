<?php

if (! function_exists('sanitize_rich_text')) {
    /**
     * Allow basic formatting only (safe for wall years_of_service HTML).
     */
    function sanitize_rich_text(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '' || $html === '<p></p>' || $html === '<p><br></p>' || $html === '<p><br/></p>') {
            return '';
        }

        // Drop dangerous blocks early.
        $html = preg_replace('#<(script|iframe|object|embed|form|link|meta|style|svg|math)[^>]*>.*?</\1>#is', '', $html) ?? $html;
        $html = preg_replace('#<(script|iframe|object|embed|form|link|meta|style|svg|math)[^>]*/?>#is', '', $html) ?? $html;
        $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/(href|src)\s*=\s*([\'"])\s*javascript:[^\'"]*\2/i', '$1="#"', $html) ?? $html;

        $allowed = '<p><br><strong><b><em><i><u><s><ul><ol><li><span><a><h3><h4><blockquote><div>';
        $html = strip_tags($html, $allowed);

        // Keep only safe inline styles (color / alignment / weight).
        $html = preg_replace_callback(
            '/\sstyle\s*=\s*("([^"]*)"|\'([^\']*)\')/i',
            static function (array $m): string {
                $style = strtolower((string) ($m[2] !== '' ? $m[2] : ($m[3] ?? '')));
                $keep  = [];
                foreach (preg_split('/\s*;\s*/', $style) ?: [] as $part) {
                    if ($part === '') {
                        continue;
                    }
                    if (preg_match('/^(color|background-color|text-align|font-weight|font-style|text-decoration)\s*:\s*[^;]+$/i', $part)) {
                        if (! preg_match('/expression|url\s*\(|javascript/i', $part)) {
                            $keep[] = $part;
                        }
                    }
                }

                return $keep === [] ? '' : ' style="' . htmlspecialchars(implode('; ', $keep), ENT_QUOTES, 'UTF-8') . '"';
            },
            $html
        ) ?? $html;

        // Force safe link attributes.
        $html = preg_replace_callback(
            '/<a\b([^>]*)>/i',
            static function (array $m): string {
                $attrs = $m[1] ?? '';
                $href  = '';
                if (preg_match('/\bhref\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $attrs, $hm)) {
                    $href = $hm[2] !== '' ? $hm[2] : ($hm[3] !== '' ? $hm[3] : ($hm[4] ?? ''));
                }
                $href = trim($href);
                if ($href === '' || preg_match('/^\s*javascript:/i', $href)) {
                    return '<a>';
                }
                if (! preg_match('#^https?://#i', $href) && ! str_starts_with($href, '/') && ! str_starts_with($href, '#')) {
                    $href = 'https://' . ltrim($href, '/');
                }

                return '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">';
            },
            $html
        ) ?? $html;

        return trim($html);
    }
}

if (! function_exists('render_safe_html')) {
    /**
     * Output sanitized rich text (do not wrap in esc()).
     */
    function render_safe_html(?string $html): string
    {
        return sanitize_rich_text($html);
    }
}

if (! function_exists('plain_from_html')) {
    /**
     * Plain-text fallback (API / mobile previews).
     */
    function plain_from_html(?string $html): string
    {
        $text = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }
}
