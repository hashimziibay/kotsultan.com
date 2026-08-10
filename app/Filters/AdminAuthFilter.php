<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $params = null)
    {
        $session = session();

        if (!$session->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'))->with('error', 'Please log in to access the Admin Panel.');
        }

        $uriPath = trim($request->getUri()->getPath(), '/');
        // Handle subfolder bases (e.g., kts web project/public/admin/...)
        $segments = explode('/', $uriPath);
        $adminIndex = array_search('admin', $segments);
        
        $subPath = '';
        if ($adminIndex !== false && isset($segments[$adminIndex + 1])) {
            $subPath = $segments[$adminIndex + 1];
        }

        // Force password change if temporary password is still active
        if ($session->get('must_change_password') && !in_array($subPath, ['change-password', 'logout'])) {
            return redirect()->to(base_url('admin/change-password'))->with('warning', 'You must change your temporary password before accessing the dashboard.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $params = null)
    {
        // No post-processing needed
    }
}
