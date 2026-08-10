<?php

namespace App\Controllers;

use App\Libraries\WordPressMigrator;

class MigrationController extends BaseController
{
    /**
     * Display migration status and verification
     */
    public function index()
    {
        // Basic verification checks
        $wpSqlPath = 'd:/Wamp/www/kts web project/kts data base/wordpress-35303337d41d.sql';
        $wpUploadsPath = 'd:/Wamp/www/kts web project/kts data base/kts web data base pics/public_html/kts/wp-content/uploads/';
        
        $checks = [
            'sql_file' => file_exists($wpSqlPath),
            'uploads_folder' => is_dir($wpUploadsPath),
            'target_folder' => is_dir(FCPATH . 'uploads/business_images/') || mkdir(FCPATH . 'uploads/business_images/', 0755, true)
        ];
        
        // Get current database statistics
        $db = \Config\Database::connect();
        $stats = [
            'businesses' => $db->table('businesses')->countAllResults(),
            'categories' => $db->table('categories')->countAllResults(),
            'businesses_with_images' => $db->table('businesses')
                ->where('image !=', '')
                ->where('image IS NOT NULL')
                ->countAllResults(),
            'businesses_english' => $db->table('businesses')
                ->where('name_en !=', '')
                ->where('name_en IS NOT NULL')
                ->countAllResults(),
            'businesses_urdu' => $db->table('businesses')
                ->where('name_ur !=', '')
                ->where('name_ur IS NOT NULL')
                ->countAllResults()
        ];
        
        return view('migration/index', [
            'checks' => $checks,
            'stats' => $stats
        ]);
    }
    
    /**
     * Execute WordPress migration
     */
    public function execute()
    {
        try {
            $migrator = new WordPressMigrator();
            $migrationStats = $migrator->migrate();
            $report = $migrator->generateReport();
            
            return view('migration/result', [
                'success' => true,
                'report' => $report,
                'stats' => $migrationStats
            ]);
            
        } catch (\Exception $e) {
            return view('migration/result', [
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Get verification data as JSON
     */
    public function verify()
    {
        $wpSqlPath = 'd:/Wamp/www/kts web project/kts data base/wordpress-35303337d41d.sql';
        $wpUploadsPath = 'd:/Wamp/www/kts web project/kts data base/kts web data base pics/public_html/kts/wp-content/uploads/';
        
        $verification = [
            'sql_file_exists' => file_exists($wpSqlPath),
            'uploads_folder_exists' => is_dir($wpUploadsPath),
            'target_folder_writable' => is_writable(FCPATH . 'uploads/')
        ];
        
        return $this->response->setJSON($verification);
    }
}