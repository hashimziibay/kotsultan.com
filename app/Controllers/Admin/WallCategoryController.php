<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminActivityLogModel;
use App\Models\WallCategoryModel;
use App\Models\WallModel;

class WallCategoryController extends BaseController
{
    public function index()
    {
        $model = new WallCategoryModel();

        $query  = trim((string) $this->request->getGet('q'));
        $status = trim((string) ($this->request->getGet('status') ?? ''));

        $builder = $model->builder();
        if ($query !== '') {
            $builder->groupStart()
                ->like('name_en', $query)
                ->orLike('name_ur', $query)
                ->orLike('slug', $query)
                ->groupEnd();
        }
        if ($status === 'active' || $status === 'inactive') {
            $builder->where('status', $status);
        }

        $categories = $builder->orderBy('display_order', 'ASC')
            ->orderBy('name_en', 'ASC')
            ->get()
            ->getResultArray();

        $countsMap = $this->personalityCountsByCategory();

        return view('admin/wall_categories/index', [
            'title'          => lang('App.admin_wall_categories_title'),
            'pageHeading'    => lang('App.admin_wall_categories_title'),
            'categories'     => $categories,
            'countsMap'      => $countsMap,
            'query'          => $query,
            'selectedStatus' => $status,
        ]);
    }

    public function create()
    {
        return view('admin/wall_categories/form', [
            'title'       => lang('App.admin_add_wall_category'),
            'pageHeading' => lang('App.admin_add_wall_category'),
            'category'    => null,
        ]);
    }

    public function store()
    {
        $rules = [
            'name_en' => 'required|min_length[2]',
            'name_ur' => 'required|min_length[2]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        helper('seo');
        $model = new WallCategoryModel();
        $data  = $this->parseFormData();
        $data['slug'] = 'pending-' . bin2hex(random_bytes(3));

        $id = (int) $model->insert($data);
        if ($id < 1) {
            return redirect()->back()->withInput()->with('error', lang('App.admin_msg_wall_category_failed') ?: 'Could not create category');
        }

        $model->update($id, [
            'slug' => $this->uniqueWallCategorySlug($data['name_en'], $id),
        ]);

        AdminActivityLogModel::log('Created Wall Category', 'Wall Categories', $id, "Created {$data['name_en']}");

        return redirect()->to(base_url('admin/wall-categories'))
            ->with('success', lang('App.admin_msg_wall_category_created'));
    }

    public function edit($id)
    {
        $model    = new WallCategoryModel();
        $category = $model->find($id);
        if (! $category) {
            return redirect()->to(base_url('admin/wall-categories'))
                ->with('error', lang('App.admin_msg_wall_category_not_found'));
        }

        return view('admin/wall_categories/form', [
            'title'       => lang('App.admin_edit_wall_category'),
            'pageHeading' => lang('App.admin_edit_wall_category') . ': ' . ($category['name_en'] ?? ''),
            'category'    => $category,
        ]);
    }

    public function update($id)
    {
        $model    = new WallCategoryModel();
        $category = $model->find($id);
        if (! $category) {
            return redirect()->to(base_url('admin/wall-categories'))
                ->with('error', lang('App.admin_msg_wall_category_not_found'));
        }

        $rules = [
            'name_en' => 'required|min_length[2]',
            'name_ur' => 'required|min_length[2]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        helper('seo');
        $data         = $this->parseFormData();
        $data['slug'] = $this->uniqueWallCategorySlug($data['name_en'], (int) $id);
        $model->update((int) $id, $data);

        AdminActivityLogModel::log('Updated Wall Category', 'Wall Categories', $id, "Updated {$data['name_en']}");

        return redirect()->to(base_url('admin/wall-categories'))
            ->with('success', lang('App.admin_msg_wall_category_updated'));
    }

    public function delete($id)
    {
        $model    = new WallCategoryModel();
        $category = $model->find($id);
        if (! $category) {
            return redirect()->to(base_url('admin/wall-categories'))
                ->with('error', lang('App.admin_msg_wall_category_not_found'));
        }

        $inUse = $this->personalityCountForCategory((int) $id);
        if ($inUse > 0) {
            return redirect()->to(base_url('admin/wall-categories'))
                ->with('error', lang('App.admin_msg_wall_category_in_use', [$category['name_en'], $inUse]));
        }

        $model->delete($id);
        AdminActivityLogModel::log('Deleted Wall Category', 'Wall Categories', $id, "Deleted {$category['name_en']}");

        return redirect()->to(base_url('admin/wall-categories'))
            ->with('success', lang('App.admin_msg_wall_category_deleted'));
    }

    public function toggle($id)
    {
        $model    = new WallCategoryModel();
        $category = $model->find($id);
        if (! $category) {
            return redirect()->back()->with('error', lang('App.admin_msg_wall_category_not_found'));
        }

        $newStatus = ($category['status'] ?? 'active') === 'active' ? 'inactive' : 'active';
        $model->update((int) $id, ['status' => $newStatus]);
        AdminActivityLogModel::log('Toggled Wall Category', 'Wall Categories', $id, "Status → {$newStatus}");

        return redirect()->back()->with('success', lang('App.admin_msg_wall_category_status_changed', [$newStatus]));
    }

    /**
     * @return array<string,mixed>
     */
    private function parseFormData(): array
    {
        $color = strtolower(trim((string) $this->request->getPost('color')));
        $allowedColors = ['emerald', 'blue', 'rose', 'amber', 'indigo', 'teal', 'sky', 'violet', 'orange', 'slate'];
        if (! in_array($color, $allowedColors, true)) {
            $color = 'emerald';
        }

        return [
            'name_en'       => trim((string) $this->request->getPost('name_en')),
            'name_ur'       => trim((string) $this->request->getPost('name_ur')),
            'icon'          => trim((string) $this->request->getPost('icon')) ?: 'user',
            'color'         => $color,
            'display_order' => (int) ($this->request->getPost('display_order') ?? 0),
            'status'        => $this->request->getPost('status') === 'inactive' ? 'inactive' : 'active',
        ];
    }

    private function uniqueWallCategorySlug(string $nameEn, int $id): string
    {
        helper('seo');
        $db   = \Config\Database::connect();
        $base = seo_base_slug($nameEn);
        if ($base === '') {
            $base = 'wall-category-' . $id;
        }

        $row = $db->table('wall_categories')
            ->select('id')
            ->where('slug', $base)
            ->get()
            ->getRowArray();

        if ($row && (int) $row['id'] !== $id) {
            $base .= '-' . $id;
        }

        return $base;
    }

    /**
     * @return array<int,int>
     */
    private function personalityCountsByCategory(): array
    {
        $db  = \Config\Database::connect();
        $map = [];

        // Primary category on wall table
        $primary = $db->table('wall_of_kot_sultan')
            ->select('category_id, COUNT(*) AS total')
            ->where('category_id IS NOT NULL', null, false)
            ->where('category_id >', 0)
            ->groupBy('category_id')
            ->get()
            ->getResultArray();
        foreach ($primary as $row) {
            $cid = (int) $row['category_id'];
            $map[$cid] = ($map[$cid] ?? 0) + (int) $row['total'];
        }

        // Also count pivot links (unique wall ids per category)
        if ($db->tableExists('wall_entry_categories')) {
            $pivot = $db->table('wall_entry_categories')
                ->select('category_id, COUNT(DISTINCT wall_id) AS total')
                ->groupBy('category_id')
                ->get()
                ->getResultArray();
            foreach ($pivot as $row) {
                $cid = (int) $row['category_id'];
                // Prefer max so we don't double-count the same personality
                $map[$cid] = max($map[$cid] ?? 0, (int) $row['total']);
            }
        }

        return $map;
    }

    private function personalityCountForCategory(int $categoryId): int
    {
        if ($categoryId < 1) {
            return 0;
        }
        $db = \Config\Database::connect();
        $primary = (new WallModel())->where('category_id', $categoryId)->countAllResults();
        $pivot = 0;
        if ($db->tableExists('wall_entry_categories')) {
            $pivot = (int) $db->table('wall_entry_categories')
                ->where('category_id', $categoryId)
                ->countAllResults();
        }

        return max($primary, $pivot);
    }
}
