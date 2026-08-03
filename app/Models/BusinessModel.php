<?php

namespace App\Models;

use CodeIgniter\Model;

class BusinessModel extends Model
{
    protected $table            = 'businesses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'category_id',
        'name_en',
        'name_ur',
        'slug',
        'owner_name',
        'address',
        'phone',
        'whatsapp',
        'latitude',
        'longitude',
        'google_map',
        'image',
        'featured',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getRecentBusinesses($limit = 6)
    {
        return $this->select('businesses.*, categories.name_en as category_name_en, categories.name_ur as category_name_ur, categories.icon as category_icon')
                    ->join('categories', 'categories.id = businesses.category_id')
                    ->where('businesses.status', 'active')
                    ->orderBy('businesses.created_at', 'DESC')
                    ->findAll($limit);
    }

    public function searchDirectory($query = '', $categoryId = null, $tagId = null)
    {
        $builder = $this->select('businesses.*, categories.name_en as category_name_en, categories.name_ur as category_name_ur, categories.icon as category_icon')
                        ->join('categories', 'categories.id = businesses.category_id')
                        ->where('businesses.status', 'active');

        if (!empty($categoryId)) {
            $builder->where('businesses.category_id', $categoryId);
        }

        if (!empty($query)) {
            $builder->groupStart()
                    ->like('businesses.name_en', $query)
                    ->orLike('businesses.name_ur', $query)
                    ->orLike('businesses.owner_name', $query)
                    ->orLike('businesses.address', $query)
                    ->orLike('businesses.phone', $query)
                    ->orLike('categories.name_en', $query)
                    ->orLike('categories.name_ur', $query)
                    ->groupEnd();
        }

        if (!empty($tagId)) {
            $builder->join('business_tags', 'business_tags.business_id = businesses.id')
                    ->where('business_tags.tag_id', $tagId);
        }

        return $builder->orderBy('businesses.featured', 'DESC')
                       ->orderBy('businesses.name_en', 'ASC')
                       ->findAll();
    }

    public function getCategoryCounts()
    {
        return $this->select('category_id, COUNT(*) as total')
                    ->where('status', 'active')
                    ->groupBy('category_id')
                    ->findAll();
    }
}
