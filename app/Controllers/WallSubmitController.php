<?php

namespace App\Controllers;

use App\Models\WallAttachmentModel;
use App\Models\WallCategoryModel;
use App\Models\WallModel;
use App\Models\AdminActivityLogModel;

/**
 * Public (no login) Wall personality nomination → pending until admin approves.
 */
class WallSubmitController extends BaseController
{
    public function index()
    {
        $lang = service('request')->getLocale();
        $cats = (new WallCategoryModel())
            ->where('status', 'active')
            ->orderBy('display_order', 'ASC')
            ->orderBy('name_en', 'ASC')
            ->findAll();

        $preselect = trim((string) ($this->request->getGet('category') ?? ''));
        $selectedCategoryIds = [];
        if ($preselect !== '') {
            foreach ($cats as $c) {
                if ((string) $c['id'] === $preselect
                    || ($c['slug'] ?? '') === $preselect
                    || strcasecmp((string) ($c['name_en'] ?? ''), $preselect) === 0
                ) {
                    $selectedCategoryIds = [(int) $c['id']];
                    break;
                }
            }
        }

        $oldIds = old('category_ids');
        if (is_array($oldIds) && $oldIds !== []) {
            $selectedCategoryIds = array_values(array_unique(array_map('intval', $oldIds)));
        } elseif (old('category_id')) {
            $selectedCategoryIds = [(int) old('category_id')];
        }

        return view('wall_submit', [
            'lang'                 => $lang,
            'title'                => lang('App.wall_submit_title'),
            'metaDescription'      => lang('App.wall_submit_subtitle'),
            'categories'           => $cats,
            'selectedCategoryIds'  => $selectedCategoryIds,
            'socialPlatforms'      => WallModel::socialPlatforms(),
        ]);
    }

    public function store()
    {
        // Honeypot — bots fill this; humans leave empty
        if (trim((string) $this->request->getPost('website_url')) !== '') {
            return redirect()->to(base_url('wall-of-kot-sultan/submit'))
                ->with('success', lang('App.wall_submit_success'));
        }

        $rules = [
            'name_en'         => 'required|min_length[2]|max_length[150]',
            'submitter_name'  => 'required|min_length[2]|max_length[150]',
            'submitter_phone' => 'required|min_length[10]|max_length[50]',
            'years_of_service'=> 'permit_empty|max_length[5000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        $categoryIds = $this->parseCategoryIdsFromRequest();
        if ($categoryIds === []) {
            return redirect()->back()->withInput()
                ->with('error', lang('App.admin_select_category') ?: 'Please select a valid category');
        }

        $activeCats = (new WallCategoryModel())
            ->where('status', 'active')
            ->whereIn('id', $categoryIds)
            ->findAll();
        $validIds = array_map(static fn ($c) => (int) $c['id'], $activeCats);
        // Preserve submit order, drop invalid
        $categoryIds = array_values(array_filter(
            $categoryIds,
            static fn ($id) => in_array($id, $validIds, true)
        ));
        if ($categoryIds === []) {
            return redirect()->back()->withInput()
                ->with('error', lang('App.admin_select_category') ?: 'Please select a valid category');
        }

        $nameEn = trim((string) $this->request->getPost('name_en'));
        $nameUr = trim((string) $this->request->getPost('name_ur'));
        if ($nameUr === '') {
            $nameUr = $nameEn;
        }

        $photoPath = $this->handlePhotoUpload();

        $data = [
            'category_id'      => $categoryIds[0],
            'name_en'          => $nameEn,
            'name_ur'          => $nameUr,
            'profession_en'    => trim((string) $this->request->getPost('profession_en')),
            'profession_ur'    => trim((string) $this->request->getPost('profession_ur')),
            'intro_en'         => trim((string) $this->request->getPost('intro_en')),
            'intro_ur'         => trim((string) $this->request->getPost('intro_ur')),
            'years_of_service' => sanitize_rich_text((string) $this->request->getPost('years_of_service')),
            'featured'         => 0,
            'display_order'    => 0,
            'status'           => 'pending',
            'photo'            => $photoPath,
            'slug'             => 'pending-' . bin2hex(random_bytes(4)),
            'submitter_name'   => trim((string) $this->request->getPost('submitter_name')),
            'submitter_phone'  => preg_replace('/\D+/', '', (string) $this->request->getPost('submitter_phone')) ?: trim((string) $this->request->getPost('submitter_phone')),
            'submitter_email'  => trim((string) $this->request->getPost('submitter_email')),
        ];
        $this->applyExternalLinksField($data);

        $wallModel = new WallModel();
        $id = (int) $wallModel->insert($data);
        if ($id < 1) {
            return redirect()->back()->withInput()
                ->with('error', lang('App.wall_submit_failed') ?: 'Could not submit. Please try again.');
        }

        $slugBase = url_title($nameEn, '-', true) ?: ('personality-' . $id);
        $wallModel->update($id, ['slug' => $slugBase . '-' . $id]);
        $wallModel->syncCategories($id, $categoryIds);

        $uploadResult = $this->handleAttachmentsUpload($id);
        $uploaded     = (int) ($uploadResult['saved'] ?? 0);

        try {
            AdminActivityLogModel::log(
                'Public Wall Nomination',
                'Wall of Kot Sultan',
                $id,
                "Pending nomination: {$nameEn} (cats: " . implode(',', $categoryIds) . ')'
                . ($uploaded > 0 ? " (+{$uploaded} files)" : '')
            );
        } catch (\Throwable $e) {
            // non-fatal
        }

        return redirect()->to(base_url('wall-of-kot-sultan/submit'))
            ->with('success', lang('App.wall_submit_success'));
    }

    /**
     * @return list<int>
     */
    private function parseCategoryIdsFromRequest(): array
    {
        $raw = $this->request->getPost('category_ids');
        if (! is_array($raw)) {
            $single = (int) $this->request->getPost('category_id');
            return $single > 0 ? [$single] : [];
        }

        $ids = [];
        foreach ($raw as $v) {
            $id = (int) $v;
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
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
        $data['external_links'] = $this->encodeAllLinks();
    }

    private function encodeAllLinks(): ?string
    {
        $links = array_merge(
            $this->encodeLinkGroup(
                'external',
                null,
                $this->request->getPost('ext_link_url'),
                $this->request->getPost('ext_link_title')
            ),
            $this->encodeLinkGroup(
                'social',
                $this->request->getPost('social_link_platform'),
                $this->request->getPost('social_link_url'),
                $this->request->getPost('social_link_title')
            )
        );

        return $links === [] ? null : json_encode($links, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param mixed $platforms
     * @param mixed $urls
     * @param mixed $titles
     * @return list<array{kind:string,platform:string,url:string,title:string}>
     */
    private function encodeLinkGroup(string $kind, $platforms, $urls, $titles): array
    {
        $platforms = is_array($platforms) ? array_values($platforms) : [];
        $urls      = is_array($urls) ? array_values($urls) : [];
        $titles    = is_array($titles) ? array_values($titles) : [];
        $catalog   = WallModel::socialPlatforms();
        $links     = [];
        $count     = max(count($urls), count($platforms), count($titles));

        for ($i = 0; $i < $count; $i++) {
            $url = trim((string) ($urls[$i] ?? ''));
            $url = preg_replace('/\s+/', '', $url) ?? $url;
            if ($url === '') {
                continue;
            }
            if (! preg_match('#^https?://#i', $url)) {
                $url = 'https://' . ltrim($url, '/');
            }
            if (! preg_match('#^https?://[^\s<>"\']{3,}$#iu', $url)) {
                continue;
            }

            $title = trim((string) ($titles[$i] ?? ''));

            if ($kind === 'social') {
                $rawPlatform = trim((string) ($platforms[$i] ?? ''));
                $platform    = $rawPlatform === ''
                    ? WallModel::inferPlatformFromUrl($url)
                    : WallModel::normalizePlatform($rawPlatform);
                if (! WallModel::isSocialPlatform($platform)) {
                    continue;
                }
                $meta = $catalog[$platform];
                if ($title === '') {
                    $title = $meta['label'];
                }
                $links[] = [
                    'kind'     => 'social',
                    'platform' => $platform,
                    'url'      => $url,
                    'title'    => $title,
                ];
                continue;
            }

            if ($title === '') {
                $host = (string) (parse_url($url, PHP_URL_HOST) ?? '');
                $title = $host !== '' ? (preg_replace('/^www\./', '', $host) ?: $url) : $url;
            }
            $links[] = [
                'kind'     => 'external',
                'platform' => 'website',
                'url'      => $url,
                'title'    => $title,
            ];
        }

        return $links;
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

        $files = array_values(array_filter($files ?? [], static function ($file) {
            return $file && $file->getError() !== UPLOAD_ERR_NO_FILE;
        }));

        if ($files === []) {
            return ['saved' => 0, 'errors' => []];
        }

        $dir = FCPATH . 'uploads/wall/attachments';
        if (! is_dir($dir) && ! @mkdir($dir, 0755, true) && ! is_dir($dir)) {
            return ['saved' => 0, 'errors' => []];
        }

        try {
            $model  = new WallAttachmentModel();
            $parent = (new WallModel())->find($wallId);
            if (! $parent) {
                return ['saved' => 0, 'errors' => []];
            }
            $order = (int) $model->where('wall_id', $wallId)->countAllResults();
        } catch (\Throwable $e) {
            return ['saved' => 0, 'errors' => []];
        }

        $allowed = WallAttachmentModel::allowedExtensions();
        $saved   = 0;
        $errors  = [];
        $maxFiles = 10;
        $count    = 0;

        foreach ($files as $file) {
            if ($count >= $maxFiles) {
                break;
            }
            if (! $file || ! $file->isValid() || $file->hasMoved()) {
                continue;
            }

            $ext = strtolower((string) ($file->getClientExtension() ?: $file->guessExtension() ?: ''));
            if (! in_array($ext, $allowed, true)) {
                continue;
            }

            $size = (int) $file->getSize();
            if ($size > 12 * 1024 * 1024) {
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
                $count++;
            } catch (\Throwable $e) {
                // skip bad file
            }
        }

        return ['saved' => $saved, 'errors' => $errors];
    }

    private function handlePhotoUpload(): ?string
    {
        $file = $this->request->getFile('photo');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $mime = (string) $file->getMimeType();
        if ($mime !== '' && strpos($mime, 'image/') !== 0) {
            return null;
        }

        $dir = FCPATH . 'uploads/wall';
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $newName = $file->getRandomName();
        $file->move($dir, $newName);

        return 'uploads/wall/' . $newName;
    }
}
