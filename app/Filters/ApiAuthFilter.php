<?php

namespace App\Filters;

use App\Models\AppUserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        $token = null;

        if (preg_match('/Bearer\s+(\S+)/i', $authHeader, $m)) {
            $token = $m[1];
        } elseif ($request->getHeaderLine('X-Api-Token') !== '') {
            $token = $request->getHeaderLine('X-Api-Token');
        }

        $user = (new AppUserModel())->findByToken($token);
        if (!$user) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        service('request')->appUser = $user;
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
