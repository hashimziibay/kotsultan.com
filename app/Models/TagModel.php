<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table            = 'tags';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name_en', 'name_ur', 'slug'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';

    public function getActiveTags()
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
