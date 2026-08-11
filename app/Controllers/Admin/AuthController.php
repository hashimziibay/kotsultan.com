<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminUserModel;
use App\Models\AdminActivityLogModel;

class AuthController extends BaseController
{
    public function login()
    {
        $session = session();
        if ($session->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('admin/login', [
            'title' => lang('App.admin_login_title'),
        ]);
    }

    public function attemptLogin()
    {
        $session = session();
        $cache   = \Config\Services::cache();
        $ip      = $this->request->getIPAddress();
        $cacheKey = 'admin_login_attempts_' . md5($ip);
        // Rate-limit only in production so local/dev lockouts are easy to clear.
        $rateLimit = ENVIRONMENT === 'production';

        if ($rateLimit) {
            $attempts = (int) $cache->get($cacheKey);
            if ($attempts >= 5) {
                return redirect()->back()->with('error', lang('App.admin_msg_lockout'));
            }
        }

        $login    = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if (empty($login) || empty($password)) {
            return redirect()->back()->with('error', lang('App.admin_msg_credentials_required'));
        }

        $adminModel = new AdminUserModel();
        $user       = $adminModel->verifyCredentials($login, $password);

        if (!$user) {
            if ($rateLimit) {
                $attempts = (int) $cache->get($cacheKey);
                $cache->save($cacheKey, $attempts + 1, 900); // Lockout for 15 mins (900s)
            }
            return redirect()->back()->with('error', lang('App.admin_msg_invalid_credentials'));
        }

        // Reset rate limit attempts
        $cache->delete($cacheKey);

        // Set session
        $session->set([
            'admin_id'             => $user['id'],
            'admin_username'       => $user['username'],
            'admin_email'          => $user['email'],
            'admin_role'           => $user['role'],
            'must_change_password' => (int) $user['must_change_password'],
            'admin_logged_in'      => true,
        ]);

        AdminActivityLogModel::log('Admin Logged In', 'Auth', $user['id'], 'User logged in successfully');

        if ($user['must_change_password']) {
            return redirect()->to(base_url('admin/change-password'))->with('warning', lang('App.admin_msg_temp_password'));
        }

        return redirect()->to(base_url('admin/dashboard'))->with('success', lang('App.admin_msg_welcome_back', [$user['username']]));
    }

    public function changePassword()
    {
        return view('admin/change_password', [
            'title' => lang('App.admin_change_temp_password'),
        ]);
    }

    public function updatePassword()
    {
        $session = session();
        $adminId = $session->get('admin_id');

        $rules = [
            'new_password'     => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $newPassword = (string) $this->request->getPost('new_password');
        
        if ($newPassword === 'Admin@12345') {
            return redirect()->back()->with('error', lang('App.admin_msg_default_password'));
        }

        $adminModel = new AdminUserModel();
        $adminModel->updatePassword($adminId, $newPassword);

        // Clear flag in session
        $session->set('must_change_password', 0);

        AdminActivityLogModel::log('Changed Password', 'Auth', $adminId, 'Updated temporary password to permanent password');

        return redirect()->to(base_url('admin/dashboard'))->with('success', lang('App.admin_msg_password_updated'));
    }

    public function logout()
    {
        $session = session();
        if ($session->get('admin_id')) {
            AdminActivityLogModel::log('Admin Logged Out', 'Auth', $session->get('admin_id'), 'User logged out');
        }

        $session->destroy();
        return redirect()->to(base_url('admin/login'))->with('success', lang('App.admin_msg_logged_out'));
    }
}
