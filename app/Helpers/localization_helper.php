<?php

if (!function_exists('render_localized_text')) {
    /**
     * Renders text with direction and font class strictly according to selected website locale.
     * ZERO regex / preg_match / automatic language detection!
     *
     * @param string|null $text
     * @return string
     */
    function render_localized_text(?string $text): string {
        if (empty($text)) {
            return '';
        }
        
        $isUrdu = (service('request')->getLocale() === 'ur');
        
        if ($isUrdu) {
            return '<span class="font-urdu" dir="rtl">' . esc($text) . '</span>';
        }
        
        return '<span dir="ltr">' . esc($text) . '</span>';
    }
}

if (!function_exists('get_business_image_url')) {
    /**
     * Centralized business-image URL resolver.
     *
     * Takes the raw database reference (e.g. "uploads/businesses/2025/02/photo.avif",
     * a legacy WordPress path, or an absolute URL) and returns a reliable,
     * public, absolute web URL — identical in BOTH languages (images are
     * language-independent).
     *
     * If the physical file genuinely does not exist, it logs the missing
     * reference (so it can be repaired) and returns the designed fallback.
     */
    function get_business_image_url(?string $image, string $fallback = ''): string
    {
        // Existing designed fallback (used by the original business-card markup)
        $defaultFallback = 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&q=80&w=600';
        if ($fallback === '') {
            $fallback = $defaultFallback;
        }

        if (empty($image)) {
            return $fallback;
        }

        // Already absolute (http://, https://, protocol-relative, data:) — keep as-is
        if (preg_match('#^(https?:)?//#i', $image) || stripos($image, 'data:') === 0) {
            return $image;
        }

        // Normalize: strip leading slashes and any redundant "public/" prefix
        $normalized = ltrim($image, '/');
        if (strpos($normalized, 'public/') === 0) {
            $normalized = substr($normalized, 7);
        }

        // Map legacy WordPress / source-folder paths into the app's upload root
        $normalized = preg_replace(
            '#^(?:wp-content/uploads/|kts web data base pics/|kts data base/)#i',
            'uploads/businesses/',
            $normalized
        );

        // Physical file exists under public/ → serve its absolute public URL.
        // Skip is_file() on production page renders (hundreds of disk hits per directory page).
        // Missing images are handled by <img onerror> fallbacks in the views.
        if ($normalized !== '') {
            if (ENVIRONMENT !== 'production') {
                if (is_file(FCPATH . $normalized)) {
                    return base_url($normalized);
                }
            } else {
                return base_url($normalized);
            }
        }

        // Missing file in development: log for repair, then render the designed fallback
        if (ENVIRONMENT !== 'production') {
            log_message('warning', '[BusinessImage] Missing physical file for DB reference: ' . $image . ' (normalized: ' . $normalized . ')');
        }

        return $fallback;
    }
}
