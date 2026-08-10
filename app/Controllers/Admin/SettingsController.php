<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminUserModel;
use App\Models\AdminActivityLogModel;

class SettingsController extends BaseController
{
    public function index()
    {
        $session = session();
        $adminId = $session->get('admin_id');

        $adminModel = new AdminUserModel();
        $admin      = $adminModel->find($adminId);

        return view('admin/settings/index', [
            'title'       => lang('App.admin_page_settings_title'),
            'pageHeading' => lang('App.admin_page_settings'),
            'admin'       => $admin,
        ]);
    }

    public function updatePassword()
    {
        $session = session();
        $adminId = $session->get('admin_id');

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword     = (string) $this->request->getPost('new_password');

        $adminModel = new AdminUserModel();
        $admin      = $adminModel->find($adminId);

        if (!password_verify($currentPassword, $admin['password_hash'])) {
            return redirect()->back()->with('error', lang('App.admin_msg_current_password_incorrect'));
        }

        $adminModel->updatePassword($adminId, $newPassword);
        AdminActivityLogModel::log('Changed Password', 'Settings', $adminId, 'Admin updated password');

        return redirect()->to(base_url('admin/settings'))->with('success', lang('App.admin_msg_password_updated'));
    }

    public function activityLogs()
    {
        $logModel = new AdminActivityLogModel();
        
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 30;
        $offset  = ($page - 1) * $perPage;

        $total = $logModel->countAllResults(false);
        $logs  = $logModel->orderBy('id', 'DESC')->limit($perPage, $offset)->findAll();

        return view('admin/activity_logs', [
            'title'       => lang('App.admin_page_activity_title'),
            'pageHeading' => lang('App.admin_page_activity'),
            'logs'        => $logs,
            'total'       => $total,
            'page'        => $page,
            'perPage'     => $perPage,
            'totalPages'  => (int) ceil($total / $perPage),
        ]);
    }
}
