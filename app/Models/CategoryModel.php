<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name_en',
        'name_ur',
        'slug',
        'icon',
        'display_order',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveCategories()
    {
        $locale = $this->locale();
        $rows = $this->where('status', 'active')
                    ->orderBy('display_order', 'ASC')
                    ->orderBy("name_{$locale}", 'ASC')
                    ->findAll();
        return array_map(function (array $row) {
            $slugOrId = !empty($row['slug']) ? $row['slug'] : $row['id'];
            return $row + [
                'display_name' => $this->localized($row, 'name'),
                'url'          => function_exists('base_url') ? base_url('directory?category=' . $slugOrId) : '/directory?category=' . $slugOrId,
            ];
        }, $rows);
    }

    private function locale(): string { return service('request')->getLocale() === 'ur' ? 'ur' : 'en'; }
    private function localized(array $row, string $field): string
    {
        return trim((string) ($row["{$field}_{$this->locale()}"] ?? ''));
    }
}
