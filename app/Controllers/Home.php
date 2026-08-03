<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\BusinessModel;
use App\Models\WallModel;
use App\Models\TagModel;

class Home extends BaseController
{
    protected function setLocale()
    {
        $session = session();
        $request = service('request');

        // Check GET param first, then Session, then Cookie, default to 'en'
        $getLang = $this->request->getGet('lang');
        $sessionLang = $session->get('lang');
        $cookieLang = $this->request->getCookie('lang');

        $lang = $getLang ?? $sessionLang ?? $cookieLang ?? 'en';
        if (!in_array($lang, ['en', 'ur'])) {
            $lang = 'en';
        }

        $session->set('lang', $lang);
        $this->request->setLocale($lang);
        service('language')->setLocale($lang);

        return $lang;
    }

    public function lang($locale)
    {
        $session = session();

        if (in_array($locale, ['en', 'ur'])) {
            $session->set('lang', $locale);
            setcookie('lang', $locale, time() + (86400 * 365), '/');
            $this->request->setLocale($locale);
            service('language')->setLocale($locale);
        }

        $referer = $this->request->getServer('HTTP_REFERER');
        if ($referer) {
            // Remove existing lang query param if present
            $refererPath = strtok($referer, '?');
            return redirect()->to($refererPath . '?lang=' . $locale);
        }
        return redirect()->to(base_url('?lang=' . $locale));
    }

    public function index()
    {
        $lang = $this->setLocale();

        $categoryModel = new CategoryModel();
        $businessModel = new BusinessModel();
        $wallModel     = new WallModel();

        $categories       = $categoryModel->getActiveCategories();
        $recentBusinesses = $businessModel->getRecentBusinesses(6);
        $wallEntries      = $wallModel->getActiveWallEntries();

        // Compute dynamic directory statistics from database
        $categoryCounts = $businessModel->getCategoryCounts();
        $totalBusinesses = 0;
        $categoryTotalsMap = [];
        foreach ($categoryCounts as $row) {
            $totalBusinesses += $row['total'];
            $categoryTotalsMap[$row['category_id']] = $row['total'];
        }

        $stats = [
            'total_businesses' => $totalBusinesses,
            'categories_count' => count($categories),
            'category_totals'  => $categoryTotalsMap,
        ];

        return view('home', [
            'lang'             => $lang,
            'categories'       => $categories,
            'recentBusinesses' => $recentBusinesses,
            'wallEntries'      => $wallEntries,
            'stats'            => $stats,
        ]);
    }

    public function directory()
    {
        $lang = $this->setLocale();

        $categoryModel = new CategoryModel();
        $businessModel = new BusinessModel();
        $tagModel      = new TagModel();

        $query      = $this->request->getGet('q');
        $categoryId = $this->request->getGet('category');
        $tagId      = $this->request->getGet('tag');

        $categories = $categoryModel->getActiveCategories();
        $businesses = $businessModel->searchDirectory($query, $categoryId, $tagId);

        // Compute category totals
        $categoryCounts = $businessModel->getCategoryCounts();
        $categoryTotalsMap = [];
        foreach ($categoryCounts as $row) {
            $categoryTotalsMap[$row['category_id']] = $row['total'];
        }

        return view('directory', [
            'lang'             => $lang,
            'categories'       => $categories,
            'businesses'       => $businesses,
            'searchQuery'      => $query,
            'selectedCategory' => $categoryId,
            'categoryTotals'   => $categoryTotalsMap,
        ]);
    }

    public function listings()
    {
        return $this->directory();
    }

    public function wall()
    {
        $lang = $this->setLocale();

        $wallModel   = new WallModel();
        $wallEntries = $wallModel->getActiveWallEntries();

        return view('wall', [
            'lang'        => $lang,
            'wallEntries' => $wallEntries,
        ]);
    }

    public function volunteer()
    {
        $lang = $this->setLocale();

        $helpOptions = [
            ['value' => 'add_businesses', 'label_en' => 'Add missing businesses', 'label_ur' => 'غائب کاروبار شامل کریں'],
            ['value' => 'update_info', 'label_en' => 'Update business information', 'label_ur' => 'کاروباری معلومات اپ ڈیٹ کریں'],
            ['value' => 'report_incorrect', 'label_en' => 'Report incorrect details', 'label_ur' => 'غلط تفصیلات کی اطلاع دیں'],
            ['value' => 'suggest_categories', 'label_en' => 'Suggest new categories', 'label_ur' => 'نئے زمرہ جات تجویز کریں'],
            ['value' => 'share_history', 'label_en' => 'Share historical information', 'label_ur' => 'تاریخی معلومات شیئر کریں'],
            ['value' => 'translate', 'label_en' => 'Help translate English ↔ Urdu', 'label_ur' => 'انگریزی ↔ اردو ترجمے میں مدد کریں'],
            ['value' => 'recommend_places', 'label_en' => 'Recommend places', 'label_ur' => 'مقامات کی سفارش کریں'],
            ['value' => 'verify_locations', 'label_en' => 'Verify business locations', 'label_ur' => 'کاروباری مقامات کی تصدیق کریں'],
        ];

        return view('volunteer', [
            'lang'        => $lang,
            'helpOptions' => $helpOptions,
        ]);
    }

    public function about()
    {
        $lang = $this->setLocale();

        $wallModel     = new WallModel();
        $businessModel = new BusinessModel();
        $categoryModel = new CategoryModel();

        $wallEntries   = $wallModel->getActiveWallEntries();
        $categories    = $categoryModel->getActiveCategories();

        $categoryCounts = $businessModel->getCategoryCounts();
        $totalBusinesses = 0;
        foreach ($categoryCounts as $row) {
            $totalBusinesses += $row['total'];
        }

        $stats = [
            'total_businesses' => $totalBusinesses,
            'categories_count' => count($categories),
            'wall_count'       => count($wallEntries),
        ];

        return view('about', [
            'lang'        => $lang,
            'wallEntries' => $wallEntries,
            'stats'       => $stats,
        ]);
    }

    public function contact()
    {
        $lang = $this->setLocale();

        // Local Kot Sultan Emergency & Service Contacts
        $emergencyContacts = [
            [
                'name_en' => 'Police Station Kot Sultan',
                'name_ur' => 'تھانہ کوٹ سلطان',
                'phone'   => '068-5544100',
                'category' => 'Police / Law',
                'icon'    => 'shield-alert',
            ],
            [
                'name_en' => 'THQ Hospital Kot Sultan',
                'name_ur' => 'تحصیل ہیڈکوارٹر ہسپتال کوٹ سلطان',
                'phone'   => '068-5544200',
                'category' => 'Medical / Emergency',
                'icon'    => 'hospital',
            ],
            [
                'name_en' => 'Rescue 1122 Emergency',
                'name_ur' => 'ریسکیو 1122 ایمرجنسی',
                'phone'   => '1122',
                'category' => 'Disaster / Ambulance',
                'icon'    => 'ambulance',
            ],
            [
                'name_en' => 'MEPCO WAPDA Grid Station',
                'name_ur' => 'میپکو واپڈا گرڈ اسٹیشن',
                'phone'   => '068-5544300',
                'category' => 'Electricity / Power',
                'icon'    => 'zap',
            ],
            [
                'name_en' => 'Kot Sultan Press Club',
                'name_ur' => 'کوٹ سلطان پریس کلب',
                'phone'   => '0305-6660169',
                'category' => 'Media / News',
                'icon'    => 'newspaper',
            ],
        ];

        return view('contact', [
            'lang'              => $lang,
            'emergencyContacts' => $emergencyContacts,
        ]);
    }

    public function business()
    {
        $lang = $this->setLocale();
        return view('business', ['lang' => $lang]);
    }

    public function login()
    {
        $lang = $this->setLocale();
        return view('login', ['lang' => $lang]);
    }

    public function signup()
    {
        $lang = $this->setLocale();
        return view('signup', ['lang' => $lang]);
    }

    public function dashboard()
    {
        $lang = $this->setLocale();
        return view('dashboard', ['lang' => $lang]);
    }

    public function admin()
    {
        $lang = $this->setLocale();
        
        $categoryModel = new CategoryModel();
        $businessModel = new BusinessModel();
        $wallModel     = new WallModel();
        $tagModel      = new TagModel();

        return view('admin', [
            'lang'       => $lang,
            'categories' => $categoryModel->findAll(),
            'businesses' => $businessModel->findAll(),
            'wall'       => $wallModel->findAll(),
            'tags'       => $tagModel->findAll(),
        ]);
    }

    public function not_found()
    {
        $this->setLocale();
        return view('errors/html/error_404');
    }
}
