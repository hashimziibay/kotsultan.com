<?php

namespace App\Controllers\Api;

use App\Models\EmergencyModel;

class EmergencyController extends BaseApiController
{
    public function index()
    {
        $this->applyLocale();

        $q        = $this->request->getGet('q');
        $category = $this->request->getGet('category');
        $page     = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage  = min(100, max(1, (int) ($this->request->getGet('per_page') ?? 50)));

        $model  = new EmergencyModel();
        $result = $model->searchEmergencyContacts($q, $category ?: '', $page, $perPage);
        $cats   = (new EmergencyModel())->getCategories();

        return $this->jsonOk([
            'items' => array_values(array_map(static function ($c) {
                $deptEn = trim((string) ($c['department_name_en'] ?? ''));
                $deptUr = trim((string) ($c['department_name_ur'] ?? ''));
                $catEn  = trim((string) ($c['category_en'] ?? ''));
                $catUr  = trim((string) ($c['category_ur'] ?? ''));
                $dept   = trim((string) ($c['department_name'] ?? ''));
                $cat    = trim((string) ($c['category'] ?? ''));

                return [
                    'id'              => (int) ($c['id'] ?? 0),
                    'category'        => $cat !== '' ? $cat : ($catEn !== '' ? $catEn : $catUr),
                    'category_en'     => $catEn,
                    'category_ur'     => $catUr,
                    'department'      => $dept !== '' ? $dept : ($deptEn !== '' ? $deptEn : $deptUr),
                    'department_en'   => $deptEn,
                    'department_ur'   => $deptUr,
                    'phone_primary'   => trim((string) ($c['phone_primary'] ?? '')),
                    'phone_secondary' => trim((string) ($c['phone_secondary'] ?? '')),
                    'email'           => $c['email'] ?? '',
                    'address'         => $c['address'] ?? ($c['address_en'] ?? ''),
                    'working_hours'   => $c['working_hours'] ?? ($c['working_hours_en'] ?? ''),
                    'website'         => $c['website'] ?? '',
                    'google_maps'     => $c['google_maps'] ?? '',
                    'icon'            => $c['icon'] ?? 'phone',
                    'call_url'        => $c['call_url'] ?? '',
                ];
            }, $result['contacts'] ?? [])),
            'categories'  => $cats,
            'total'       => $result['total'] ?? 0,
            'page'        => $result['page'] ?? $page,
            'per_page'    => $result['perPage'] ?? $perPage,
            'total_pages' => $result['totalPages'] ?? 1,
        ]);
    }
}
