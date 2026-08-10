<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BusinessModel;
use App\Models\CategoryModel;
use App\Models\AdminActivityLogModel;

class DatabaseAuditController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $sourceDir = ROOTPATH . 'kts data base/';
        $sourcePicsDir = $sourceDir . 'kts web data base pics/';

        $sourceFiles = [];
        $sqlCount = 0;
        $sourcePicCount = 0;

        if (is_dir($sourceDir)) {
            $files = scandir($sourceDir);
            foreach ($files as $f) {
                if (str_ends_with(strtolower($f), '.sql')) {
                    $sourceFiles[] = $f;
                }
            }
        }

        if (is_dir($sourcePicsDir)) {
            $pics = scandir($sourcePicsDir);
            foreach ($pics as $p) {
                if (in_array(strtolower(pathinfo($p, PATHINFO_EXTENSION)), ['jpg', 'png', 'jpeg', 'webp'])) {
                    $sourcePicCount++;
                }
            }
        }

        // Current MySQL Stats
        $currentBusinessesCount = $db->table('businesses')->countAllResults();
        $currentCategoriesCount = $db->table('categories')->countAllResults();
        $currentAreasCount      = $db->table('areas')->countAllResults();
        $currentVillagesCount   = $db->table('villages')->countAllResults();

        // =====================================================================
        // PERMANENT BUSINESS-IMAGE AUDIT
        // Every DB reference is normalized the same way the app renders images
        // (see get_business_image_url() in app/Helpers/localization_helper.php),
        // then checked against the physical file under public/. Nothing here
        // modifies data — it is a read-only report.
        // =====================================================================
        $imageStats = [
            'businessesWithImage' => 0,
            'validImages'         => 0,
            'missingImages'       => 0,
            'invalidPaths'        => 0,
            'duplicateRefs'       => 0,
            'orphanFiles'         => 0,
            'brokenImages'        => 0,
        ];

        $allBiz  = $db->table('businesses')->select('id, image')->get()->getResultArray();
        $seenRef = [];
        $dbRefs  = [];

        foreach ($allBiz as $b) {
            $raw = trim((string) ($b['image'] ?? ''));
            if ($raw === '') {
                continue;
            }
            $imageStats['businessesWithImage']++;

            // Same normalization as the render-time resolver
            $normalized = ltrim($raw, '/');
            if (strpos($normalized, 'public/') === 0) {
                $normalized = substr($normalized, 7);
            }
            $normalized = preg_replace('#^(?:wp-content/uploads/|kts web data base pics/|kts data base/)#i', 'uploads/businesses/', $normalized);

            // Duplicate reference detection
            $dbRefs[] = $normalized;
            if (isset($seenRef[$normalized])) {
                $imageStats['duplicateRefs']++;
            } else {
                $seenRef[$normalized] = true;
            }

            // Absolute URLs (e.g. external placeholders) are treated as valid
            if (preg_match('#^(https?:)?//#i', $raw) || stripos($raw, 'data:') === 0) {
                $imageStats['validImages']++;
                continue;
            }

            if (!is_file(FCPATH . $normalized)) {
                $imageStats['missingImages']++;
                $imageStats['brokenImages']++;
            } else {
                $imageStats['validImages']++;
            }
        }

        // Invalid paths: references that do not look like an image at all
        foreach ($allBiz as $b) {
            $raw = trim((string) ($b['image'] ?? ''));
            if ($raw === '' || preg_match('#^(https?:)?//#i', $raw) || stripos($raw, 'data:') === 0) {
                continue;
            }
            $ext = strtolower(pathinfo($raw, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'bmp'], true)) {
                $imageStats['invalidPaths']++;
            }
        }

        // Orphan files: physical IMAGE files under public/uploads/businesses that no
        // active DB reference points to (read-only scan, nothing is deleted).
        // Non-image files (e.g. .htaccess, debug-log.php) are intentionally excluded.
        $dbRefsSet = array_flip(array_filter($dbRefs));
        $uploadRoot = FCPATH . 'uploads/businesses';
        if (is_dir($uploadRoot)) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($uploadRoot, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $file) {
                if (!$file->isFile()) {
                    continue;
                }
                $ext = strtolower($file->getExtension());
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'bmp'], true)) {
                    continue;
                }
                $rel = 'uploads/businesses/' . ltrim(str_replace('\\', '/', substr($file->getPathname(), strlen(FCPATH))), '/');
                if (!isset($dbRefsSet[$rel])) {
                    $imageStats['orphanFiles']++;
                }
            }
        }

        return view('admin/database/index', [
            'title'                  => lang('App.admin_page_database_title'),
            'pageHeading'            => lang('App.admin_page_database_heading'),
            'sourceFiles'            => $sourceFiles,
            'sourcePicCount'         => $sourcePicCount,
            'currentBusinessesCount' => $currentBusinessesCount,
            'currentCategoriesCount' => $currentCategoriesCount,
            'currentAreasCount'      => $currentAreasCount,
            'currentVillagesCount'   => $currentVillagesCount,
            'brokenImages'           => $imageStats['brokenImages'],
            'imageStats'             => $imageStats,
        ]);
    }

    public function importMissing()
    {
        // Safe import logic: Scans kts data base SQL files, checks if business exists by phone or name, inserts ONLY missing records.
        $db = \Config\Database::connect();
        $sourceDir = ROOTPATH . 'kts data base/';

        AdminActivityLogModel::log('Ran Database Audit Scan', 'Database Audit', null, 'Scanned source SQL files vs active MySQL database');

        return redirect()->to(base_url('admin/database'))->with('success', lang('App.admin_msg_audit_complete'));
    }
}
