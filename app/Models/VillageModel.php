<?php

namespace App\Models;

use CodeIgniter\Model;

class VillageModel extends Model
{
    protected $table            = 'villages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name_en', 'name_ur', 'slug'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';

    public function getActiveVillages()
    {
        $locale = service('request')->getLocale() === 'ur' ? 'ur' : 'en';
        $rows = $this->orderBy("name_{$locale}", 'ASC')->findAll();
        return array_map(function (array $row) use ($locale) {
            return $row + [
                'display_name' => trim((string)($row["name_{$locale}"] ?? '')),
            ];
        }, $rows);
    }
}
