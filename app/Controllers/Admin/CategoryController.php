<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\BusinessModel;
use App\Models\AdminActivityLogModel;

class CategoryController extends BaseController
{
    public function index()
    {
        $categoryModel = new CategoryModel();
        $businessModel = new BusinessModel();

        $categories = $categoryModel->orderBy('display_order', 'ASC')->orderBy('name_en', 'ASC')->findAll();
        $counts     = $businessModel->getCategoryCounts();
        $countsMap  = [];
        foreach ($counts as $c) {
            $countsMap[$c['category_id']] = $c['total'];
        }

        return view('admin/categories/index', [
            'title'       => lang('App.admin_page_category_management'),
            'pageHeading' => lang('App.admin_categories_title'),
            'categories'  => $categories,
            'countsMap'   => $countsMap,
        ]);
    }

    public function create()
    {
        return view('admin/categories/form', [
            'title'       => lang('App.admin_add_category'),
            'pageHeading' => lang('App.admin_page_create_category'),
            'category'    => null,
        ]);
    }

    public function store()
    {
        $rules = [
            'name_en' => 'required|min_length[2]',
            'name_ur' => 'required|min_length[2]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $data = [
            'name_en'       => trim((string) $this->request->getPost('name_en')),
            'name_ur'       => trim((string) $this->request->getPost('name_ur')),
            'slug'          => url_title(trim((string) $this->request->getPost('name_en')), '-', true),
            'icon'          => trim((string) $this->request->getPost('icon')) ?: 'folder',
            'display_order' => (int) ($this->request->getPost('display_order') ?? 0),
            'status'        => $this->request->getPost('status') ?? 'active',
        ];

        $categoryModel = new CategoryModel();
        $id = $categoryModel->insert($data);

        AdminActivityLogModel::log('Created Category', 'Categories', $id, "Created category {$data['name_en']}");

        return redirect()->to(base_url('admin/categories'))->with('success', lang('App.admin_msg_category_created'));
    }

    public function edit($id)
    {
        $categoryModel = new CategoryModel();
        $category      = $categoryModel->find($id);

        if (!$category) {
            return redirect()->to(base_url('admin/categories'))->with('error', lang('App.admin_msg_category_not_found'));
        }

        return view('admin/categories/form', [
            'title'       => lang('App.admin_page_edit_category', ['#' . $id]),
            'pageHeading' => lang('App.admin_page_edit_category', [$category['name_en']]),
            'category'    => $category,
        ]);
    }

    public function update($id)
    {
        $categoryModel = new CategoryModel();
        $category      = $categoryModel->find($id);

        if (!$category) {
            return redirect()->to(base_url('admin/categories'))->with('error', lang('App.admin_msg_category_not_found'));
        }

        $rules = [
            'name_en' => 'required|min_length[2]',
            'name_ur' => 'required|min_length[2]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $data = [
            'name_en'       => trim((string) $this->request->getPost('name_en')),
            'name_ur'       => trim((string) $this->request->getPost('name_ur')),
            'icon'          => trim((string) $this->request->getPost('icon')) ?: 'folder',
            'display_order' => (int) ($this->request->getPost('display_order') ?? 0),
            'status'        => $this->request->getPost('status') ?? 'active',
        ];

        $categoryModel->update($id, $data);

        AdminActivityLogModel::log('Updated Category', 'Categories', $id, "Updated category {$data['name_en']}");

        return redirect()->to(base_url('admin/categories'))->with('success', lang('App.admin_msg_category_updated'));
    }

    public function delete($id)
    {
        $categoryModel = new CategoryModel();
        $businessModel = new BusinessModel();

        $category = $categoryModel->find($id);
        if (!$category) {
            return redirect()->to(base_url('admin/categories'))->with('error', lang('App.admin_msg_category_not_found'));
        }

        $connected = $businessModel->where('category_id', $id)->countAllResults();
        if ($connected > 0) {
            return redirect()->to(base_url('admin/categories'))->with('error', lang('App.admin_msg_category_in_use', [$category['name_en'], $connected]));
        }

        $categoryModel->delete($id);
        AdminActivityLogModel::log('Deleted Category', 'Categories', $id, "Deleted category {$category['name_en']}");

        return redirect()->to(base_url('admin/categories'))->with('success', lang('App.admin_msg_category_deleted'));
    }

    public function toggle($id)
    {
        $categoryModel = new CategoryModel();
        $category      = $categoryModel->find($id);

        if ($category) {
            $newStatus = ($category['status'] === 'active') ? 'inactive' : 'active';
            $categoryModel->update($id, ['status' => $newStatus]);
            AdminActivityLogModel::log('Toggled Category Status', 'Categories', $id, "Status changed to $newStatus");
            return redirect()->back()->with('success', lang('App.admin_msg_category_status_changed', [$newStatus]));
        }

        return redirect()->back()->with('error', lang('App.admin_msg_category_not_found'));
    }
}
