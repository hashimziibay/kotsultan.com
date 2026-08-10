<?php

namespace App\Controllers\Api;

use App\Models\AppUserModel;

class AuthController extends BaseApiController
{
    public function register()
    {
        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $name    = trim((string) ($payload['name'] ?? ''));
        $phone   = (new AppUserModel())->normalizePhone((string) ($payload['phone'] ?? ''));
        $locale  = in_array(($payload['locale'] ?? 'en'), ['en', 'ur'], true) ? $payload['locale'] : 'en';
        $theme   = in_array(($payload['theme'] ?? 'light'), ['light', 'dark'], true) ? $payload['theme'] : 'light';

        if ($name === '' || strlen($name) < 2) {
            return $this->jsonError('Name is required', 422);
        }
        if ($phone === '' || strlen($phone) < 10) {
            return $this->jsonError('Valid contact number is required', 422);
        }

        $model = new AppUserModel();
        $existing = $model->where('phone', $phone)->first();

        if ($existing) {
            // Upsert-style: update profile prefs and re-issue token
            $model->update($existing['id'], [
                'name'   => $name,
                'locale' => $locale,
                'theme'  => $theme,
            ]);
            $token = $model->issueToken((int) $existing['id']);
            $user  = $model->find($existing['id']);
            return $this->jsonOk([
                'token' => $token,
                'user'  => $model->publicProfile($user),
                'is_new' => false,
            ], 'Welcome back');
        }

        $id = $model->insert([
            'name'   => $name,
            'phone'  => $phone,
            'locale' => $locale,
            'theme'  => $theme,
        ], true);

        if (!$id) {
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
        $phone   = (new AppUserModel())->normalizePhone((string) ($payload['phone'] ?? ''));

        if ($phone === '' || strlen($phone) < 10) {
            return $this->jsonError('Valid contact number is required', 422);
        }

        $model = new AppUserModel();
        $user  = $model->where('phone', $phone)->first();
        if (!$user) {
            return $this->jsonError('Account not found. Please sign up.', 404);
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
        if (!$user) {
            return $this->jsonError('Unauthorized', 401);
        }
        return $this->jsonOk((new AppUserModel())->publicProfile($user));
    }

    public function updateMe()
    {
        $user = $this->currentAppUser();
        if (!$user) {
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

        if ($data !== []) {
            $model->update($user['id'], $data);
        }

        $fresh = $model->find($user['id']);
        return $this->jsonOk($model->publicProfile($fresh), 'Profile updated');
    }
}
