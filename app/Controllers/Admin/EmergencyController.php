<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EmergencyModel;
use App\Models\AdminActivityLogModel;

class EmergencyController extends BaseController
{
    public function index()
    {
        $emergencyModel = new EmergencyModel();
        
        $query    = trim((string) $this->request->getGet('q'));
        $category = $this->request->getGet('category');

        $builder = $emergencyModel->builder();

        if (!empty($category)) {
            $builder->where('category_en', $category)->orWhere('category_ur', $category);
        }

        if (!empty($query)) {
            $builder->groupStart()
                        ->like('department_name_en', $query)
                        ->orLike('department_name_ur', $query)
                        ->orLike('phone_primary', $query)
                        ->orLike('phone_secondary', $query)
                    ->groupEnd();
        }

        $items = $builder->orderBy('display_order', 'ASC')->get()->getResultArray();
        $categories = $emergencyModel->getCategories();

        return view('admin/emergency/index', [
            'title'            => lang('App.admin_page_emergency_management'),
            'pageHeading'      => 'Emergency Numbers & Helplines',
            'contacts'         => $items,
            'categories'       => $categories,
            'query'            => $query,
            'selectedCategory' => $category,
        ]);
    }

    public function create()
    {
        return view('admin/emergency/form', [
            'title'       => lang('App.admin_page_add_emergency'),
            'pageHeading' => lang('App.admin_page_create_emergency'),
            'contact'     => null,
        ]);
    }

    public function store()
    {
        $rules = [
            'department_name_en' => 'required|min_length[2]',
            'phone_primary'      => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $data = [
            'category_en'        => trim((string) $this->request->getPost('category_en')),
            'category_ur'        => trim((string) $this->request->getPost('category_ur')) ?: trim((string) $this->request->getPost('category_en')),
            'department_name_en' => trim((string) $this->request->getPost('department_name_en')),
            'department_name_ur' => trim((string) $this->request->getPost('department_name_ur')) ?: trim((string) $this->request->getPost('department_name_en')),
            'phone_primary'      => trim((string) $this->request->getPost('phone_primary')),
            'phone_secondary'    => trim((string) $this->request->getPost('phone_secondary')),
            'email'              => trim((string) $this->request->getPost('email')),
            'address_en'         => trim((string) $this->request->getPost('address_en')),
            'address_ur'         => trim((string) $this->request->getPost('address_ur')),
            'working_hours_en'   => trim((string) $this->request->getPost('working_hours_en')),
            'working_hours_ur'   => trim((string) $this->request->getPost('working_hours_ur')),
            'website'            => trim((string) $this->request->getPost('website')),
            'google_maps'        => trim((string) $this->request->getPost('google_maps')),
            'icon'               => trim((string) $this->request->getPost('icon')) ?: 'phone-call',
            'display_order'      => (int) ($this->request->getPost('display_order') ?? 0),
            'status'             => $this->request->getPost('status') ?? 'active',
        ];

        $emergencyModel = new EmergencyModel();
        $id             = $emergencyModel->insert($data);

        AdminActivityLogModel::log('Created Emergency Contact', 'Emergency Numbers', $id, "Created contact {$data['department_name_en']}");

        return redirect()->to(base_url('admin/emergency-numbers'))->with('success', lang('App.admin_msg_emergency_created'));
    }

    public function edit($id)
    {
        $emergencyModel = new EmergencyModel();
        $contact        = $emergencyModel->find($id);

        if (!$contact) {
            return redirect()->to(base_url('admin/emergency-numbers'))->with('error', lang('App.admin_msg_emergency_not_found'));
        }

        return view('admin/emergency/form', [
            'title'       => lang('App.admin_page_edit_emergency_title', [$id]),
            'pageHeading' => lang('App.admin_page_edit_emergency', [($contact['department_name_en'] ?: $contact['department_name_ur'])]),
            'contact'     => $contact,
        ]);
    }

    public function update($id)
    {
        $emergencyModel = new EmergencyModel();
        $contact        = $emergencyModel->find($id);

        if (!$contact) {
            return redirect()->to(base_url('admin/emergency-numbers'))->with('error', lang('App.admin_msg_emergency_not_found'));
        }

        $rules = [
            'department_name_en' => 'required|min_length[2]',
            'phone_primary'      => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $data = [
            'category_en'        => trim((string) $this->request->getPost('category_en')),
            'category_ur'        => trim((string) $this->request->getPost('category_ur')),
            'department_name_en' => trim((string) $this->request->getPost('department_name_en')),
            'department_name_ur' => trim((string) $this->request->getPost('department_name_ur')),
            'phone_primary'      => trim((string) $this->request->getPost('phone_primary')),
            'phone_secondary'    => trim((string) $this->request->getPost('phone_secondary')),
            'email'              => trim((string) $this->request->getPost('email')),
            'address_en'         => trim((string) $this->request->getPost('address_en')),
            'address_ur'         => trim((string) $this->request->getPost('address_ur')),
            'working_hours_en'   => trim((string) $this->request->getPost('working_hours_en')),
            'working_hours_ur'   => trim((string) $this->request->getPost('working_hours_ur')),
            'website'            => trim((string) $this->request->getPost('website')),
            'google_maps'        => trim((string) $this->request->getPost('google_maps')),
            'icon'               => trim((string) $this->request->getPost('icon')) ?: 'phone-call',
            'display_order'      => (int) ($this->request->getPost('display_order') ?? 0),
            'status'             => $this->request->getPost('status') ?? 'active',
        ];

        $emergencyModel->update($id, $data);

        AdminActivityLogModel::log('Updated Emergency Contact', 'Emergency Numbers', $id, "Updated contact {$data['department_name_en']}");

        return redirect()->to(base_url('admin/emergency-numbers'))->with('success', lang('App.admin_msg_emergency_updated'));
    }

    public function delete($id)
    {
        $emergencyModel = new EmergencyModel();
        $contact        = $emergencyModel->find($id);

        if ($contact) {
            $emergencyModel->delete($id);
            AdminActivityLogModel::log('Deleted Emergency Contact', 'Emergency Numbers', $id, "Deleted contact {$contact['department_name_en']}");
            return redirect()->to(base_url('admin/emergency-numbers'))->with('success', lang('App.admin_msg_emergency_deleted'));
        }

        return redirect()->to(base_url('admin/emergency-numbers'))->with('error', lang('App.admin_msg_contact_not_found'));
    }

    public function toggle($id)
    {
        $emergencyModel = new EmergencyModel();
        $contact        = $emergencyModel->find($id);

        if ($contact) {
            $newStatus = ($contact['status'] === 'active') ? 'inactive' : 'active';
            $emergencyModel->update($id, ['status' => $newStatus]);
            AdminActivityLogModel::log('Toggled Emergency Contact Status', 'Emergency Numbers', $id, "Status changed to $newStatus");
            return redirect()->back()->with('success', lang('App.admin_msg_entry_status_changed', [$newStatus]));
        }

        return redirect()->back()->with('error', lang('App.admin_msg_contact_not_found'));
    }
}
