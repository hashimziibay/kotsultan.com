<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VillageModel;
use App\Models\BusinessModel;
use App\Models\AdminActivityLogModel;

class VillageController extends BaseController
{
    public function index()
    {
        $villageModel  = new VillageModel();
        $businessModel = new BusinessModel();

        $villages = $villageModel->orderBy('name_en', 'ASC')->findAll();

        $db = \Config\Database::connect();
        $counts = $db->query("SELECT village_id, COUNT(*) as total FROM businesses WHERE village_id IS NOT NULL GROUP BY village_id")->getResultArray();
        $countsMap = [];
        foreach ($counts as $c) {
            $countsMap[$c['village_id']] = $c['total'];
        }

        return view('admin/villages/index', [
            'title'       => lang('App.admin_page_village_management'),
            'pageHeading' => lang('App.admin_villages_title'),
            'villages'    => $villages,
            'countsMap'   => $countsMap,
        ]);
    }

    public function create()
    {
        return view('admin/villages/form', [
            'title'       => lang('App.admin_add_village'),
            'pageHeading' => lang('App.admin_page_create_village'),
            'village'     => null,
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
            'name_en' => trim((string) $this->request->getPost('name_en')),
            'name_ur' => trim((string) $this->request->getPost('name_ur')),
            'slug'    => url_title(trim((string) $this->request->getPost('name_en')), '-', true),
            'status'  => $this->request->getPost('status') ?? 'active',
        ];

        $villageModel = new VillageModel();
        $id = $villageModel->insert($data);

        AdminActivityLogModel::log('Created Village', 'Villages', $id, "Created village {$data['name_en']}");

        return redirect()->to(base_url('admin/villages'))->with('success', lang('App.admin_msg_village_created'));
    }

    public function edit($id)
    {
        $villageModel = new VillageModel();
        $village      = $villageModel->find($id);

        if (!$village) {
            return redirect()->to(base_url('admin/villages'))->with('error', lang('App.admin_msg_village_not_found'));
        }

        return view('admin/villages/form', [
            'title'       => lang('App.admin_page_edit_village', ['#' . $id]),
            'pageHeading' => lang('App.admin_page_edit_village', [$village['name_en']]),
            'village'     => $village,
        ]);
    }

    public function update($id)
    {
        $villageModel = new VillageModel();
        $village      = $villageModel->find($id);

        if (!$village) {
            return redirect()->to(base_url('admin/villages'))->with('error', lang('App.admin_msg_village_not_found'));
        }

        $rules = [
            'name_en' => 'required|min_length[2]',
            'name_ur' => 'required|min_length[2]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $data = [
            'name_en' => trim((string) $this->request->getPost('name_en')),
            'name_ur' => trim((string) $this->request->getPost('name_ur')),
            'status'  => $this->request->getPost('status') ?? 'active',
        ];

        $villageModel->update($id, $data);

        AdminActivityLogModel::log('Updated Village', 'Villages', $id, "Updated village {$data['name_en']}");

        return redirect()->to(base_url('admin/villages'))->with('success', lang('App.admin_msg_village_updated'));
    }

    public function delete($id)
    {
        $villageModel  = new VillageModel();
        $businessModel = new BusinessModel();

        $village = $villageModel->find($id);
        if (!$village) {
            return redirect()->to(base_url('admin/villages'))->with('error', lang('App.admin_msg_village_not_found'));
        }

        $connected = $businessModel->where('village_id', $id)->countAllResults();
        if ($connected > 0) {
            return redirect()->to(base_url('admin/villages'))->with('error', lang('App.admin_msg_village_in_use', [$village['name_en'], $connected]));
        }

        $villageModel->delete($id);
        AdminActivityLogModel::log('Deleted Village', 'Villages', $id, "Deleted village {$village['name_en']}");

        return redirect()->to(base_url('admin/villages'))->with('success', lang('App.admin_msg_village_deleted'));
    }
}
