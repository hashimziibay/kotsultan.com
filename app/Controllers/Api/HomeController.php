<?php

namespace App\Controllers\Api;

use App\Models\BusinessModel;
use App\Models\CategoryModel;
use App\Models\WallModel;

class HomeController extends BaseApiController
{
    public function index()
    {
        $this->applyLocale();
        helper('localization');
        $locale = $this->apiLocale();

        $categoryModel = new CategoryModel();
        $businessModel = new BusinessModel();
        $wallModel     = new WallModel();

        $categories = $categoryModel->getActiveCategories();
        $recent     = $businessModel->getRecentBusinesses(12);
        $counts     = $businessModel->getCategoryCounts();

        $totalBusinesses = 0;
        $categoryTotals  = [];
        foreach ($counts as $row) {
            $totalBusinesses += (int) $row['total'];
            $categoryTotals[(int) $row['category_id']] = (int) $row['total'];
        }

        // App home shows a featured subset + "view more" for the rest — return all active categories.
        $mappedCategories = array_map(fn ($c) => [
            'id'             => (int) $c['id'],
            'slug'           => $c['slug'] ?? null,
            'name'           => $c['display_name'] ?? ($c['name_en'] ?? ''),
            'name_en'        => $c['name_en'] ?? '',
            'name_ur'        => $c['name_ur'] ?? '',
            'icon'           => $c['icon'] ?? 'folder',
            'business_count' => $categoryTotals[(int) $c['id']] ?? 0,
        ], $categories);

        return $this->jsonOk([
            'locale' => $locale,
            'stats'  => [
                'total_businesses' => $totalBusinesses,
                'categories_count' => count($categories),
                'wall_count'       => count($wallModel->getActiveWallEntries()),
            ],
            'popular_categories' => $mappedCategories,
            'categories'         => $mappedCategories,
            'recent_businesses'  => array_map([$this, 'mapBusinessCard'], $recent),
        ]);
    }

    private function mapBusinessCard(array $b): array
    {
        $image = function_exists('get_business_image_url')
            ? get_business_image_url($b['image'] ?? '')
            : ($this->absoluteUrl($b['image'] ?? '') ?? '');

        return [
            'id'         => (int) $b['id'],
            'slug'       => $b['slug'] ?? null,
            'name'       => $b['display_name'] ?? ($b['name_en'] ?? ''),
            'name_en'    => $b['name_en'] ?? '',
            'name_ur'    => $b['name_ur'] ?? '',
            'owner_name' => $b['owner_name'] ?? '',
            'phone'      => $b['phone'] ?? '',
            'whatsapp'   => $b['whatsapp'] ?? '',
            'address'    => $b['display_address'] ?? '',
            'category'   => $b['display_category_name'] ?? '',
            'image'      => $image,
            'featured'   => (bool) ($b['featured'] ?? false),
        ];
    }
}
