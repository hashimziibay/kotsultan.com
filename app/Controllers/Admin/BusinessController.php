<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BusinessModel;
use App\Models\CategoryModel;
use App\Models\AreaModel;
use App\Models\VillageModel;
use App\Models\AdminActivityLogModel;

class BusinessController extends BaseController
{
    public function index()
    {
        $businessModel = new BusinessModel();
        $categoryModel = new CategoryModel();
        $areaModel     = new AreaModel();
        $villageModel  = new VillageModel();

        $query      = trim((string) $this->request->getGet('q'));
        $category   = $this->request->getGet('category');
        $area       = $this->request->getGet('area');
        $village    = $this->request->getGet('village');
        $status     = $this->request->getGet('status');
        $perPage    = max(10, (int) ($this->request->getGet('per_page') ?? 20));
        $page       = max(1, (int) ($this->request->getGet('page') ?? 1));

        $builder = $businessModel->select('businesses.*, categories.name_en as cat_en, categories.name_ur as cat_ur, areas.name_en as area_en_name, villages.name_en as village_en_name')
                                 ->join('categories', 'categories.id = businesses.category_id', 'left')
                                 ->join('areas', 'areas.id = businesses.area_id', 'left')
                                 ->join('villages', 'villages.id = businesses.village_id', 'left');

        if (!empty($category)) {
            $builder->where('businesses.category_id', $category);
        }
        if (!empty($area)) {
            $builder->where('businesses.area_id', $area);
        }
        if (!empty($village)) {
            $builder->where('businesses.village_id', $village);
        }
        if (!empty($status)) {
            $builder->where('businesses.status', $status);
        }

        if (!empty($query)) {
            $builder->groupStart()
                        ->like('businesses.name_en', $query)
                        ->orLike('businesses.name_ur', $query)
                        ->orLike('businesses.phone', $query)
                        ->orLike('businesses.address_en', $query)
                        ->orLike('businesses.address_ur', $query)
                    ->groupEnd();
        }

        $total  = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;

        $rows = $builder->orderBy('businesses.id', 'DESC')
                       ->limit($perPage, $offset)
                       ->findAll();

        return view('admin/businesses/index', [
            'title'            => lang('App.admin_business_listings'),
            'pageHeading'      => lang('App.manage_businesses'),
            'businesses'       => $rows,
            'total'            => $total,
            'page'             => $page,
            'perPage'          => $perPage,
            'totalPages'       => (int) ceil($total / $perPage),
            'query'            => $query,
            'selectedCategory' => $category,
            'selectedArea'     => $area,
            'selectedVillage'  => $village,
            'selectedStatus'   => $status,
            'categories'       => $categoryModel->orderBy('name_en', 'ASC')->findAll(),
            'areas'            => $areaModel->orderBy('name_en', 'ASC')->findAll(),
            'villages'         => $villageModel->orderBy('name_en', 'ASC')->findAll(),
        ]);
    }

    public function create()
    {
        $categoryModel = new CategoryModel();
        $areaModel     = new AreaModel();
        $villageModel  = new VillageModel();

        return view('admin/businesses/form', [
            'title'       => lang('App.admin_add_new_business'),
            'pageHeading' => lang('App.admin_page_create_business'),
            'business'    => null,
            'categories'  => $categoryModel->orderBy('name_en', 'ASC')->findAll(),
            'areas'       => $areaModel->orderBy('name_en', 'ASC')->findAll(),
            'villages'    => $villageModel->orderBy('name_en', 'ASC')->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'name_en'     => 'required|min_length[2]',
            'category_id' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $photoPath = $this->handleImageUpload();

        $data = [
            'category_id'      => (int) $this->request->getPost('category_id'),
            'area_id'          => $this->request->getPost('area_id') ? (int) $this->request->getPost('area_id') : null,
            'village_id'       => $this->request->getPost('village_id') ? (int) $this->request->getPost('village_id') : null,
            'name_en'          => trim((string) $this->request->getPost('name_en')),
            'name_ur'          => trim((string) $this->request->getPost('name_ur')) ?: trim((string) $this->request->getPost('name_en')),
            'address_en'       => trim((string) $this->request->getPost('address_en')),
            'address_ur'       => trim((string) $this->request->getPost('address_ur')),
            'description_en'   => trim((string) $this->request->getPost('description_en')),
            'description_ur'   => trim((string) $this->request->getPost('description_ur')),
            'phone'            => trim((string) $this->request->getPost('phone')),
            'whatsapp'         => trim((string) $this->request->getPost('whatsapp')),
            'email'            => trim((string) $this->request->getPost('email')),
            'website'          => trim((string) $this->request->getPost('website')),
            'latitude'         => $this->request->getPost('latitude') ?: null,
            'longitude'        => $this->request->getPost('longitude') ?: null,
            'slug'             => url_title(trim((string) $this->request->getPost('name_en')), '-', true),
            'status'           => $this->request->getPost('status') ?? 'active',
            'image'            => $photoPath ?: null,
        ];

        $businessModel = new BusinessModel();
        $insertId      = $businessModel->insert($data);

        AdminActivityLogModel::log('Created Business', 'Businesses', $insertId, "Created business {$data['name_en']}");

        return redirect()->to(base_url('admin/businesses'))->with('success', lang('App.admin_msg_business_created'));
    }

    public function edit($id)
    {
        $businessModel = new BusinessModel();
        $business      = $businessModel->find($id);

        if (!$business) {
            return redirect()->to(base_url('admin/businesses'))->with('error', lang('App.admin_msg_business_not_found'));
        }

        $categoryModel = new CategoryModel();
        $areaModel     = new AreaModel();
        $villageModel  = new VillageModel();

        return view('admin/businesses/form', [
            'title'       => lang('App.admin_page_edit_business', ['#' . $id]),
            'pageHeading' => lang('App.admin_page_edit_business', [($business['name_en'] ?: $business['name_ur'])]),
            'business'    => $business,
            'categories'  => $categoryModel->orderBy('name_en', 'ASC')->findAll(),
            'areas'       => $areaModel->orderBy('name_en', 'ASC')->findAll(),
            'villages'    => $villageModel->orderBy('name_en', 'ASC')->findAll(),
        ]);
    }

    public function update($id)
    {
        $businessModel = new BusinessModel();
        $business      = $businessModel->find($id);

        if (!$business) {
            return redirect()->to(base_url('admin/businesses'))->with('error', lang('App.admin_msg_business_not_found'));
        }

        $rules = [
            'name_en'     => 'required|min_length[2]',
            'category_id' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $photoPath = $this->handleImageUpload();
        if (!$photoPath) {
            $photoPath = $business['image'];
        }

        $data = [
            'category_id'      => (int) $this->request->getPost('category_id'),
            'area_id'          => $this->request->getPost('area_id') ? (int) $this->request->getPost('area_id') : null,
            'village_id'       => $this->request->getPost('village_id') ? (int) $this->request->getPost('village_id') : null,
            'name_en'          => trim((string) $this->request->getPost('name_en')),
            'name_ur'          => trim((string) $this->request->getPost('name_ur')),
            'address_en'       => trim((string) $this->request->getPost('address_en')),
            'address_ur'       => trim((string) $this->request->getPost('address_ur')),
            'description_en'   => trim((string) $this->request->getPost('description_en')),
            'description_ur'   => trim((string) $this->request->getPost('description_ur')),
            'phone'            => trim((string) $this->request->getPost('phone')),
            'whatsapp'         => trim((string) $this->request->getPost('whatsapp')),
            'email'            => trim((string) $this->request->getPost('email')),
            'website'          => trim((string) $this->request->getPost('website')),
            'latitude'         => $this->request->getPost('latitude') ?: null,
            'longitude'        => $this->request->getPost('longitude') ?: null,
            'status'           => $this->request->getPost('status') ?? 'active',
            'image'            => $photoPath,
        ];

        $businessModel->update($id, $data);

        AdminActivityLogModel::log('Updated Business', 'Businesses', $id, "Updated business {$data['name_en']}");

        return redirect()->to(base_url('admin/businesses'))->with('success', lang('App.admin_msg_business_updated'));
    }

    public function delete($id)
    {
        $businessModel = new BusinessModel();
        $business      = $businessModel->find($id);

        if ($business) {
            $businessModel->delete($id);
            AdminActivityLogModel::log('Deleted Business', 'Businesses', $id, "Deleted business ID $id ({$business['name_en']})");
            return redirect()->to(base_url('admin/businesses'))->with('success', lang('App.admin_msg_business_deleted'));
        }

        return redirect()->to(base_url('admin/businesses'))->with('error', lang('App.admin_msg_business_not_found'));
    }

    public function toggle($id)
    {
        $businessModel = new BusinessModel();
        $business      = $businessModel->find($id);

        if ($business) {
            $newStatus = ($business['status'] === 'active') ? 'inactive' : 'active';
            $businessModel->update($id, ['status' => $newStatus]);
            AdminActivityLogModel::log('Toggled Business Status', 'Businesses', $id, "Status changed to $newStatus");
            return redirect()->back()->with('success', lang('App.admin_msg_status_changed', [$newStatus]));
        }

        return redirect()->back()->with('error', lang('App.admin_msg_business_not_found'));
    }

    private function handleImageUpload(): ?string
    {
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/businesses', $newName);
            return 'uploads/businesses/' . $newName;
        }
        return null;
    }
}
