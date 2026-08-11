<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WallModel;
use App\Models\WallCategoryModel;
use App\Models\WallAttachmentModel;
use App\Models\AdminActivityLogModel;

class WallController extends BaseController
{
    public function index()
    {
        $wallModel         = new WallModel();
        $wallCategoryModel = new WallCategoryModel();

        $query    = trim((string) $this->request->getGet('q'));
        $category = $this->request->getGet('category');

        $builder = $wallModel->select('wall_of_kot_sultan.*, wall_categories.name_en as category_name_en, wall_categories.name_ur as category_name_ur')
                             ->join('wall_categories', 'wall_categories.id = wall_of_kot_sultan.category_id', 'left');

        if (!empty($category)) {
            $builder->where('wall_of_kot_sultan.category_id', $category);
        }

        if (!empty($query)) {
            $builder->groupStart()
                        ->like('wall_of_kot_sultan.name_en', $query)
                        ->orLike('wall_of_kot_sultan.name_ur', $query)
                        ->orLike('wall_of_kot_sultan.profession_en', $query)
                        ->orLike('wall_of_kot_sultan.profession_ur', $query)
                    ->groupEnd();
        }

        $items = $builder->orderBy('wall_of_kot_sultan.featured', 'DESC')
                         ->orderBy('wall_of_kot_sultan.display_order', 'ASC')
                         ->findAll();

        return view('admin/wall/index', [
            'title'            => lang('App.admin_page_wall_management'),
            'pageHeading'      => 'Wall Personalities & Legends',
            'personalities'    => $items,
            'categories'       => $wallCategoryModel->orderBy('display_order', 'ASC')->findAll(),
            'query'            => $query,
            'selectedCategory' => $category,
        ]);
    }

    public function create()
    {
        $wallCategoryModel = new WallCategoryModel();

        return view('admin/wall/form', [
            'title'       => lang('App.admin_page_add_wall'),
            'pageHeading' => lang('App.admin_page_create_personality'),
            'person'      => null,
            'attachments' => [],
            'categories'  => $wallCategoryModel->orderBy('display_order', 'ASC')->findAll(),
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

        $photoPath = $this->handlePhotoUpload();

        $data = [
            'category_id'      => (int) $this->request->getPost('category_id'),
            'name_en'          => trim((string) $this->request->getPost('name_en')),
            'name_ur'          => trim((string) $this->request->getPost('name_ur')) ?: trim((string) $this->request->getPost('name_en')),
            'profession_en'    => trim((string) $this->request->getPost('profession_en')),
            'profession_ur'    => trim((string) $this->request->getPost('profession_ur')),
            'intro_en'         => trim((string) $this->request->getPost('intro_en')),
            'intro_ur'         => trim((string) $this->request->getPost('intro_ur')),
            'achievements_en'  => trim((string) $this->request->getPost('achievements_en')),
            'achievements_ur'  => trim((string) $this->request->getPost('achievements_ur')),
            'awards_en'        => trim((string) $this->request->getPost('awards_en')),
            'awards_ur'        => trim((string) $this->request->getPost('awards_ur')),
            'years_of_service' => trim((string) $this->request->getPost('years_of_service')),
            'birth_date'       => $this->request->getPost('birth_date') ?: null,
            'death_date'       => $this->request->getPost('death_date') ?: null,
            'featured'         => $this->request->getPost('featured') ? 1 : 0,
            'display_order'    => (int) ($this->request->getPost('display_order') ?? 0),
            'status'           => $this->request->getPost('status') ?? 'active',
            'photo'            => $photoPath ?: null,
            'slug'             => url_title(trim((string) $this->request->getPost('name_en')), '-', true),
        ];

        $wallModel = new WallModel();
        $id        = (int) $wallModel->insert($data);

        $uploaded = $this->handleAttachmentsUpload($id);
        AdminActivityLogModel::log(
            'Created Wall Entry',
            'Wall of Kot Sultan',
            $id,
            "Created personality {$data['name_en']}" . ($uploaded > 0 ? " (+{$uploaded} attachments)" : '')
        );

        return redirect()->to(base_url('admin/wall-of-kot-sultan'))->with('success', lang('App.admin_msg_personality_created'));
    }

    public function edit($id)
    {
        $wallModel         = new WallModel();
        $person            = $wallModel->find($id);

        if (!$person) {
            return redirect()->to(base_url('admin/wall-of-kot-sultan'))->with('error', lang('App.admin_msg_personality_not_found'));
        }

        $wallCategoryModel = new WallCategoryModel();
        $attachmentModel   = new WallAttachmentModel();

        return view('admin/wall/form', [
            'title'       => lang('App.admin_page_edit_wall_title', [$id]),
            'pageHeading' => lang('App.admin_page_edit_personality', [($person['name_en'] ?: $person['name_ur'])]),
            'person'      => $person,
            'attachments' => $attachmentModel->getForWall((int) $id),
            'categories'  => $wallCategoryModel->orderBy('display_order', 'ASC')->findAll(),
        ]);
    }

    public function update($id)
    {
        $wallModel = new WallModel();
        $person    = $wallModel->find($id);

        if (!$person) {
            return redirect()->to(base_url('admin/wall-of-kot-sultan'))->with('error', lang('App.admin_msg_personality_not_found'));
        }

        $rules = [
            'name_en'     => 'required|min_length[2]',
            'category_id' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $photoPath = $this->handlePhotoUpload();
        if (!$photoPath) {
            $photoPath = $person['photo'];
        }

        $data = [
            'category_id'      => (int) $this->request->getPost('category_id'),
            'name_en'          => trim((string) $this->request->getPost('name_en')),
            'name_ur'          => trim((string) $this->request->getPost('name_ur')),
            'profession_en'    => trim((string) $this->request->getPost('profession_en')),
            'profession_ur'    => trim((string) $this->request->getPost('profession_ur')),
            'intro_en'         => trim((string) $this->request->getPost('intro_en')),
            'intro_ur'         => trim((string) $this->request->getPost('intro_ur')),
            'achievements_en'  => trim((string) $this->request->getPost('achievements_en')),
            'achievements_ur'  => trim((string) $this->request->getPost('achievements_ur')),
            'awards_en'        => trim((string) $this->request->getPost('awards_en')),
            'awards_ur'        => trim((string) $this->request->getPost('awards_ur')),
            'years_of_service' => trim((string) $this->request->getPost('years_of_service')),
            'birth_date'       => $this->request->getPost('birth_date') ?: null,
            'death_date'       => $this->request->getPost('death_date') ?: null,
            'featured'         => $this->request->getPost('featured') ? 1 : 0,
            'display_order'    => (int) ($this->request->getPost('display_order') ?? 0),
            'status'           => $this->request->getPost('status') ?? 'active',
            'photo'            => $photoPath,
        ];

        $wallModel->update($id, $data);
        $uploaded = $this->handleAttachmentsUpload((int) $id);

        AdminActivityLogModel::log(
            'Updated Wall Entry',
            'Wall of Kot Sultan',
            $id,
            "Updated personality {$data['name_en']}" . ($uploaded > 0 ? " (+{$uploaded} attachments)" : '')
        );

        return redirect()->to(base_url('admin/wall-of-kot-sultan'))->with('success', lang('App.admin_msg_personality_updated'));
    }

    public function delete($id)
    {
        $wallModel = new WallModel();
        $person    = $wallModel->find($id);

        if ($person) {
            $this->deleteAllAttachments((int) $id);
            $wallModel->delete($id);
            AdminActivityLogModel::log('Deleted Wall Entry', 'Wall of Kot Sultan', $id, "Deleted personality {$person['name_en']}");
            return redirect()->to(base_url('admin/wall-of-kot-sultan'))->with('success', lang('App.admin_msg_personality_deleted'));
        }

        return redirect()->to(base_url('admin/wall-of-kot-sultan'))->with('error', lang('App.admin_msg_entry_not_found'));
    }

    public function toggle($id)
    {
        $wallModel = new WallModel();
        $person    = $wallModel->find($id);

        if ($person) {
            $newStatus = ($person['status'] === 'active') ? 'inactive' : 'active';
            $wallModel->update($id, ['status' => $newStatus]);
            AdminActivityLogModel::log('Toggled Wall Status', 'Wall of Kot Sultan', $id, "Status changed to $newStatus");
            return redirect()->back()->with('success', lang('App.admin_msg_entry_status_changed', [$newStatus]));
        }

        return redirect()->back()->with('error', lang('App.admin_msg_entry_not_found'));
    }

    public function deleteAttachment($id, $attachmentId)
    {
        $wallModel       = new WallModel();
        $attachmentModel = new WallAttachmentModel();
        $person          = $wallModel->find($id);
        $attachment      = $attachmentModel->find($attachmentId);

        if (!$person || !$attachment || (int) $attachment['wall_id'] !== (int) $id) {
            return redirect()->back()->with('error', lang('App.admin_msg_entry_not_found'));
        }

        $this->deleteAttachmentFile($attachment);
        $attachmentModel->delete($attachmentId);

        AdminActivityLogModel::log('Deleted Wall Attachment', 'Wall of Kot Sultan', $id, 'Removed attachment #' . $attachmentId);

        return redirect()->back()->with('success', lang('App.admin_attachment_deleted') ?? 'Attachment deleted.');
    }

    private function handlePhotoUpload(): ?string
    {
        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $dir = FCPATH . 'uploads/wall';
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $newName = $file->getRandomName();
            $file->move($dir, $newName);
            return 'uploads/wall/' . $newName;
        }
        return null;
    }

    private function handleAttachmentsUpload(int $wallId): int
    {
        $files = $this->request->getFileMultiple('attachments');
        if ($files === null) {
            $single = $this->request->getFile('attachments');
            $files  = ($single && $single->isValid()) ? [$single] : [];
        }

        if ($files === []) {
            return 0;
        }

        $dir = FCPATH . 'uploads/wall/attachments';
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $model   = new WallAttachmentModel();
        $allowed = WallAttachmentModel::allowedExtensions();
        $count   = 0;
        $order   = (int) $model->where('wall_id', $wallId)->countAllResults();

        foreach ($files as $file) {
            if (! $file || ! $file->isValid() || $file->hasMoved()) {
                continue;
            }

            $ext = strtolower($file->getClientExtension() ?: $file->guessExtension() ?: '');
            if (! in_array($ext, $allowed, true)) {
                continue;
            }

            $size = (int) $file->getSize();
            // ~12MB soft limit per file
            if ($size > 12 * 1024 * 1024) {
                continue;
            }

            $originalName = $file->getClientName();
            $mimeType     = $file->getClientMimeType();
            $newName      = $file->getRandomName();
            $file->move($dir, $newName);
            $relative = 'uploads/wall/attachments/' . $newName;

            $model->insert([
                'wall_id'       => $wallId,
                'file_path'     => $relative,
                'original_name' => $originalName,
                'mime_type'     => $mimeType,
                'file_type'     => WallAttachmentModel::classifyExtension($ext),
                'file_size'     => $size,
                'display_order' => $order++,
            ]);
            $count++;
        }

        return $count;
    }

    private function deleteAllAttachments(int $wallId): void
    {
        $model = new WallAttachmentModel();
        $rows  = $model->where('wall_id', $wallId)->findAll();
        foreach ($rows as $row) {
            $this->deleteAttachmentFile($row);
            $model->delete($row['id']);
        }
    }

    private function deleteAttachmentFile(array $attachment): void
    {
        $path = trim((string) ($attachment['file_path'] ?? ''));
        if ($path === '') {
            return;
        }
        $full = FCPATH . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
        // Only delete under uploads/wall
        $uploadsRoot = realpath(FCPATH . 'uploads/wall');
        $realFile    = realpath($full);
        if ($uploadsRoot && $realFile && str_starts_with($realFile, $uploadsRoot) && is_file($realFile)) {
            @unlink($realFile);
        }
    }
}
