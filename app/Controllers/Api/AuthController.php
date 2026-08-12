<?php

namespace App\Controllers\Api;

use App\Models\AppUserModel;

class AuthController extends BaseApiController
{
    public function register()
    {
        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $model   = new AppUserModel();

        $name    = trim((string) ($payload['name'] ?? ''));
        $phone   = $model->normalizePhone((string) ($payload['phone'] ?? ''));
        $locale  = in_array(($payload['locale'] ?? 'en'), ['en', 'ur'], true) ? $payload['locale'] : 'en';
        $theme   = in_array(($payload['theme'] ?? 'light'), ['light', 'dark'], true) ? $payload['theme'] : 'light';
        $type    = strtolower(trim((string) ($payload['account_type'] ?? $payload['type'] ?? 'user')));
        $type    = $type === 'business' ? 'business' : 'user';
        $password = (string) ($payload['password'] ?? '');

        if ($name === '' || strlen($name) < 2) {
            return $this->jsonError('Name is required', 422);
        }
        if ($phone === '' || strlen($phone) < 10) {
            return $this->jsonError('Valid contact number is required', 422);
        }
        if ($type === 'business') {
            if (strlen($password) < 6) {
                return $this->jsonError('Business accounts require a password (min 6 characters)', 422);
            }
        }

        $existing = $model->where('phone', $phone)->first();

        if ($existing) {
            $existingType = ($existing['account_type'] ?? 'user') === 'business' ? 'business' : 'user';

            // Business login path when registering again with password
            if ($existingType === 'business') {
                if ($password === '' || ! $model->verifyPassword($existing, $password)) {
                    return $this->jsonError('Business account already exists. Please log in with mobile and password.', 409);
                }
                if (($existing['status'] ?? 'active') !== 'active') {
                    return $this->jsonError('Account is inactive. Contact admin.', 403);
                }
                $model->update($existing['id'], [
                    'name'   => $name,
                    'locale' => $locale,
                    'theme'  => $theme,
                ]);
                $token = $model->issueToken((int) $existing['id']);
                $user  = $model->find($existing['id']);
                return $this->jsonOk([
                    'token'  => $token,
                    'user'   => $model->publicProfile($user),
                    'is_new' => false,
                ], 'Welcome back');
            }

            // Regular user upsert (phone-only, no password)
            if ($type === 'business') {
                // Upgrade existing community user to business account
                $model->update($existing['id'], [
                    'name'          => $name,
                    'account_type'  => 'business',
                    'password_hash' => $model->hashPassword($password),
                    'locale'        => $locale,
                    'theme'         => $theme,
                    'status'        => 'active',
                ]);
                $token = $model->issueToken((int) $existing['id']);
                $user  = $model->find($existing['id']);
                return $this->jsonOk([
                    'token'  => $token,
                    'user'   => $model->publicProfile($user),
                    'is_new' => false,
                ], 'Upgraded to business account', 200);
            }

            $model->update($existing['id'], [
                'name'   => $name,
                'locale' => $locale,
                'theme'  => $theme,
            ]);
            $token = $model->issueToken((int) $existing['id']);
            $user  = $model->find($existing['id']);
            return $this->jsonOk([
                'token'  => $token,
                'user'   => $model->publicProfile($user),
                'is_new' => false,
            ], 'Welcome back');
        }

        $insert = [
            'name'         => $name,
            'phone'        => $phone,
            'account_type' => $type,
            'locale'       => $locale,
            'theme'        => $theme,
            'status'       => 'active',
        ];
        if ($type === 'business') {
            $insert['password_hash'] = $model->hashPassword($password);
        }

        $id = $model->insert($insert, true);
        if (! $id) {
            return $this->jsonError('Could not create account', 500);
        }

        $token = $model->issueToken((int) $id);
        $user  = $model->find($id);

        return $this->jsonOk([
            'token'  => $token,
            'user'   => $model->publicProfile($user),
            'is_new' => true,
        ], 'Account created', 201);
    }

    public function login()
    {
        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $model   = new AppUserModel();
        $phone   = $model->normalizePhone((string) ($payload['phone'] ?? ''));
        $password = (string) ($payload['password'] ?? '');

        if ($phone === '' || strlen($phone) < 10) {
            return $this->jsonError('Valid contact number is required', 422);
        }

        $user = $model->where('phone', $phone)->first();
        if (! $user) {
            return $this->jsonError('Account not found. Please sign up.', 404);
        }
        if (($user['status'] ?? 'active') !== 'active') {
            return $this->jsonError('Account is inactive. Contact admin.', 403);
        }

        $type = ($user['account_type'] ?? 'user') === 'business' ? 'business' : 'user';
        if ($type === 'business') {
            if ($password === '' || ! $model->verifyPassword($user, $password)) {
                return $this->jsonError('Invalid mobile number or password', 401);
            }
        }

        $token = $model->issueToken((int) $user['id']);
        $user  = $model->find($user['id']);

        return $this->jsonOk([
            'token' => $token,
            'user'  => $model->publicProfile($user),
        ]);
    }

    public function me()
    {
        $user = $this->currentAppUser();
        if (! $user) {
            return $this->jsonError('Unauthorized', 401);
        }
        return $this->jsonOk((new AppUserModel())->publicProfile($user));
    }

    public function updateMe()
    {
        $user = $this->currentAppUser();
        if (! $user) {
            return $this->jsonError('Unauthorized', 401);
        }

        $payload = $this->request->getJSON(true) ?: $this->request->getRawInput();
        $model   = new AppUserModel();
        $data    = [];

        if (isset($payload['name'])) {
            $name = trim((string) $payload['name']);
            if ($name === '' || strlen($name) < 2) {
                return $this->jsonError('Name is required', 422);
            }
            $data['name'] = $name;
        }

        if (isset($payload['phone'])) {
            $phone = $model->normalizePhone((string) $payload['phone']);
            if ($phone === '' || strlen($phone) < 10) {
                return $this->jsonError('Valid contact number is required', 422);
            }
            $dup = $model->where('phone', $phone)->where('id !=', $user['id'])->first();
            if ($dup) {
                return $this->jsonError('Phone number already in use', 422);
            }
            $data['phone'] = $phone;
        }

        if (isset($payload['locale']) && in_array($payload['locale'], ['en', 'ur'], true)) {
            $data['locale'] = $payload['locale'];
        }

        if (isset($payload['theme']) && in_array($payload['theme'], ['light', 'dark'], true)) {
            $data['theme'] = $payload['theme'];
        }

        if (isset($payload['password']) && (string) $payload['password'] !== '') {
            $password = (string) $payload['password'];
            if (strlen($password) < 6) {
                return $this->jsonError('Password must be at least 6 characters', 422);
            }
            $data['password_hash'] = $model->hashPassword($password);
            // Password update alone does not switch account type — use upgrade-business.
        }

        if ($data !== []) {
            $model->update($user['id'], $data);
        }

        $fresh = $model->find($user['id']);
        return $this->jsonOk($model->publicProfile($fresh), 'Profile updated');
    }

    /**
     * Switch a community user to a business account (requires password).
     * Business identity stays tied to the same contact/mobile number.
     */
    public function upgradeBusiness()
    {
        $user = $this->currentAppUser();
        if (! $user) {
            return $this->jsonError('Unauthorized', 401);
        }

        $payload  = $this->request->getJSON(true) ?: $this->request->getPost();
        $password = (string) ($payload['password'] ?? '');
        $confirm  = (string) ($payload['password_confirm'] ?? $payload['confirm_password'] ?? '');

        if (strlen($password) < 6) {
            return $this->jsonError('Password must be at least 6 characters', 422);
        }
        if ($confirm !== '' && $confirm !== $password) {
            return $this->jsonError('Password confirmation does not match', 422);
        }

        $model = new AppUserModel();

        if (($user['account_type'] ?? 'user') === 'business' && ! empty($user['password_hash'])) {
            // Already a business account — optionally rotate password
            $model->update((int) $user['id'], [
                'password_hash' => $model->hashPassword($password),
            ]);
            $fresh = $model->find((int) $user['id']);
            return $this->jsonOk($model->publicProfile($fresh), 'Business password updated');
        }

        $model->update((int) $user['id'], [
            'account_type'  => 'business',
            'password_hash' => $model->hashPassword($password),
            'status'        => 'active',
        ]);
        $fresh = $model->find((int) $user['id']);

        return $this->jsonOk($model->publicProfile($fresh), 'Switched to business account');
    }
}
