<?php

namespace App\Controllers;

use App\Models\AppUserModel;
use App\Models\BusinessModel;
use App\Models\CategoryModel;
use App\Models\AreaModel;
use App\Models\VillageModel;

class AccountController extends BaseController
{
    public function login()
    {
        if (session()->get('app_user_logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('login', [
            'lang'         => service('request')->getLocale(),
            'title'        => lang('App.login_title'),
            'prefillPhone' => (string) ($this->request->getGet('phone') ?: old('phone') ?: ''),
            'intent'       => (string) $this->request->getGet('intent'),
        ]);
    }

    public function attemptLogin()
    {
        $model    = new AppUserModel();
        $phone    = $model->normalizePhone((string) $this->request->getPost('phone'));
        $password = (string) $this->request->getPost('password');

        if ($phone === '' || strlen($phone) < 10) {
            return redirect()->back()->withInput()->with('error', lang('App.valid_phone_required') ?: 'Valid mobile number is required');
        }

        $user = $model->where('phone', $phone)->first();
        if (! $user) {
            return redirect()->back()->withInput()->with('error', lang('App.account_not_found') ?: 'Account not found. Please sign up.');
        }
        if (($user['status'] ?? 'active') !== 'active') {
            return redirect()->back()->with('error', lang('App.account_inactive') ?: 'Account is inactive. Contact admin.');
        }

        $type = ($user['account_type'] ?? 'user') === 'business' ? 'business' : 'user';
        if ($type === 'business') {
            if ($password === '' || ! $model->verifyPassword($user, $password)) {
                return redirect()->back()->withInput()->with('error', lang('App.invalid_login') ?: 'Invalid mobile number or password');
            }
        }

        $this->setAppUserSession($user);
        $model->issueToken((int) $user['id']); // keep API token in sync for app use

        $dest = base_url('dashboard');
        if (($user['account_type'] ?? 'user') === 'business') {
            $dest = base_url('dashboard?tab=business');
        }

        return redirect()->to($dest)->with('success', lang('App.welcome_back') ?: 'Welcome back');
    }

    public function signup()
    {
        if (session()->get('app_user_logged_in')) {
            if (session()->get('app_user_type') === 'business') {
                $owned = (new BusinessModel())->findOwnedByUser((int) session()->get('app_user_id'));
                if ($owned) {
                    return redirect()->to(base_url('dashboard?tab=business'));
                }
                return redirect()->to(base_url('dashboard/business/create'));
            }
            return redirect()->to(base_url('dashboard'));
        }

        $type = strtolower((string) $this->request->getGet('type'));
        $defaultRole = $type === 'business' ? 'business' : 'user';

        // Business signup must start with mobile check
        $verifiedPhone = (string) (session()->get('business_signup_phone') ?? '');
        if ($defaultRole === 'business' && $verifiedPhone === '') {
            return redirect()->to(base_url('add-business'));
        }

        return view('signup', [
            'lang'           => service('request')->getLocale(),
            'title'          => lang('App.signup_title'),
            'categories'     => (new CategoryModel())->orderBy('name_en', 'ASC')->findAll(),
            'areas'          => (new AreaModel())->orderBy('name_en', 'ASC')->findAll(),
            'villages'       => (new VillageModel())->orderBy('name_en', 'ASC')->findAll(),
            'defaultRole'    => $defaultRole,
            'verifiedPhone'  => $verifiedPhone,
            'lockPhone'      => $defaultRole === 'business' && $verifiedPhone !== '',
        ]);
    }

    /**
     * Step 1 for listing a business: collect mobile and check duplicates.
     */
    public function addBusinessGate()
    {
        if (session()->get('app_user_logged_in')) {
            if (session()->get('app_user_type') === 'business') {
                $owned = (new BusinessModel())->findOwnedByUser((int) session()->get('app_user_id'));
                if ($owned) {
                    return redirect()->to(base_url('dashboard?tab=business'))
                        ->with('info', lang('App.sign_in_to_edit_business') ?: 'Sign in to edit your business listing.');
                }
                return redirect()->to(base_url('dashboard/business/create'));
            }
            // Logged-in community user upgrading: still verify phone via gate? Use their phone.
            session()->set('business_signup_phone', (string) session()->get('app_user_phone'));
            return redirect()->to(base_url('signup?type=business'));
        }

        return view('add_business_gate', [
            'lang'  => service('request')->getLocale(),
            'title' => lang('App.list_your_business'),
        ]);
    }

    public function checkAddBusinessPhone()
    {
        $model = new AppUserModel();
        $phone = $model->normalizePhone((string) $this->request->getPost('phone'));

        if ($phone === '' || strlen($phone) < 10) {
            return redirect()->back()->withInput()->with('error', lang('App.valid_phone_required') ?: 'Valid mobile number is required');
        }

        $existing = $model->where('phone', $phone)->first();
        if ($existing && ($existing['account_type'] ?? 'user') === 'business') {
            // Already a business account — must sign in to edit
            session()->remove('business_signup_phone');
            return redirect()
                ->to(base_url('login?phone=' . rawurlencode($phone) . '&intent=edit-business'))
                ->with('info', lang('App.business_exists_sign_in_edit') ?: 'This mobile number already has a business account. Please sign in to edit your listing.');
        }

        // Also block if this phone already owns a linked business via any account
        if ($existing) {
            $owned = (new BusinessModel())->findOwnedByUser((int) $existing['id']);
            if ($owned) {
                session()->remove('business_signup_phone');
                return redirect()
                    ->to(base_url('login?phone=' . rawurlencode($phone) . '&intent=edit-business'))
                    ->with('info', lang('App.business_exists_sign_in_edit') ?: 'This mobile number already has a business account. Please sign in to edit your listing.');
            }
        }

        session()->set('business_signup_phone', $phone);
        return redirect()->to(base_url('signup?type=business'));
    }

    public function attemptSignup()
    {
        $model    = new AppUserModel();
        $name     = trim((string) $this->request->getPost('name'));
        $type     = strtolower(trim((string) $this->request->getPost('account_type')));
        $type     = $type === 'business' ? 'business' : 'user';
        $password = (string) $this->request->getPost('password');
        $locale   = service('request')->getLocale();
        $locale   = in_array($locale, ['en', 'ur'], true) ? $locale : 'en';

        // Business signup phone must come from the verified gate session
        if ($type === 'business') {
            $verified = (string) (session()->get('business_signup_phone') ?? '');
            if ($verified === '' || strlen($verified) < 10) {
                return redirect()->to(base_url('add-business'))
                    ->with('error', lang('App.add_business_mobile_step') ?: 'Enter your mobile number first.');
            }
            $phone = $verified;

            // Re-check before create (race / tamper)
            $existingBiz = $model->where('phone', $phone)->first();
            if ($existingBiz && ($existingBiz['account_type'] ?? 'user') === 'business') {
                session()->remove('business_signup_phone');
                return redirect()
                    ->to(base_url('login?phone=' . rawurlencode($phone) . '&intent=edit-business'))
                    ->with('info', lang('App.business_exists_sign_in_edit') ?: 'This mobile number already has a business account. Please sign in to edit your listing.');
            }
        } else {
            $phoneRaw = (string) ($this->request->getPost('account_phone') ?: $this->request->getPost('phone'));
            $phone    = $model->normalizePhone($phoneRaw);
        }

        if ($name === '' || strlen($name) < 2) {
            return redirect()->back()->withInput()->with('error', lang('App.name_required') ?: 'Name is required');
        }
        if ($phone === '' || strlen($phone) < 10) {
            return redirect()->back()->withInput()->with('error', lang('App.valid_phone_required') ?: 'Valid mobile number is required');
        }
        if ($type === 'business' && strlen($password) < 6) {
            return redirect()->back()->withInput()->with('error', lang('App.business_password_required') ?: 'Business accounts require a password (min 6 characters)');
        }

        if ($type === 'business') {
            $bizCheck = $this->parseBusinessPost(true);
            if (isset($bizCheck['error'])) {
                return redirect()->back()->withInput()->with('error', $bizCheck['error']);
            }
        }

        $existing = $model->where('phone', $phone)->first();
        if ($existing) {
            $existingType = ($existing['account_type'] ?? 'user') === 'business' ? 'business' : 'user';
            if ($existingType === 'business') {
                session()->remove('business_signup_phone');
                return redirect()->to(base_url('login?phone=' . rawurlencode($phone) . '&intent=edit-business'))
                    ->with('info', lang('App.business_exists_sign_in_edit') ?: 'This mobile number already has a business account. Please sign in to edit your listing.');
            }
            if ($type === 'business') {
                $model->update($existing['id'], [
                    'name'          => $name,
                    'account_type'  => 'business',
                    'password_hash' => $model->hashPassword($password),
                    'locale'        => $locale,
                    'status'        => 'active',
                ]);
                $user = $model->find($existing['id']);
                $this->setAppUserSession($user);
                $this->createPendingBusinessFromRequest((int) $user['id'], $name);
                session()->remove('business_signup_phone');
                return redirect()->to(base_url('dashboard?tab=business'))->with('success', lang('App.upgraded_business') ?: 'Upgraded to business account');
            }

            $model->update($existing['id'], [
                'name'   => $name,
                'locale' => $locale,
            ]);
            $user = $model->find($existing['id']);
            $this->setAppUserSession($user);
            return redirect()->to(base_url('dashboard'))->with('success', lang('App.welcome_back') ?: 'Welcome back');
        }

        $insert = [
            'name'         => $name,
            'phone'        => $phone,
            'account_type' => $type,
            'locale'       => $locale,
            'theme'        => 'light',
            'status'       => 'active',
        ];
        if ($type === 'business') {
            $insert['password_hash'] = $model->hashPassword($password);
        }

        $id = (int) $model->insert($insert, true);
        if ($id < 1) {
            return redirect()->back()->withInput()->with('error', lang('App.could_not_create_account') ?: 'Could not create account');
        }

        $user = $model->find($id);
        $this->setAppUserSession($user);

        if ($type === 'business') {
            $this->createPendingBusinessFromRequest($id, $name);
            session()->remove('business_signup_phone');
            return redirect()->to(base_url('dashboard?tab=business'))
                ->with('success', lang('App.business_submitted_review') ?: 'Business submitted for admin review');
        }

        return redirect()->to(base_url('dashboard'))->with('success', lang('App.account_created') ?: 'Account created');
    }

    public function logout()
    {
        session()->remove([
            'app_user_id',
            'app_user_name',
            'app_user_phone',
            'app_user_type',
            'app_user_logged_in',
        ]);
        return redirect()->to(base_url('login'))->with('success', lang('App.logged_out') ?: 'Logged out');
    }

    public function dashboard()
    {
        $user = $this->currentAppUser();
        if (! $user) {
            return redirect()->to(base_url('login'));
        }

        $tab = $this->request->getGet('tab');
        if (! in_array($tab, ['profile', 'business', 'upgrade'], true)) {
            $tab = (($user['account_type'] ?? 'user') === 'business') ? 'business' : 'profile';
        }
        if ($tab === 'business' && ($user['account_type'] ?? 'user') !== 'business') {
            $tab = 'upgrade';
        }
        if ($tab === 'upgrade' && ($user['account_type'] ?? 'user') === 'business') {
            $tab = 'business';
        }
        $businesses = [];
        if (($user['account_type'] ?? 'user') === 'business') {
            $businesses = (new BusinessModel())
                ->where('user_id', (int) $user['id'])
                ->orderBy('id', 'DESC')
                ->findAll();
        }

        return view('dashboard', [
            'lang'        => service('request')->getLocale(),
            'title'       => lang('App.dashboard_my_profile'),
            'user'        => $user,
            'businesses'  => $businesses,
            'currentTab'  => $tab,
            'hasBusiness' => ! empty($businesses),
        ]);
    }

    public function updateProfile()
    {
        $user = $this->currentAppUser();
        if (! $user) {
            return redirect()->to(base_url('login'));
        }

        $model = new AppUserModel();
        $name  = trim((string) $this->request->getPost('name'));
        $phone = $model->normalizePhone((string) $this->request->getPost('phone'));
        $data  = [];

        if ($name === '' || strlen($name) < 2) {
            return redirect()->back()->with('error', lang('App.name_required') ?: 'Name is required');
        }
        $data['name'] = $name;

        if ($phone !== '' && strlen($phone) >= 10) {
            $dup = $model->where('phone', $phone)->where('id !=', $user['id'])->first();
            if ($dup) {
                return redirect()->back()->with('error', lang('App.phone_in_use') ?: 'Phone number already in use');
            }
            $data['phone'] = $phone;
        }

        $password = (string) $this->request->getPost('password');
        if ($password !== '') {
            if (strlen($password) < 6) {
                return redirect()->back()->with('error', lang('App.password_min') ?: 'Password must be at least 6 characters');
            }
            // Only existing business accounts can change password here.
            // Community users must use "Switch to business".
            if (($user['account_type'] ?? 'user') === 'business') {
                $data['password_hash'] = $model->hashPassword($password);
            }
        }

        $model->update($user['id'], $data);
        $fresh = $model->find($user['id']);
        $this->setAppUserSession($fresh);

        return redirect()->to(base_url('dashboard'))->with('success', lang('App.profile_updated') ?: 'Profile updated');
    }

    /**
     * Switch community user → business account (set password).
     * Listing stays tied to this mobile/contact number (one business max).
     */
    public function upgradeBusiness()
    {
        $user = $this->currentAppUser();
        if (! $user) {
            return redirect()->to(base_url('login'));
        }

        $password = (string) $this->request->getPost('password');
        $confirm  = (string) $this->request->getPost('password_confirm');

        if (strlen($password) < 6) {
            return redirect()->back()->with('error', lang('App.password_min') ?: 'Password must be at least 6 characters');
        }
        if ($confirm !== '' && $confirm !== $password) {
            return redirect()->back()->with('error', lang('App.password_mismatch') ?: 'Password confirmation does not match');
        }

        $model = new AppUserModel();
        $model->update((int) $user['id'], [
            'account_type'  => 'business',
            'password_hash' => $model->hashPassword($password),
            'status'        => 'active',
        ]);
        $fresh = $model->find((int) $user['id']);
        $this->setAppUserSession($fresh);

        return redirect()->to(base_url('dashboard?tab=business'))
            ->with('success', lang('App.upgraded_business') ?: 'Switched to business account. You can add your listing now.');
    }

    public function businessCreate()
    {
        $user = $this->requireBusinessUser();
        if ($user instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $user;
        }

        $existing = (new BusinessModel())->findOwnedByUser((int) $user['id']);
        if ($existing) {
            return redirect()->to(base_url('dashboard/business/edit/' . $existing['id']))
                ->with('error', lang('App.one_business_per_account') ?: 'Only one business is allowed per mobile number. You can update your existing listing.');
        }

        return view('account/business_form', [
            'lang'       => service('request')->getLocale(),
            'title'      => lang('App.dashboard_my_business_listing'),
            'user'       => $user,
            'business'   => null,
            'categories' => (new CategoryModel())->orderBy('name_en', 'ASC')->findAll(),
            'areas'      => (new AreaModel())->orderBy('name_en', 'ASC')->findAll(),
            'villages'   => (new VillageModel())->orderBy('name_en', 'ASC')->findAll(),
        ]);
    }

    public function businessStore()
    {
        $user = $this->requireBusinessUser();
        if ($user instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $user;
        }

        $existing = (new BusinessModel())->findOwnedByUser((int) $user['id']);
        if ($existing) {
            return redirect()->to(base_url('dashboard/business/edit/' . $existing['id']))
                ->with('error', lang('App.one_business_per_account') ?: 'Only one business is allowed per mobile number. You can update your existing listing.');
        }

        $parsed = $this->parseBusinessPost(true);
        if (isset($parsed['error'])) {
            return redirect()->back()->withInput()->with('error', $parsed['error']);
        }

        helper('seo');
        $model = new BusinessModel();
        $accountPhone = (new AppUserModel())->normalizePhone((string) ($user['phone'] ?? ''));
        if ($accountPhone !== '' && $model->contactPhoneHasBusiness($accountPhone)) {
            return redirect()->to(base_url('dashboard?tab=business'))
                ->with('error', lang('App.one_business_per_account') ?: 'A business is already registered with this contact number.');
        }

        $photo = $this->handleImageUpload();
        $data  = $parsed['data'] + [
            'user_id'    => (int) $user['id'],
            'owner_name' => $user['name'],
            'phone'      => $accountPhone,
            'slug'       => 'pending-' . bin2hex(random_bytes(4)),
            'status'     => 'pending',
            'featured'   => 0,
        ];
        if ($accountPhone !== '') {
            $data['phone'] = $accountPhone;
        }
        if ($photo) {
            $data['image'] = $photo;
        }

        $id = (int) $model->insert($data);
        if ($id < 1) {
            return redirect()->back()->withInput()->with('error', 'Could not create business');
        }

        $slug = make_unique_listing_seo_slug($data['name_en'], $id);
        $model->update($id, ['slug' => $slug]);

        return redirect()->to(base_url('dashboard?tab=business'))
            ->with('success', lang('App.business_submitted_review') ?: 'Business submitted for admin review');
    }

    public function businessEdit($id = null)
    {
        $user = $this->requireBusinessUser();
        if ($user instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $user;
        }

        $biz = $this->ownedBusiness((int) $id, (int) $user['id']);
        if (! $biz) {
            return redirect()->to(base_url('dashboard?tab=business'))->with('error', 'Business not found');
        }

        return view('account/business_form', [
            'lang'       => service('request')->getLocale(),
            'title'      => lang('App.dashboard_my_business_listing'),
            'user'       => $user,
            'business'   => $biz,
            'categories' => (new CategoryModel())->orderBy('name_en', 'ASC')->findAll(),
            'areas'      => (new AreaModel())->orderBy('name_en', 'ASC')->findAll(),
            'villages'   => (new VillageModel())->orderBy('name_en', 'ASC')->findAll(),
        ]);
    }

    public function businessUpdate($id = null)
    {
        $user = $this->requireBusinessUser();
        if ($user instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $user;
        }

        $biz = $this->ownedBusiness((int) $id, (int) $user['id']);
        if (! $biz) {
            return redirect()->to(base_url('dashboard?tab=business'))->with('error', 'Business not found');
        }

        $parsed = $this->parseBusinessPost(false);
        if (isset($parsed['error'])) {
            return redirect()->back()->withInput()->with('error', $parsed['error']);
        }

        helper('seo');
        $data = $parsed['data'];
        if (isset($data['name_en']) && $data['name_en'] !== '') {
            $data['slug'] = make_unique_listing_seo_slug($data['name_en'], (int) $biz['id']);
        }
        if (($biz['status'] ?? '') === 'inactive') {
            $data['status'] = 'pending';
        }
        $photo = $this->handleImageUpload();
        if ($photo) {
            $data['image'] = $photo;
        }

        (new BusinessModel())->update((int) $biz['id'], $data);

        return redirect()->to(base_url('dashboard?tab=business'))
            ->with('success', lang('App.business_updated') ?: 'Business updated');
    }

    private function setAppUserSession(array $user): void
    {
        session()->set([
            'app_user_id'        => (int) $user['id'],
            'app_user_name'      => $user['name'],
            'app_user_phone'     => $user['phone'],
            'app_user_type'      => ($user['account_type'] ?? 'user') === 'business' ? 'business' : 'user',
            'app_user_logged_in' => true,
        ]);
    }

    private function currentAppUser(): ?array
    {
        $id = (int) (session()->get('app_user_id') ?? 0);
        if ($id < 1 || ! session()->get('app_user_logged_in')) {
            return null;
        }
        $user = (new AppUserModel())->find($id);
        if (! $user || ($user['status'] ?? 'active') !== 'active') {
            session()->remove(['app_user_id', 'app_user_name', 'app_user_phone', 'app_user_type', 'app_user_logged_in']);
            return null;
        }
        return $user;
    }

    /**
     * @return array|\CodeIgniter\HTTP\RedirectResponse
     */
    private function requireBusinessUser()
    {
        $user = $this->currentAppUser();
        if (! $user) {
            return redirect()->to(base_url('login'));
        }
        if (($user['account_type'] ?? 'user') !== 'business') {
            return redirect()->to(base_url('dashboard'))->with('error', lang('App.business_only') ?: 'Only business accounts can manage listings');
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
     * Create pending listing from the full public business form (signup / dashboard).
     * Enforces one business per account/mobile.
     */
    private function createPendingBusinessFromRequest(int $userId, string $ownerName): void
    {
        $model = new BusinessModel();
        if ($model->userHasBusiness($userId)) {
            return;
        }

        $parsed = $this->parseBusinessPost(true);
        if (isset($parsed['error'])) {
            return;
        }

        helper('seo');
        $photo = $this->handleImageUpload();
        $accountPhone = (new AppUserModel())->normalizePhone((string) (session()->get('app_user_phone') ?: ''));

        if ($accountPhone !== '' && $model->contactPhoneHasBusiness($accountPhone)) {
            return;
        }

        $data  = $parsed['data'] + [
            'user_id'    => $userId,
            'owner_name' => $ownerName,
            'slug'       => 'pending-' . bin2hex(random_bytes(4)),
            'status'     => 'pending',
            'featured'   => 0,
        ];
        // Business is registered against the account contact number
        $data['phone'] = $accountPhone !== '' ? $accountPhone : ($data['phone'] ?? '');
        if ($photo) {
            $data['image'] = $photo;
        }

        $id = (int) $model->insert($data);
        if ($id > 0 && ! empty($data['name_en'])) {
            $model->update($id, ['slug' => make_unique_listing_seo_slug($data['name_en'], $id)]);
        }
    }

    private function handleImageUpload(): ?string
    {
        $file = $this->request->getFile('image');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $dir = FCPATH . 'uploads/businesses';
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $newName = $file->getRandomName();
        if (! $file->move($dir, $newName)) {
            return null;
        }

        return 'uploads/businesses/' . $newName;
    }

    /**
     * @return array{data?:array<string,mixed>,error?:string}
     */
    private function parseBusinessPost(bool $requireName): array
    {
        $nameEn = trim((string) $this->request->getPost('name_en'));
        $nameUr = trim((string) $this->request->getPost('name_ur'));
        if ($requireName && ($nameEn === '' || strlen($nameEn) < 2)) {
            return ['error' => 'Business name is required'];
        }

        $categoryId = (int) $this->request->getPost('category_id');
        if ($requireName && $categoryId < 1) {
            return ['error' => 'Category is required'];
        }
        if ($categoryId > 0 && ! (new CategoryModel())->find($categoryId)) {
            return ['error' => 'Invalid category'];
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
            $v = $this->request->getPost($fk);
            if ($v !== null && $v !== '') {
                $data[$fk] = (int) $v ?: null;
            }
        }

        foreach (['address_en', 'address_ur', 'description_en', 'description_ur', 'phone', 'whatsapp', 'email', 'website', 'opening_hours'] as $field) {
            if ($this->request->getPost($field) !== null) {
                $data[$field] = trim((string) $this->request->getPost($field));
            }
        }

        return ['data' => $data];
    }
}
