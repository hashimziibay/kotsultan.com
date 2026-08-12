<?php

namespace App\Controllers\Api;

use App\Models\AppUserModel;
use App\Models\AreaModel;
use App\Models\BusinessModel;
use App\Models\CategoryModel;
use App\Models\VillageModel;

/**
 * Authenticated business-owner listing management.
 * Business accounts can browse the app as users AND manage their own listings.
 */
class MyBusinessController extends BaseApiController
{
    public function formOptions()
    {
        $user = $this->requireBusinessUser();
        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        $categories = (new CategoryModel())->orderBy('name_en', 'ASC')->findAll();
        $areas      = (new AreaModel())->orderBy('name_en', 'ASC')->findAll();
        $villages   = (new VillageModel())->orderBy('name_en', 'ASC')->findAll();

        $map = static function (array $rows): array {
            return array_map(static function ($r) {
                return [
                    'id'      => (int) $r['id'],
                    'name_en' => $r['name_en'] ?? '',
                    'name_ur' => $r['name_ur'] ?? '',
                    'name'    => ($r['name_en'] ?? '') !== '' ? $r['name_en'] : ($r['name_ur'] ?? ''),
                ];
            }, $rows);
        };

        return $this->jsonOk([
            'categories' => $map($categories),
            'areas'      => $map($areas),
            'villages'   => $map($villages),
            'account_phone' => (new AppUserModel())->normalizePhone((string) ($user['phone'] ?? '')),
        ]);
    }

    public function index()
    {
        $user = $this->requireBusinessUser();
        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        $rows = (new BusinessModel())
            ->where('user_id', (int) $user['id'])
            ->orderBy('id', 'DESC')
            ->findAll();

        return $this->jsonOk([
            'items' => array_map([$this, 'mapBusiness'], $rows),
            'user'  => (new AppUserModel())->publicProfile($user),
        ]);
    }

    public function show($id = null)
    {
        $user = $this->requireBusinessUser();
        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        $biz = $this->ownedBusiness((int) $id, (int) $user['id']);
        if (! $biz) {
            return $this->jsonError('Business not found', 404);
        }

        return $this->jsonOk(['business' => $this->mapBusiness($biz)]);
    }

    public function store()
    {
        $user = $this->requireBusinessUser();
        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        $model = new BusinessModel();
        $accountPhone = (new AppUserModel())->normalizePhone((string) ($user['phone'] ?? ''));

        $existing = $model->findOwnedByUser((int) $user['id']);
        if ($existing) {
            return $this->jsonError(
                'Only one business is allowed per mobile number. Update your existing listing instead.',
                409,
                ['business' => $this->mapBusiness($existing)]
            );
        }

        if ($accountPhone !== '' && $model->contactPhoneHasBusiness($accountPhone)) {
            return $this->jsonError(
                'A business is already registered with this contact number.',
                409
            );
        }

        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $parsed  = $this->parseBusinessPayload(is_array($payload) ? $payload : []);
        if (isset($parsed['error'])) {
            return $this->jsonError($parsed['error'], 422);
        }

        helper('seo');
        $photo = $this->handleImageUpload();
        $data  = $parsed['data'] + [
            'user_id'    => (int) $user['id'],
            'owner_name' => $user['name'],
            'phone'      => $accountPhone !== '' ? $accountPhone : ($parsed['data']['phone'] ?? ''),
            'slug'       => 'pending-' . bin2hex(random_bytes(4)),
            'status'     => 'pending', // admin approves
            'featured'   => 0,
        ];
        // Always register listing against account contact number
        if ($accountPhone !== '') {
            $data['phone'] = $accountPhone;
        }
        if ($photo) {
            $data['image'] = $photo;
        }

        $id = (int) $model->insert($data);
        if ($id < 1) {
            return $this->jsonError('Could not create business', 500);
        }

        $slug = make_unique_listing_seo_slug($data['name_en'], $id);
        $model->update($id, ['slug' => $slug]);
        $biz = $model->find($id);

        return $this->jsonOk([
            'business' => $this->mapBusiness($biz),
            'user'     => (new AppUserModel())->publicProfile($user),
        ], 'Business submitted for review', 201);
    }

    public function update($id = null)
    {
        $user = $this->requireBusinessUser();
        if ($user instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $user;
        }

        $biz = $this->ownedBusiness((int) $id, (int) $user['id']);
        if (! $biz) {
            return $this->jsonError('Business not found', 404);
        }

        $payload = $this->request->getJSON(true) ?: $this->request->getRawInput() ?: $this->request->getPost();
        $parsed  = $this->parseBusinessPayload(is_array($payload) ? $payload : [], false);
        if (isset($parsed['error'])) {
            return $this->jsonError($parsed['error'], 422);
        }

        helper('seo');
        $data = $parsed['data'];
        if (isset($data['name_en']) && $data['name_en'] !== '') {
            $data['slug'] = make_unique_listing_seo_slug($data['name_en'], (int) $biz['id']);
        }
        // Keep pending unless admin already activated — owner edits don't auto-publish inactive
        if (($biz['status'] ?? '') === 'inactive') {
            $data['status'] = 'pending';
        }

        $photo = $this->handleImageUpload();
        if ($photo) {
            $data['image'] = $photo;
        }

        // Keep listing phone tied to account contact number
        $accountPhone = (new AppUserModel())->normalizePhone((string) ($user['phone'] ?? ''));
        if ($accountPhone !== '') {
            $data['phone'] = $accountPhone;
        }

        (new BusinessModel())->update((int) $biz['id'], $data);
        $fresh = (new BusinessModel())->find((int) $biz['id']);

        return $this->jsonOk([
            'business' => $this->mapBusiness($fresh),
        ], 'Business updated');
    }

    /**
     * Business accounts only. Community users must call auth/upgrade-business first.
     *
     * @return array|ResponseInterface
     */
    private function requireBusinessUser()
    {
        $user = $this->currentAppUser();
        if (! $user) {
            return $this->jsonError('Unauthorized', 401);
        }

        if (($user['account_type'] ?? 'user') !== 'business') {
            return $this->jsonError(
                'Switch to a business account and set a password to manage listings.',
                403,
                ['code' => 'upgrade_required']
            );
        }

        if (empty($user['password_hash'])) {
            return $this->jsonError(
                'Set a business password before managing listings.',
                403,
                ['code' => 'password_required']
            );
        }

        return $user;
    }

    private function ownedBusiness(int $id, int $userId): ?array
    {
        if ($id < 1) {
            return null;
        }
        return (new BusinessModel())
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * @param array<string,mixed> $payload
     * @return array{data?:array<string,mixed>,error?:string}
     */
    private function parseBusinessPayload(array $payload, bool $requireName = true): array
    {
        $nameEn = trim((string) ($payload['name_en'] ?? $payload['name'] ?? ''));
        $nameUr = trim((string) ($payload['name_ur'] ?? ''));
        if ($requireName && ($nameEn === '' || strlen($nameEn) < 2)) {
            return ['error' => 'Business name is required'];
        }

        $categoryId = (int) ($payload['category_id'] ?? 0);
        if ($requireName && $categoryId < 1) {
            return ['error' => 'Category is required'];
        }
        if ($categoryId > 0) {
            $cat = (new CategoryModel())->find($categoryId);
            if (! $cat) {
                return ['error' => 'Invalid category'];
            }
        }

        $data = [];
        if ($nameEn !== '') {
            $data['name_en'] = $nameEn;
            $data['name_ur'] = $nameUr !== '' ? $nameUr : $nameEn;
        } elseif ($nameUr !== '') {
            $data['name_ur'] = $nameUr;
        }

        if ($categoryId > 0) {
            $data['category_id'] = $categoryId;
        }

        foreach (['area_id', 'village_id'] as $fk) {
            if (array_key_exists($fk, $payload) && $payload[$fk] !== '' && $payload[$fk] !== null) {
                $data[$fk] = (int) $payload[$fk] ?: null;
            }
        }

        foreach (['address_en', 'address_ur', 'description_en', 'description_ur', 'phone', 'whatsapp', 'email', 'website', 'opening_hours'] as $field) {
            if (array_key_exists($field, $payload)) {
                $data[$field] = trim((string) $payload[$field]);
            }
        }
        if (isset($payload['address']) && ! isset($data['address_en'])) {
            $data['address_en'] = trim((string) $payload['address']);
        }
        if (isset($payload['description']) && ! isset($data['description_en'])) {
            $data['description_en'] = trim((string) $payload['description']);
        }

        return ['data' => $data];
    }

    private function mapBusiness(?array $b): array
    {
        if (! $b) {
            return [];
        }
        $image = trim((string) ($b['image'] ?? ''));
        if ($image !== '' && ! preg_match('#^(https?:)?//#i', $image)) {
            $image = base_url(ltrim($image, '/'));
        }

        return [
            'id'              => (int) ($b['id'] ?? 0),
            'user_id'         => isset($b['user_id']) ? (int) $b['user_id'] : null,
            'category_id'     => isset($b['category_id']) ? (int) $b['category_id'] : null,
            'area_id'         => isset($b['area_id']) ? (int) $b['area_id'] : null,
            'village_id'      => isset($b['village_id']) ? (int) $b['village_id'] : null,
            'name_en'         => $b['name_en'] ?? '',
            'name_ur'         => $b['name_ur'] ?? '',
            'slug'            => $b['slug'] ?? '',
            'owner_name'      => $b['owner_name'] ?? '',
            'address_en'      => $b['address_en'] ?? ($b['address'] ?? ''),
            'address_ur'      => $b['address_ur'] ?? '',
            'description_en'  => $b['description_en'] ?? '',
            'description_ur'  => $b['description_ur'] ?? '',
            'phone'           => $b['phone'] ?? '',
            'whatsapp'        => $b['whatsapp'] ?? '',
            'email'           => $b['email'] ?? '',
            'website'         => $b['website'] ?? '',
            'opening_hours'   => $b['opening_hours'] ?? '',
            'image'           => $image !== '' ? $image : null,
            'status'          => $b['status'] ?? 'pending',
            'created_at'      => $b['created_at'] ?? null,
            'updated_at'      => $b['updated_at'] ?? null,
        ];
    }

    private function handleImageUpload(): ?string
    {
        $file = $this->request->getFile('image');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $mime = (string) $file->getMimeType();
        if ($mime !== '' && strpos($mime, 'image/') !== 0) {
            return null;
        }

        $dir = FCPATH . 'uploads/businesses';
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $newName = $file->getRandomName();
        $file->move($dir, $newName);

        return 'uploads/businesses/' . $newName;
    }
}
