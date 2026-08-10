<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AreaModel;
use App\Models\BusinessModel;
use App\Models\AdminActivityLogModel;

class AreaController extends BaseController
{
    public function index()
    {
        $areaModel     = new AreaModel();
        $businessModel = new BusinessModel();

        $areas = $areaModel->orderBy('name_en', 'ASC')->findAll();
        
        $db = \Config\Database::connect();
        $counts = $db->query("SELECT area_id, COUNT(*) as total FROM businesses WHERE area_id IS NOT NULL GROUP BY area_id")->getResultArray();
        $countsMap = [];
        foreach ($counts as $c) {
            $countsMap[$c['area_id']] = $c['total'];
        }

        return view('admin/areas/index', [
            'title'       => lang('App.admin_page_area_management'),
            'pageHeading' => lang('App.admin_areas_title'),
            'areas'       => $areas,
            'countsMap'   => $countsMap,
        ]);
    }

    public function create()
    {
        return view('admin/areas/form', [
            'title'       => lang('App.admin_add_area'),
            'pageHeading' => lang('App.admin_page_create_area'),
            'area'        => null,
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

        $areaModel = new AreaModel();
        $id = $areaModel->insert($data);

        AdminActivityLogModel::log('Created Area', 'Areas', $id, "Created area {$data['name_en']}");

        return redirect()->to(base_url('admin/areas'))->with('success', lang('App.admin_msg_area_created'));
    }

    public function edit($id)
    {
        $areaModel = new AreaModel();
        $area      = $areaModel->find($id);

        if (!$area) {
            return redirect()->to(base_url('admin/areas'))->with('error', lang('App.admin_msg_area_not_found'));
        }

        return view('admin/areas/form', [
            'title'       => lang('App.admin_page_edit_area', ['#' . $id]),
            'pageHeading' => lang('App.admin_page_edit_area', [$area['name_en']]),
            'area'        => $area,
        ]);
    }

    public function update($id)
    {
        $areaModel = new AreaModel();
        $area      = $areaModel->find($id);

        if (!$area) {
            return redirect()->to(base_url('admin/areas'))->with('error', lang('App.admin_msg_area_not_found'));
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

        $areaModel->update($id, $data);

        AdminActivityLogModel::log('Updated Area', 'Areas', $id, "Updated area {$data['name_en']}");

        return redirect()->to(base_url('admin/areas'))->with('success', lang('App.admin_msg_area_updated'));
    }

    public function delete($id)
    {
        $areaModel     = new AreaModel();
        $businessModel = new BusinessModel();

        $area = $areaModel->find($id);
        if (!$area) {
            return redirect()->to(base_url('admin/areas'))->with('error', lang('App.admin_msg_area_not_found'));
        }

        $connected = $businessModel->where('area_id', $id)->countAllResults();
        if ($connected > 0) {
            return redirect()->to(base_url('admin/areas'))->with('error', lang('App.admin_msg_area_in_use', [$area['name_en'], $connected]));
        }

        $areaModel->delete($id);
        AdminActivityLogModel::log('Deleted Area', 'Areas', $id, "Deleted area {$area['name_en']}");

        return redirect()->to(base_url('admin/areas'))->with('success', lang('App.admin_msg_area_deleted'));
    }
}
