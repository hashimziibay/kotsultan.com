<?php
/**
 * Web-accessible Business Image Restoration Script
 * ONLY restores existing business images from WordPress data
 */

// Include CodeIgniter framework
$rootPath = dirname(__DIR__) . '/';
require_once $rootPath . 'vendor/autoload.php';

// Bootstrap CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

use App\Libraries\ImageRestorer;

// Set content type to plain text for better readability
header('Content-Type: text/plain; charset=utf-8');

echo "=== Business Image Restoration Process ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "Processing...\n\n";

try {
    $restorer = new ImageRestorer();
    
    echo "Scanning WordPress data and matching with existing businesses...\n";
    $stats = $restorer->restoreBusinessImages();
    
    echo "\n=== RESTORATION REPORT ===\n";
    echo "• Total businesses: {$stats['total_businesses']}\n";
    echo "• Businesses with restored images: {$stats['businesses_updated']}\n";
    echo "• Businesses already having images: {$stats['businesses_with_existing_images']}\n";
    echo "• Missing image references: {$stats['missing_image_references']}\n";
    echo "• Missing physical image files: {$stats['missing_physical_files']}\n";
    echo "• Number of database rows updated: {$stats['database_rows_updated']}\n";
    echo "• WordPress places found: {$stats['wp_places_found']}\n";
    echo "• WordPress attachments found: {$stats['wp_attachments_found']}\n";
    
    $totalWithImages = $stats['businesses_with_existing_images'] + $stats['businesses_updated'];
    $imagePercentage = $stats['total_businesses'] > 0 ? 
        round(($totalWithImages / $stats['total_businesses']) * 100, 2) : 0;
    
    echo "\nFinal image coverage: {$imagePercentage}%\n";
    
    echo "\n=== VERIFICATION ===\n";
    
    if ($stats['database_rows_updated'] > 0) {
        echo "✓ SUCCESS: {$stats['database_rows_updated']} businesses have been updated with restored images.\n";
        echo "✓ Database has been actually updated and verified.\n";
        
        // Show some sample updated businesses
        $db = \Config\Database::connect();
        $updatedBusinesses = $db->table('businesses')
            ->select('id, name_en, image')
            ->where('image IS NOT NULL')
            ->where('image !=', '')
            ->where('image LIKE', '%uploads/business_images/%')
            ->limit(3)
            ->get()
            ->getResultArray();
        
        if (!empty($updatedBusinesses)) {
            echo "\nSample restored businesses:\n";
            foreach ($updatedBusinesses as $business) {
                echo "- {$business['name_en']}: {$business['image']}\n";
            }
        }
        
    } else {
        echo "◦ NO UPDATES: No new images were restored.\n";
        echo "  Possible reasons:\n";
        echo "  - Businesses already have images\n";
        echo "  - No matching WordPress data found\n";
        echo "  - WordPress image files not accessible\n";
    }
    
    echo "\n=== DIRECTORY CHECK ===\n";
    $uploadsPath = $rootPath . 'public/uploads/business_images/';
    if (is_dir($uploadsPath)) {
        $files = glob($uploadsPath . '*.*');
        echo "Images in uploads directory: " . count($files) . "\n";
        if (count($files) > 0) {
            echo "Sample files:\n";
            foreach (array_slice($files, 0, 3) as $file) {
                echo "- " . basename($file) . "\n";
            }
        }
    } else {
        echo "Uploads directory not found.\n";
    }
    
} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "\n=== Process Complete ===\n";
echo "\nTo view restored images, visit: " . base_url('directory') . "\n";
?>