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
        return $this->where('status', 'active')
                    ->orderBy('display_order', 'ASC')
                    ->orderBy('name_en', 'ASC')
                    ->findAll();
    }
}
