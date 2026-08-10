<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

class ImageRestorer
{
    protected $db;
    protected $wpUploadsPath;
    protected $publicUploadsPath;
    protected $baseUrl;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->wpUploadsPath = ROOTPATH . 'kts data base/kts web data base pics/public_html/kts/wp-content/uploads/';
        $this->publicUploadsPath = ROOTPATH . 'public/uploads/business_images/';
        $this->baseUrl = base_url('uploads/business_images/');
        
        // Ensure public uploads directory exists
        if (!is_dir($this->publicUploadsPath)) {
            mkdir($this->publicUploadsPath, 0755, true);
        }
    }

    /**
     * Restore business images from WordPress database - ONLY update existing businesses
     */
    public function restoreBusinessImages()
    {
        $stats = [
            'total_businesses' => 0,
            'businesses_with_existing_images' => 0,
            'businesses_updated' => 0,
            'wp_places_found' => 0,
            'wp_attachments_found' => 0,
            'missing_image_references' => 0,
            'missing_physical_files' => 0,
            'database_rows_updated' => 0
        ];

        // Get total businesses count
        $stats['total_businesses'] = $this->db->table('businesses')->countAllResults();
        
        // Count businesses that already have images
        $stats['businesses_with_existing_images'] = $this->db->table('businesses')
            ->where('image IS NOT NULL')
            ->where('image !=', '')
            ->countAllResults();

        // Check if WordPress tables exist
        $tables = $this->db->listTables();
        
        if (in_array('9b_geodir_gd_place_detail', $tables)) {
            $wpPlaces = $this->db->table('9b_geodir_gd_place_detail')
                ->select('post_id, post_title, featured_image')
                ->where('featured_image IS NOT NULL')
                ->where('featured_image !=', '')
                ->get()
                ->getResultArray();
            
            $stats['wp_places_found'] = count($wpPlaces);
            
            foreach ($wpPlaces as $place) {
                $result = $this->processWordPressPlace($place);
                if ($result['updated']) {
                    $stats['businesses_updated']++;
                    $stats['database_rows_updated']++;
                }
                if ($result['missing_reference']) {
                    $stats['missing_image_references']++;
                }
                if ($result['missing_file']) {
                    $stats['missing_physical_files']++;
                }
            }
        }

        if (in_array('9b_geodir_attachments', $tables)) {
            $wpAttachments = $this->db->table('9b_geodir_attachments')
                ->select('post_id, title, file, featured')
                ->where('file IS NOT NULL')
                ->where('file !=', '')
                ->where('featured', 1)
                ->get()
                ->getResultArray();
            
            $stats['wp_attachments_found'] = count($wpAttachments);
            
            foreach ($wpAttachments as $attachment) {
                $result = $this->processWordPressAttachment($attachment);
                if ($result['updated']) {
                    $stats['businesses_updated']++;
                    $stats['database_rows_updated']++;
                }
                if ($result['missing_reference']) {
                    $stats['missing_image_references']++;
                }
                if ($result['missing_file']) {
                    $stats['missing_physical_files']++;
                }
            }
        }

        return $stats;
    }

    /**
     * Process a WordPress place and try to match it to existing business
     */
    protected function processWordPressPlace($place)
    {
        $result = ['updated' => false, 'missing_reference' => false, 'missing_file' => false];
        
        if (empty($place['featured_image'])) {
            $result['missing_reference'] = true;
            return $result;
        }

        // Find matching CodeIgniter business
        $business = $this->findMatchingBusiness($place['post_title']);
        
        if (!$business) {
            return $result;
        }

        // Skip if business already has an image
        if (!empty($business['image'])) {
            return $result;
        }

        // Try to find and copy the image
        $imageUrl = $this->findAndCopyWordPressImage($place['featured_image'], $place['post_title']);
        
        if ($imageUrl) {
            // Update the business record
            $this->db->table('businesses')
                ->where('id', $business['id'])
                ->update(['image' => $imageUrl]);
            
            $result['updated'] = true;
        } else {
            $result['missing_file'] = true;
        }

        return $result;
    }

    /**
     * Process a WordPress attachment and try to match it to existing business
     */
    protected function processWordPressAttachment($attachment)
    {
        $result = ['updated' => false, 'missing_reference' => false, 'missing_file' => false];
        
        if (empty($attachment['file'])) {
            $result['missing_reference'] = true;
            return $result;
        }

        // Try to find business by title or by post_id relation
        $business = $this->findMatchingBusiness($attachment['title']);
        
        if (!$business) {
            return $result;
        }

        // Skip if business already has an image
        if (!empty($business['image'])) {
            return $result;
        }

        // Try to find and copy the image
        $imageUrl = $this->findAndCopyWordPressImage($attachment['file'], $attachment['title']);
        
        if ($imageUrl) {
            // Update the business record
            $this->db->table('businesses')
                ->where('id', $business['id'])
                ->update(['image' => $imageUrl]);
            
            $result['updated'] = true;
        } else {
            $result['missing_file'] = true;
        }

        return $result;
    }

    /**
     * Find matching CodeIgniter business by name similarity
     */
    protected function findMatchingBusiness($wpTitle)
    {
        if (empty($wpTitle)) {
            return null;
        }

        $businesses = $this->db->table('businesses')
            ->select('id, name_en, name_ur, slug, image')
            ->get()
            ->getResultArray();

        foreach ($businesses as $business) {
            if ($this->isSimilarName($wpTitle, $business['name_en']) ||
                $this->isSimilarName($wpTitle, $business['name_ur']) ||
                $this->isSimilarSlug($wpTitle, $business['slug'])) {
                return $business;
            }
        }

        return null;
    }

    /**
     * Check if two names are similar
     */
    protected function isSimilarName($wpName, $businessName)
    {
        if (empty($wpName) || empty($businessName)) {
            return false;
        }

        $wpName = strtolower(trim($wpName));
        $businessName = strtolower(trim($businessName));

        // Exact match
        if ($wpName === $businessName) {
            return true;
        }

        // Contains match
        if (strpos($wpName, $businessName) !== false || strpos($businessName, $wpName) !== false) {
            return true;
        }

        // Similar words (at least 80% similarity)
        $similarity = 0;
        similar_text($wpName, $businessName, $similarity);
        return $similarity >= 80;
    }

    /**
     * Check if slug is similar to title
     */
    protected function isSimilarSlug($title, $slug)
    {
        if (empty($title) || empty($slug)) {
            return false;
        }

        $titleSlug = url_title($title, '-', true);
        return $titleSlug === $slug || strpos($slug, $titleSlug) !== false || strpos($titleSlug, $slug) !== false;
    }

    /**
     * Find WordPress image file and copy to public directory
     */
    protected function findAndCopyWordPressImage($imagePath, $title = '')
    {
        // Clean the image path
        $imagePath = trim($imagePath);
        
        // Remove any URL prefix if present
        $imagePath = str_replace(['http://', 'https://'], '', $imagePath);
        $imagePath = preg_replace('/^[^\/]*\//', '', $imagePath);
        $imagePath = str_replace('wp-content/uploads/', '', $imagePath);

        // Try different possible paths
        $possiblePaths = [
            $this->wpUploadsPath . $imagePath,
            $this->wpUploadsPath . ltrim($imagePath, '/'),
        ];

        // Also check in common directories
        if (!strpos($imagePath, '/')) {
            $commonDirs = ['2025/01/', '2025/02/', '2025/03/', '2026/01/', '2026/02/', '2026/03/', 'classified-listing/2025/02/'];
            foreach ($commonDirs as $dir) {
                $possiblePaths[] = $this->wpUploadsPath . $dir . $imagePath;
            }
        }

        foreach ($possiblePaths as $sourcePath) {
            if (file_exists($sourcePath)) {
                return $this->copyImageToPublic($sourcePath, $title);
            }
        }

        // Try recursive search as last resort
        return $this->searchImageRecursively(basename($imagePath), $title);
    }

    /**
     * Search for image recursively in WordPress uploads
     */
    protected function searchImageRecursively($filename, $title = '')
    {
        if (!is_dir($this->wpUploadsPath)) {
            return null;
        }

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->wpUploadsPath, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getFilename() === $filename) {
                    return $this->copyImageToPublic($file->getPathname(), $title);
                }
            }
        } catch (Exception $e) {
            // Ignore directory traversal errors
        }

        return null;
    }

    /**
     * Copy image file to public directory
     */
    protected function copyImageToPublic($sourcePath, $title = '')
    {
        $filename = basename($sourcePath);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Create a unique filename to avoid conflicts
        $timestamp = time();
        $cleanTitle = $this->sanitizeFilename($title);
        $newFilename = $cleanTitle ? "{$cleanTitle}_{$timestamp}.{$extension}" : "{$timestamp}_{$filename}";
        
        $destinationPath = $this->publicUploadsPath . $newFilename;
        
        if (copy($sourcePath, $destinationPath)) {
            return $this->baseUrl . $newFilename;
        }
        
        return null;
    }

    /**
     * Sanitize filename
     */
    protected function sanitizeFilename($title)
    {
        $clean = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $title);
        $clean = preg_replace('/_{2,}/', '_', $clean);
        $clean = trim($clean, '_');
        return substr($clean, 0, 30);
    }
}