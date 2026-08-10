<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LocaleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $params = null)
    {
        $session = session();
        
        $sessionLang = $session->get('lang');
        $cookieLang  = $request->getCookie('lang');
        $defaultLang = config('App')->defaultLocale;

        // Priority: Session > Cookie > Default config
        $lang = $sessionLang ?? $cookieLang ?? $defaultLang;

        if (!in_array($lang, ['en', 'ur'], true)) {
            $lang = $defaultLang;
        }

        // Store in session if not present or changed
        if ($sessionLang !== $lang) {
            $session->set('lang', $lang);
        }

        // Enforce CodeIgniter request & language service locale
        $request->setLocale($lang);
        service('language')->setLocale($lang);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $params = null)
    {
        // No post-processing needed
    }
}
