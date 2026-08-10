<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BusinessModel;
use App\Models\CategoryModel;
use App\Models\AreaModel;
use App\Models\VillageModel;
use App\Models\WallModel;
use App\Models\EmergencyModel;
use App\Models\AdminActivityLogModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $businessModel  = new BusinessModel();
        $categoryModel  = new CategoryModel();
        $areaModel      = new AreaModel();
        $villageModel   = new VillageModel();
        $wallModel      = new WallModel();
        $emergencyModel = new EmergencyModel();
        $activityModel  = new AdminActivityLogModel();

        // 1. Core Real MySQL Counts
        $totalBusinesses    = $businessModel->countAllResults();
        $activeBusinesses   = $businessModel->where('status', 'active')->countAllResults();
        $inactiveBusinesses = $businessModel->where('status !=', 'active')->countAllResults();

        $totalCategories    = $categoryModel->countAllResults();
        $totalAreas         = $areaModel->countAllResults();
        $totalVillages      = $villageModel->countAllResults();

        $withImages         = $db->table('businesses')->where("image IS NOT NULL AND image != ''")->countAllResults();
        $withoutImages      = $totalBusinesses - $withImages;

        $totalWallMembers   = $wallModel->countAllResults();
        $totalEmergency     = $emergencyModel->countAllResults();

        // 2. Duplicates Count (by phone or name)
        $dupQuery = $db->query("SELECT phone, COUNT(*) as cnt FROM businesses WHERE phone IS NOT NULL AND phone != '' GROUP BY phone HAVING cnt > 1");
        $duplicatePhoneCount = $dupQuery->getNumRows();

        // 3. Translation Health Counts
        $missingEnCount = $db->table('businesses')
                             ->groupStart()
                                 ->where('name_en IS NULL')->orWhere('name_en', '')
                                 ->orWhere('address_en IS NULL')->orWhere('address_en', '')
                             ->groupEnd()
                             ->countAllResults();

        $missingUrCount = $db->table('businesses')
                             ->groupStart()
                                 ->where('name_ur IS NULL')->orWhere('name_ur', '')
                                 ->orWhere('address_ur IS NULL')->orWhere('address_ur', '')
                             ->groupEnd()
                             ->countAllResults();

        // 4. Category Breakdown
        $categoryCounts = $businessModel->getCategoryCounts();

        // 5. Recent Items
        $recentBusinesses = $businessModel->orderBy('created_at', 'DESC')->findAll(5);
        $recentWall       = $wallModel->orderBy('created_at', 'DESC')->findAll(5);
        $recentEmergency  = $emergencyModel->orderBy('created_at', 'DESC')->findAll(5);
        $recentLogs       = $activityModel->orderBy('created_at', 'DESC')->findAll(5);

        return view('admin/dashboard', [
            'title'               => lang('App.admin_page_dashboard_title'),
            'pageHeading'         => lang('App.admin_page_overview'),
            'stats' => [
                'totalBusinesses'    => $totalBusinesses,
                'activeBusinesses'   => $activeBusinesses,
                'inactiveBusinesses' => $inactiveBusinesses,
                'totalCategories'    => $totalCategories,
                'totalAreas'         => $totalAreas,
                'totalVillages'      => $totalVillages,
                'withImages'         => $withImages,
                'withoutImages'      => $withoutImages,
                'totalWallMembers'   => $totalWallMembers,
                'totalEmergency'     => $totalEmergency,
                'duplicatePhoneCount'=> $duplicatePhoneCount,
                'missingEnCount'     => $missingEnCount,
                'missingUrCount'     => $missingUrCount,
            ],
            'categoryCounts'   => $categoryCounts,
            'recentBusinesses' => $recentBusinesses,
            'recentWall'       => $recentWall,
            'recentEmergency'  => $recentEmergency,
            'recentLogs'       => $recentLogs,
        ]);
    }
}
