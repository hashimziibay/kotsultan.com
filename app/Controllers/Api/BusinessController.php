<?php

namespace App\Controllers\Api;

use App\Models\BusinessModel;
use App\Models\CategoryModel;

class BusinessController extends BaseApiController
{
    public function index()
    {
        $this->applyLocale();
        helper('localization');

        $q        = $this->request->getGet('q');
        $category = $this->request->getGet('category');
        $page     = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage  = min(50, max(1, (int) ($this->request->getGet('per_page') ?? 30)));

        $result = (new BusinessModel())->searchDirectory($q, $category, null, $page, $perPage);

        return $this->jsonOk([
            'items'       => array_map([$this, 'mapBusinessCard'], $result['businesses']),
            'total'       => $result['total'],
            'page'        => $result['page'],
            'per_page'    => $result['perPage'],
            'total_pages' => $result['totalPages'],
        ]);
    }

    public function show($idOrSlug = null)
    {
        $this->applyLocale();
        helper('localization');

        if ($idOrSlug === null || $idOrSlug === '') {
            return $this->jsonError('Business not found', 404);
        }

        $business = (new BusinessModel())->getLocalizedBusiness($idOrSlug);
        if (!$business) {
            return $this->jsonError('Business not found', 404);
        }

        return $this->jsonOk($this->mapBusinessDetail($business));
    }

    public function categories()
    {
        $this->applyLocale();
        $categories = (new CategoryModel())->getActiveCategories();
        $counts     = (new BusinessModel())->getCategoryCounts();
        $totals     = [];
        foreach ($counts as $row) {
            $totals[(int) $row['category_id']] = (int) $row['total'];
        }

        return $this->jsonOk(array_map(static function ($c) use ($totals) {
            return [
                'id'             => (int) $c['id'],
                'slug'           => $c['slug'] ?? null,
                'name'           => $c['display_name'] ?? ($c['name_en'] ?? ''),
                'name_en'        => $c['name_en'] ?? '',
                'name_ur'        => $c['name_ur'] ?? '',
                'icon'           => $c['icon'] ?? 'folder',
                'business_count' => $totals[(int) $c['id']] ?? 0,
            ];
        }, $categories));
    }

    private function mapBusinessCard(array $b): array
    {
        $image = function_exists('get_business_image_url')
            ? get_business_image_url($b['image'] ?? '')
            : ($this->absoluteUrl($b['image'] ?? '') ?? '');

        return [
            'id'         => (int) $b['id'],
            'slug'       => $b['seo_slug'] ?? ($b['slug'] ?? null),
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

    private function mapBusinessDetail(array $b): array
    {
        $card = $this->mapBusinessCard($b);
        return $card + [
            'description' => $b['display_description'] ?? '',
            'email'       => $b['email'] ?? '',
            'website'     => $b['website'] ?? '',
            'google_map'  => $b['google_map'] ?? '',
            'latitude'    => $b['latitude'] ?? null,
            'longitude'   => $b['longitude'] ?? null,
            'area'        => $b['display_area_name'] ?? '',
            'village'     => $b['display_village_name'] ?? '',
        ];
    }
}
