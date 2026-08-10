<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminActivityLogModel;

class ImageController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Collect uploaded images from public/uploads/ and public/images/
        $dirs = [
            FCPATH . 'uploads/businesses',
            FCPATH . 'uploads/wall',
            FCPATH . 'uploads',
            FCPATH . 'images',
        ];

        $imagesList = [];

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file) {
                    if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif'])) {
                        $fullPath = $dir . '/' . $file;
                        $relativePath = str_replace(FCPATH, '', $fullPath);
                        $relativePath = str_replace('\\', '/', $relativePath);

                        // Check DB reference count
                        $bizCount  = $db->table('businesses')->where('image', $relativePath)->countAllResults();
                        $wallCount = $db->table('wall_of_kot_sultan')->where('photo', $relativePath)->countAllResults();
                        $totalRefs = $bizCount + $wallCount;

                        $imagesList[] = [
                            'filename'     => $file,
                            'path'         => $relativePath,
                            'size'         => filesize($fullPath),
                            'mtime'        => filemtime($fullPath),
                            'references'   => $totalRefs,
                        ];
                    }
                }
            }
        }

        // Sort newest first
        usort($imagesList, fn($a, $b) => $b['mtime'] <=> $a['mtime']);

        return view('admin/images/index', [
            'title'       => lang('App.admin_page_image_manager'),
            'pageHeading' => lang('App.admin_images_title'),
            'images'      => $imagesList,
        ]);
    }

    public function upload()
    {
        $file = $this->request->getFile('image_file');
        $module = $this->request->getPost('module') ?? 'businesses';

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return redirect()->back()->with('error', lang('App.admin_msg_image_type'));
            }

            $targetDir = FCPATH . 'uploads/' . $module;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $newName = $file->getRandomName();
            $file->move($targetDir, $newName);
            $relPath = 'uploads/' . $module . '/' . $newName;

            AdminActivityLogModel::log('Uploaded Image', 'Image Manager', null, "Uploaded $relPath");

            return redirect()->to(base_url('admin/images'))->with('success', lang('App.admin_msg_image_uploaded', [$relPath]));
        }

        return redirect()->back()->with('error', lang('App.admin_msg_image_upload_failed'));
    }

    public function delete()
    {
        $relPath = trim((string) $this->request->getPost('path'));
        $fullPath = FCPATH . $relPath;

        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', lang('App.admin_msg_image_not_found'));
        }

        $db = \Config\Database::connect();
        $bizCount  = $db->table('businesses')->where('image', $relPath)->countAllResults();
        $wallCount = $db->table('wall_of_kot_sultan')->where('photo', $relPath)->countAllResults();
        $totalRefs = $bizCount + $wallCount;

        if ($totalRefs > 0) {
            return redirect()->back()->with('error', lang('App.admin_msg_image_in_use', [$relPath, $totalRefs]));
        }

        unlink($fullPath);
        AdminActivityLogModel::log('Deleted Image', 'Image Manager', null, "Deleted unreferenced image $relPath");

        return redirect()->to(base_url('admin/images'))->with('success', lang('App.admin_msg_image_deleted'));
    }
}
