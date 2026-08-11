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
        $this->applyExternalLinksField($data);

        $wallModel = new WallModel();
        $id = (int) $wallModel->insert($data);
        if ($id < 1) {
            return redirect()->back()->withInput()->with('error', lang('App.admin_msg_personality_not_found'));
        }

        $uploadResult = $this->handleAttachmentsUpload($id);
        $uploaded     = (int) ($uploadResult['saved'] ?? 0);
        $uploadErrors = $uploadResult['errors'] ?? [];

        AdminActivityLogModel::log(
            'Created Wall Entry',
            'Wall of Kot Sultan',
            $id,
            "Created personality {$data['name_en']}" . ($uploaded > 0 ? " (+{$uploaded} attachments)" : '')
        );

        $msg = lang('App.admin_msg_personality_created');
        if ($uploaded > 0) {
            $msg .= ' ' . lang('App.admin_attachments_saved', [$uploaded]);
        }
        if ($uploadErrors !== []) {
            return redirect()->to(base_url('admin/wall-of-kot-sultan/edit/' . $id))
                ->with('success', $msg)
                ->with('error', implode(' ', $uploadErrors));
        }

        return redirect()->to(base_url('admin/wall-of-kot-sultan/edit/' . $id))->with('success', $msg);
    }

    public function edit($id)
    {
        $wallModel         = new WallModel();
        $person            = $wallModel->find($id);

        if (!$person) {
            return redirect()->to(base_url('admin/wall-of-kot-sultan'))->with('error', lang('App.admin_msg_personality_not_found'));
        }

        $wallCategoryModel = new WallCategoryModel();
        $attachments       = [];
        try {
            $attachments = (new WallAttachmentModel())->getForWall((int) $id);
        } catch (\Throwable $e) {
            // Table may not exist yet on live until migrate is run.
            $attachments = [];
            session()->setFlashdata('error', lang('App.admin_attachments_table_missing'));
        }

        return view('admin/wall/form', [
            'title'       => lang('App.admin_page_edit_wall_title', [$id]),
            'pageHeading' => lang('App.admin_page_edit_personality', [($person['name_en'] ?: $person['name_ur'])]),
            'person'      => $person,
            'attachments' => $attachments,
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
        $this->applyExternalLinksField($data);

        $wallModel->update($id, $data);

        $uploadResult = $this->handleAttachmentsUpload((int) $id);
        $uploaded     = (int) ($uploadResult['saved'] ?? 0);
        $uploadErrors = $uploadResult['errors'] ?? [];

        AdminActivityLogModel::log(
            'Updated Wall Entry',
            'Wall of Kot Sultan',
            $id,
            "Updated personality {$data['name_en']}" . ($uploaded > 0 ? " (+{$uploaded} attachments)" : '')
        );

        $msg = lang('App.admin_msg_personality_updated');
        if ($uploaded > 0) {
            $msg .= ' ' . lang('App.admin_attachments_saved', [$uploaded]);
        }
        if ($uploadErrors !== []) {
            return redirect()->to(base_url('admin/wall-of-kot-sultan/edit/' . $id))
                ->with('success', $msg)
                ->with('error', implode(' ', $uploadErrors));
        }

        return redirect()->to(base_url('admin/wall-of-kot-sultan/edit/' . $id))->with('success', $msg);
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

    /**
     * @return array{saved:int,errors:list<string>}
     */
    private function handleAttachmentsUpload(int $wallId): array
    {
        $files = $this->request->getFileMultiple('attachments');
        if ($files === null) {
            $single = $this->request->getFile('attachments');
            $files  = ($single && $single->getError() !== UPLOAD_ERR_NO_FILE) ? [$single] : [];
        }

        // Drop empty placeholders browsers sometimes send.
        $files = array_values(array_filter($files ?? [], static function ($file) {
            return $file && $file->getError() !== UPLOAD_ERR_NO_FILE;
        }));

        if ($files === []) {
            return ['saved' => 0, 'errors' => []];
        }

        $dir = FCPATH . 'uploads/wall/attachments';
        if (! is_dir($dir) && ! @mkdir($dir, 0755, true) && ! is_dir($dir)) {
            return ['saved' => 0, 'errors' => [lang('App.admin_attachments_dir_failed')]];
        }
        if (! is_writable($dir)) {
            return ['saved' => 0, 'errors' => [lang('App.admin_attachments_dir_failed')]];
        }

        try {
            $model = new WallAttachmentModel();
            // Ensure parent row exists (also guards against bad ids).
            $parent = (new WallModel())->find($wallId);
            if (! $parent) {
                return ['saved' => 0, 'errors' => [lang('App.admin_msg_personality_not_found')]];
            }
            $order = (int) $model->where('wall_id', $wallId)->countAllResults();
        } catch (\Throwable $e) {
            return ['saved' => 0, 'errors' => [lang('App.admin_attachments_table_missing')]];
        }

        $allowed = WallAttachmentModel::allowedExtensions();
        $saved   = 0;
        $errors  = [];

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }
            if (! $file->isValid()) {
                $errors[] = ($file->getClientName() ?: 'File') . ': ' . $file->getErrorString();
                continue;
            }
            if ($file->hasMoved()) {
                continue;
            }

            $ext = strtolower((string) ($file->getClientExtension() ?: $file->guessExtension() ?: ''));
            if (! in_array($ext, $allowed, true)) {
                $errors[] = ($file->getClientName() ?: 'File') . ': ' . lang('App.admin_attachments_type_invalid');
                continue;
            }

            $size = (int) $file->getSize();
            if ($size > 12 * 1024 * 1024) {
                $errors[] = ($file->getClientName() ?: 'File') . ': ' . lang('App.admin_attachments_too_large');
                continue;
            }

            try {
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
                $saved++;
            } catch (\Throwable $e) {
                $errors[] = ($file->getClientName() ?: 'File') . ': ' . $e->getMessage();
            }
        }

        return ['saved' => $saved, 'errors' => $errors];
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

    /**
     * @param array<string, mixed> $data
     */
    private function applyExternalLinksField(array &$data): void
    {
        try {
            if (! db_connect()->fieldExists('external_links', 'wall_of_kot_sultan')) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }
        $data['external_links'] = $this->encodeExternalLinks(
            $this->request->getPost('link_url'),
            $this->request->getPost('link_title')
        );
    }

    /**
     * @param mixed $urls
     * @param mixed $titles
     */
    private function encodeExternalLinks($urls, $titles): ?string
    {
        $urls   = is_array($urls) ? array_values($urls) : [];
        $titles = is_array($titles) ? array_values($titles) : [];
        $links  = [];

        foreach ($urls as $i => $rawUrl) {
            $url = trim((string) $rawUrl);
            $url = preg_replace('/\s+/', '', $url) ?? $url;
            if ($url === '') {
                continue;
            }
            if (! preg_match('#^https?://#i', $url)) {
                $url = 'https://' . ltrim($url, '/');
            }
            // Keep social / query / unicode URLs that FILTER_VALIDATE_URL often rejects.
            if (! preg_match('#^https?://[^\s<>"\']{3,}$#iu', $url)) {
                continue;
            }
            $title = trim((string) ($titles[$i] ?? ''));
            $links[] = [
                'url'   => $url,
                'title' => $title !== '' ? $title : $url,
            ];
        }

        return $links === [] ? null : json_encode($links, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return list<array{url:string,title:string}>
     */
    public static function decodeExternalLinks(?string $json): array
    {
        return \App\Models\WallModel::decodeExternalLinks($json);
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
