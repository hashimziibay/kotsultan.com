<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AppUserModel;
use App\Models\BusinessModel;
use App\Models\AdminActivityLogModel;

class AppUsersController extends BaseController
{
    public function index()
    {
        $model = new AppUserModel();
        $q     = trim((string) $this->request->getGet('q'));
        $type  = $this->request->getGet('type');
        $status = $this->request->getGet('status');

        $builder = $model->orderBy('id', 'DESC');
        if ($type === 'user' || $type === 'business') {
            $builder->where('account_type', $type);
        }
        if ($status === 'active' || $status === 'inactive') {
            $builder->where('status', $status);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('name', $q)
                ->orLike('phone', $q)
                ->groupEnd();
        }

        $users = $builder->findAll(200);
        $bizCounts = [];
        if ($users !== []) {
            $ids = array_map(static fn ($u) => (int) $u['id'], $users);
            $rows = (new BusinessModel())
                ->select('user_id, COUNT(*) as c')
                ->whereIn('user_id', $ids)
                ->groupBy('user_id')
                ->findAll();
            foreach ($rows as $row) {
                $bizCounts[(int) $row['user_id']] = (int) $row['c'];
            }
        }

        return view('admin/app_users/index', [
            'title'       => 'App Users & Businesses',
            'pageHeading' => 'App Users & Business Accounts',
            'users'       => $users,
            'bizCounts'   => $bizCounts,
            'query'       => $q,
            'selectedType'=> $type,
            'selectedStatus' => $status,
        ]);
    }

    public function show($id)
    {
        $user = (new AppUserModel())->find($id);
        if (! $user) {
            return redirect()->to(base_url('admin/app-users'))->with('error', 'User not found');
        }

        $businesses = (new BusinessModel())
            ->where('user_id', (int) $id)
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('admin/app_users/show', [
            'title'       => 'App User #' . $id,
            'pageHeading' => $user['name'] ?: ('User #' . $id),
            'user'        => $user,
            'businesses'  => $businesses,
        ]);
    }

    public function toggle($id)
    {
        $model = new AppUserModel();
        $user  = $model->find($id);
        if (! $user) {
            return redirect()->to(base_url('admin/app-users'))->with('error', 'User not found');
        }
        $new = (($user['status'] ?? 'active') === 'active') ? 'inactive' : 'active';
        $update = ['status' => $new];
        if ($new === 'inactive') {
            $update['api_token'] = null;
        }
        $model->update($id, $update);
        AdminActivityLogModel::log('Toggled App User', 'App Users', (int) $id, "Set status {$new}");
        return redirect()->back()->with('success', 'User status updated');
    }

    public function approveBusiness($businessId)
    {
        $bizModel = new BusinessModel();
        $biz = $bizModel->find($businessId);
        if (! $biz) {
            return redirect()->back()->with('error', 'Business not found');
        }
        $bizModel->update((int) $businessId, ['status' => 'active']);
        AdminActivityLogModel::log('Approved Business Listing', 'Businesses', (int) $businessId, 'Approved app-submitted listing');
        return redirect()->back()->with('success', 'Business approved and published');
    }
}
