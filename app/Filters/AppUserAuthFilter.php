<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AppUserAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $params = null)
    {
        if (! session()->get('app_user_logged_in') || ! session()->get('app_user_id')) {
            return redirect()->to(base_url('login'))->with('error', lang('App.login_required') ?: 'Please log in to continue.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $params = null)
    {
    }
}
