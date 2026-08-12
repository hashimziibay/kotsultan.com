<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NavLinkModel;
use App\Models\AdminActivityLogModel;

class NavLinkController extends BaseController
{
    protected $navLinkModel;
    protected $activityLogModel;

    public function __construct()
    {
        $this->navLinkModel = new NavLinkModel();
        $this->activityLogModel = new AdminActivityLogModel();
    }

    public function index()
    {
        $data = [
            'title' => lang('App.admin_menu_management'),
            'links' => $this->navLinkModel->orderBy('sort_order', 'ASC')->findAll(),
        ];

        return view('admin/nav_links/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => lang('App.admin_add_menu_item'),
        ];

        return view('admin/nav_links/form', $data);
    }

    public function store()
    {
        $rules = [
            'title_en' => 'required|max_length[255]',
            'title_ur' => 'required|max_length[255]',
            'url'      => 'required|max_length[255]|regex_match[/^(?!javascript:).*/i]',
            'sort_order'=> 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $saveData = [
            'title_en'   => $this->request->getPost('title_en'),
            'title_ur'   => $this->request->getPost('title_ur'),
            'url'        => trim($this->request->getPost('url')),
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'status'     => $this->request->getPost('status') === 'active' ? 'active' : 'inactive',
        ];

        $this->navLinkModel->insert($saveData);
        $insertId = $this->navLinkModel->getInsertID();

        $this->activityLogModel->logAction(
            session('admin_id'),
            'CREATE',
            "Added menu item: {$saveData['title_en']}",
            'nav_links',
            $insertId
        );

        return redirect()->to('admin/nav-links')->with('success', lang('App.admin_menu_added'));
    }

    public function edit($id)
    {
        $link = $this->navLinkModel->find($id);

        if (!$link) {
            return redirect()->to('admin/nav-links')->with('error', lang('App.admin_menu_not_found'));
        }

        $data = [
            'title' => lang('App.admin_edit_menu_item'),
            'link'  => $link,
        ];

        return view('admin/nav_links/form', $data);
    }

    public function update($id)
    {
        $link = $this->navLinkModel->find($id);

        if (!$link) {
            return redirect()->to('admin/nav-links')->with('error', lang('App.admin_menu_not_found'));
        }

        $rules = [
            'title_en' => 'required|max_length[255]',
            'title_ur' => 'required|max_length[255]',
            'url'      => 'required|max_length[255]|regex_match[/^(?!javascript:).*/i]',
            'sort_order'=> 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'title_en'   => $this->request->getPost('title_en'),
            'title_ur'   => $this->request->getPost('title_ur'),
            'url'        => trim($this->request->getPost('url')),
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'status'     => $this->request->getPost('status') === 'active' ? 'active' : 'inactive',
        ];

        $this->navLinkModel->update($id, $updateData);

        $this->activityLogModel->logAction(
            session('admin_id'),
            'UPDATE',
            "Updated menu item: {$updateData['title_en']}",
            'nav_links',
            $id
        );

        return redirect()->to('admin/nav-links')->with('success', lang('App.admin_menu_updated'));
    }

    public function toggle($id)
    {
        $link = $this->navLinkModel->find($id);

        if (!$link) {
            return redirect()->to('admin/nav-links')->with('error', lang('App.admin_menu_not_found'));
        }

        $newStatus = $link['status'] === 'active' ? 'inactive' : 'active';
        $this->navLinkModel->update($id, ['status' => $newStatus]);

        $this->activityLogModel->logAction(
            session('admin_id'),
            'UPDATE',
            "Toggled menu item status to {$newStatus}: {$link['title_en']}",
            'nav_links',
            $id
        );

        return redirect()->to('admin/nav-links')->with('success', lang('App.admin_menu_status_updated', [$newStatus]));
    }

    public function delete($id)
    {
        $link = $this->navLinkModel->find($id);

        if (!$link) {
            return redirect()->to('admin/nav-links')->with('error', lang('App.admin_menu_not_found'));
        }

        $this->navLinkModel->delete($id);

        $this->activityLogModel->logAction(
            session('admin_id'),
            'DELETE',
            "Deleted menu item: {$link['title_en']}",
            'nav_links',
            $id
        );

        return redirect()->to('admin/nav-links')->with('success', lang('App.admin_menu_deleted'));
    }

    public function reorder()
    {
        $payload = $this->request->getJSON(true);
        $order   = is_array($payload) ? ($payload['order'] ?? null) : null;
        if (! is_array($order)) {
            $order = $this->request->getPost('order');
        }
        if (! is_array($order)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('App.admin_invalid_data')]);
        }

        foreach ($order as $index => $id) {
            $id = (int) $id;
            if ($id <= 0) {
                continue;
            }
            $this->navLinkModel->skipValidation(true)->update($id, ['sort_order' => $index + 1]);
        }

        $this->activityLogModel->logAction(
            session('admin_id'),
            'UPDATE',
            'Reordered menu items',
            'nav_links',
            0
        );

        return $this->response->setJSON(['status' => 'success', 'message' => lang('App.admin_order_updated')]);
    }
}
