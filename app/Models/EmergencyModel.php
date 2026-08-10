<?php

namespace App\Models;

use CodeIgniter\Model;

class EmergencyModel extends Model
{
    protected $table            = 'emergency_contacts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_en',
        'category_ur',
        'department_name_en',
        'department_name_ur',
        'phone_primary',
        'phone_secondary',
        'email',
        'address_en',
        'address_ur',
        'working_hours_en',
        'working_hours_ur',
        'website',
        'google_maps',
        'latitude',
        'longitude',
        'icon',
        'display_order',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function searchEmergencyContacts($query = '', $category = '', $page = 1, ?int $perPage = 18)
    {
        $locale = service('request')->getLocale();
        $isUrdu = ($locale === 'ur');

        $builder = $this->where('status', 'active');

        if (!empty($category) && $category !== 'all') {
            // Accept English or Urdu category labels from the mobile app.
            $builder->groupStart()
                    ->where('category_en', $category)
                    ->orWhere('category_ur', $category)
                    ->groupEnd();
        }

        if ($query !== null && trim((string)$query) !== '') {
            helper('search');
            apply_fuzzy_search($builder, [
                'department_name_en',
                'department_name_ur',
                'category_en',
                'category_ur',
                'address_en',
                'address_ur',
                'working_hours_en',
                'working_hours_ur',
                'phone_primary',
                'phone_secondary',
            ], trim((string) $query));
        }

        if ($perPage === null) {
            $rows = $builder->orderBy('display_order', 'ASC')
                           ->orderBy("department_name_{$locale}", 'ASC')
                           ->findAll();
            return $this->localizedRows($rows, $isUrdu);
        }

        $page = max(1, (int)$page);
        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;

        $rows = $builder->orderBy('display_order', 'ASC')
                       ->orderBy("department_name_{$locale}", 'ASC')
                       ->limit($perPage, $offset)
                       ->findAll();

        return [
            'contacts'   => $this->localizedRows($rows, $isUrdu),
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }

    public function getCategories()
    {
        $locale = service('request')->getLocale();
        $isUrdu = ($locale === 'ur');
        $catCol = $isUrdu ? 'category_ur' : 'category_en';

        return $this->select("{$catCol} as category, icon, category_en, category_ur, COUNT(*) as count")
                    ->where('status', 'active')
                    ->groupBy($catCol)
                    ->orderBy($catCol, 'ASC')
                    ->findAll();
    }

    /**
     * Localizes row fields strictly based on current language.
     * ZERO cross-language fallback mixing!
     */
    private function localizedRows(array $rows, bool $isUrdu): array
    {
        return array_map(function ($row) use ($isUrdu) {
            $row['category']        = trim((string)($isUrdu ? ($row['category_ur'] ?? '') : ($row['category_en'] ?? '')));
            $row['department_name'] = trim((string)($isUrdu ? ($row['department_name_ur'] ?? '') : ($row['department_name_en'] ?? '')));
            $row['address']         = trim((string)($isUrdu ? ($row['address_ur'] ?? '') : ($row['address_en'] ?? '')));
            $row['working_hours']   = trim((string)($isUrdu ? ($row['working_hours_ur'] ?? '') : ($row['working_hours_en'] ?? '')));

            if (empty($row['icon'])) {
                $cat = strtolower($row['category_en'] ?? '');
                if (str_contains($cat, 'police')) $row['icon'] = 'shield-alert';
                elseif (str_contains($cat, 'medical') || str_contains($cat, 'health')) $row['icon'] = 'ambulance';
                elseif (str_contains($cat, 'fire')) $row['icon'] = 'flame';
                elseif (str_contains($cat, 'utility')) $row['icon'] = 'zap';
                elseif (str_contains($cat, 'women') || str_contains($cat, 'child')) $row['icon'] = 'heart-handshake';
                else $row['icon'] = 'phone-call';
            }

            $cleanPhone = preg_replace('/[^0-9+]/', '', $row['phone_primary'] ?? '');
            $row['call_url'] = 'tel:' . $cleanPhone;
            $row['maps_url'] = !empty($row['google_maps']) 
                ? $row['google_maps'] 
                : 'https://www.google.com/maps/search/?api=1&query=' . urlencode(($row['department_name_en'] ?? $row['department_name']) . ' Kot Sultan Layyah');
            $row['website_url'] = !empty($row['website']) 
                ? (str_starts_with($row['website'], 'http') ? $row['website'] : 'https://' . $row['website']) 
                : '';

            return $row;
        }, $rows);
    }
}
