<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

abstract class BaseApiController extends BaseController
{
    protected function jsonOk($data = null, string $message = 'OK', int $code = 200): ResponseInterface
    {
        return $this->withCors($this->response->setStatusCode($code)->setJSON([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]));
    }

    protected function jsonError(string $message, int $code = 400, $errors = null): ResponseInterface
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];
        if ($errors !== null) {
            $payload['errors'] = $errors;
        }
        return $this->withCors($this->response->setStatusCode($code)->setJSON($payload));
    }

    protected function withCors(ResponseInterface $response): ResponseInterface
    {
        return $response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Headers', 'Authorization, Content-Type, Accept, X-Api-Token, X-App-Locale')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS');
    }

    protected function apiLocale(): string
    {
        $lang = $this->request->getGet('lang')
            ?: $this->request->getHeaderLine('X-App-Locale')
            ?: $this->request->getLocale();

        return in_array($lang, ['en', 'ur'], true) ? $lang : 'en';
    }

    protected function applyLocale(): void
    {
        $locale = $this->apiLocale();
        service('request')->setLocale($locale);
        service('language')->setLocale($locale);
    }

    protected function absoluteUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }
        if (preg_match('#^(https?:)?//#i', $path) || stripos($path, 'data:') === 0) {
            return $path;
        }
        return base_url(ltrim($path, '/'));
    }

    protected function currentAppUser(): ?array
    {
        return service('request')->appUser ?? null;
    }
}
