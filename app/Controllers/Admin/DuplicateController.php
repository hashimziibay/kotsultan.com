<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BusinessModel;
use App\Models\AdminActivityLogModel;

class DuplicateController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Phone Duplicates
        $phoneDups = $db->query("SELECT phone, COUNT(*) as cnt FROM businesses WHERE phone IS NOT NULL AND phone != '' GROUP BY phone HAVING cnt > 1 LIMIT 30")->getResultArray();

        $duplicateGroups = [];

        foreach ($phoneDups as $p) {
            $phone = $p['phone'];
            $items = $db->table('businesses')
                        ->select('businesses.*, categories.name_en as cat_en')
                        ->join('categories', 'categories.id = businesses.category_id', 'left')
                        ->where('phone', $phone)
                        ->get()->getResultArray();
            $duplicateGroups[] = [
                'type'   => 'Phone Number Duplicate (' . $phone . ')',
                'items'  => $items,
            ];
        }

        // 2. Name Duplicates
        $nameDups = $db->query("SELECT name_en, COUNT(*) as cnt FROM businesses WHERE name_en IS NOT NULL AND name_en != '' GROUP BY name_en HAVING cnt > 1 LIMIT 20")->getResultArray();

        foreach ($nameDups as $n) {
            $name = $n['name_en'];
            $items = $db->table('businesses')
                        ->select('businesses.*, categories.name_en as cat_en')
                        ->join('categories', 'categories.id = businesses.category_id', 'left')
                        ->where('businesses.name_en', $name)
                        ->get()->getResultArray();
            $duplicateGroups[] = [
                'type'   => 'Identical Name (' . $name . ')',
                'items'  => $items,
            ];
        }

        return view('admin/duplicates/index', [
            'title'           => lang('App.admin_duplicates_title'),
            'pageHeading'     => lang('App.admin_page_duplicate_heading'),
            'duplicateGroups' => $duplicateGroups,
        ]);
    }

    public function merge()
    {
        $masterId = (int) $this->request->getPost('master_id');
        $slaveId  = (int) $this->request->getPost('slave_id');

        if (!$masterId || !$slaveId || $masterId === $slaveId) {
            return redirect()->back()->with('error', lang('App.admin_msg_merge_invalid'));
        }

        $businessModel = new BusinessModel();
        $master = $businessModel->find($masterId);
        $slave  = $businessModel->find($slaveId);

        if (!$master || !$slave) {
            return redirect()->back()->with('error', lang('App.admin_msg_merge_not_found'));
        }

        // Merge missing fields from slave to master
        $updatedData = [];
        $fields = ['name_ur', 'address_en', 'address_ur', 'description_en', 'description_ur', 'phone', 'whatsapp', 'email', 'website', 'image'];

        foreach ($fields as $f) {
            if (empty($master[$f]) && !empty($slave[$f])) {
                $updatedData[$f] = $slave[$f];
            }
        }

        if (!empty($updatedData)) {
            $businessModel->update($masterId, $updatedData);
        }

        // Safe deletion of slave record
        $businessModel->delete($slaveId);

        AdminActivityLogModel::log('Merged Duplicate Businesses', 'Duplicates', $masterId, "Merged slave ID $slaveId into master ID $masterId");

        return redirect()->to(base_url('admin/duplicates'))->with('success', lang('App.admin_msg_merge_success', [$slaveId, $masterId]));
    }
}
