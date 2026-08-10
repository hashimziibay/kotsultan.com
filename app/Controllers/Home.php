<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\BusinessModel;
use App\Models\WallModel;
use App\Models\TagModel;

class Home extends BaseController
{
    public function lang($locale)
    {
        if (!in_array($locale, ['en', 'ur'], true)) {
            $locale = config('App')->defaultLocale;
        }

        session()->set('lang', $locale);

        // Use CodeIgniter's Response object to set the cookie
        $this->response->setCookie([
            'name'     => 'lang',
            'value'    => $locale,
            'expire'   => 86400 * 365,
            'path'     => '/',
            'secure'   => false,
            'httponly' => true,
        ]);

        // Preserve the current page when switching language. The referer is a
        // full URL that already contains the base path, so redirecting to it
        // verbatim can never duplicate the project directory (the old bug that
        // produced /public/index.php/kts%20web%20project/public/... -> 404).
        $referer = $this->request->getServer('HTTP_REFERER');
        if (is_string($referer) && $referer !== '') {
            $refHost = (string) parse_url($referer, PHP_URL_HOST);
            $ourHost = (string) parse_url(base_url(), PHP_URL_HOST);

            // Cross-host referer: never follow it (open-redirect safety).
            if ($refHost === '' || strtolower($refHost) === strtolower($ourHost)) {
                // Normalize the referer path so the /lang/ loop guard matches
                // regardless of percent-encoding or a leading index.php.
                $refPath = rawurldecode(ltrim((string) parse_url($referer, PHP_URL_PATH), '/'));
                $refPath = preg_replace('#^index\.php/?#i', '', $refPath);

                // Strip the app's own base path (decoded) so the guard works on
                // any deployment (subfolder like /kts web project/public or root).
                $basePath = rawurldecode(ltrim(rtrim((string) parse_url(base_url(), PHP_URL_PATH), '/'), '/'));
                if ($basePath !== '') {
                    $refPath = preg_replace('#^' . preg_quote($basePath, '#') . '/?#i', '', $refPath);
                }

                // If the referer is the /lang/ route itself, redirecting there
                // would loop forever -> fall back to the homepage.
                if (preg_match('~^lang/(en|ur)(?:[/?#]|$)~i', $refPath)) {
                    return redirect()->to(base_url('/'));
                }

                return redirect()->to($referer);
            }
        }

        return redirect()->to(base_url('/'));
    }

    public function index()
    {
        $lang = service('request')->getLocale();

        $categoryModel = new CategoryModel();
        $businessModel = new BusinessModel();

        $allCategories = $categoryModel->getActiveCategories();

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
            'categories_count' => count($allCategories),
            'category_totals'  => $categoryTotalsMap,
        ];

        // ---------------------------------------------------------
        // Generate Popular Categories: Max 6, "Utensil Stores" first
        // ---------------------------------------------------------
        $popularCategories = [];
        $otherCategories   = [];
        $utensilCategory   = null;

        foreach ($allCategories as $cat) {
            if ($utensilCategory === null && stripos($cat['name_en'] ?? '', 'utensil') !== false) {
                $utensilCategory = $cat;
            } else {
                $otherCategories[] = $cat;
            }
        }

        if ($utensilCategory !== null) {
            $popularCategories[] = $utensilCategory;
        }

        // Shuffle and pick up to 5 random categories to make it 6 in total
        shuffle($otherCategories);
        $needed = 6 - count($popularCategories);
        $randomSelections = array_slice($otherCategories, 0, $needed);
        $popularCategories = array_merge($popularCategories, $randomSelections);

        // ---------------------------------------------------------
        // Generate Recent Businesses: Max 3, "Bismillah Utensil Store" first
        // ---------------------------------------------------------
        $finalRecent = [];
        $bismillahStore = $businessModel->getLocalizedBusiness(698); // Bismillah Utensils Store

        if ($bismillahStore !== null) {
            $finalRecent[] = $bismillahStore;
        }

        $recentPool = $businessModel->getRecentBusinesses(8);
        $otherRecent = [];
        
        foreach ($recentPool as $bus) {
            if ($bus['id'] != 698) {
                $otherRecent[] = $bus;
            }
        }

        shuffle($otherRecent);
        $neededRecent = 3 - count($finalRecent);
        $randomRecent = array_slice($otherRecent, 0, $neededRecent);
        $finalRecent = array_merge($finalRecent, $randomRecent);

        return view('home', [
            'lang'              => $lang,
            'title'             => lang('App.brand_name') . ' - ' . lang('App.brand_tagline'),
            'metaDescription'   => lang('App.about_text'),
            'categories'        => $allCategories,
            'popularCategories' => $popularCategories,
            'recentBusinesses'  => $finalRecent,
            'wallEntries'       => [],
            'stats'             => $stats,
        ]);
    }

    public function directory($categorySlug = null)
    {
        $lang = service('request')->getLocale();
        helper('seo');

        $categoryModel = new CategoryModel();
        $businessModel = new BusinessModel();

        $query      = $this->request->getGet('q');
        $categoryId = $this->request->getGet('category');
        $tagId      = $this->request->getGet('tag');

        if ($categorySlug) {
            $categoryId = $categorySlug;
        }

        $categories = $categoryModel->getActiveCategories();

        // Resolve numeric ?category=161 (or SEO path) to the category row for redirects + filtering.
        $activeCategory = null;
        if (! empty($categoryId)) {
            foreach ($categories as $cat) {
                $match = ctype_digit((string) $categoryId)
                    ? ((int) $cat['id'] === (int) $categoryId)
                    : (
                        ($cat['slug'] ?? '') === seo_strip_place_suffix((string) $categoryId)
                        || ($cat['seo_slug'] ?? '') === (string) $categoryId
                        || ($cat['slug'] ?? '') === (string) $categoryId
                    );
                if ($match) {
                    $activeCategory = $cat;
                    break;
                }
            }
        }

        // Always prefer pretty SEO URLs: /directory/{name}-in-kot-sultan
        // Redirect old ?category=161 (and non-pretty query slugs) with 301.
        if ($activeCategory && $categorySlug === null) {
            $qs = [];
            if ($query) {
                $qs['q'] = $query;
            }
            $page = (int) ($this->request->getGet('page') ?? 1);
            if ($page > 1) {
                $qs['page'] = $page;
            }
            $target = ($activeCategory['url'] ?? base_url('directory/' . ($activeCategory['seo_slug'] ?? $activeCategory['slug'])));
            if ($qs) {
                $target .= '?' . http_build_query($qs);
            }
            return redirect()->to($target, 301);
        }

        if ($activeCategory && $categorySlug !== null) {
            $wanted = $activeCategory['seo_slug'] ?? seo_category_path_slug($activeCategory);
            if ((string) $categorySlug !== (string) $wanted) {
                $qs = [];
                if ($query) {
                    $qs['q'] = $query;
                }
                $page = (int) ($this->request->getGet('page') ?? 1);
                if ($page > 1) {
                    $qs['page'] = $page;
                }
                $target = base_url('directory/' . $wanted) . ($qs ? ('?' . http_build_query($qs)) : '');
                return redirect()->to($target, 301);
            }
        }

        $page       = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filterKey  = $activeCategory['id'] ?? $categoryId;
        $searchData = $businessModel->searchDirectory($query, $filterKey, $tagId, $page, 30);

        // Compute category totals
        $categoryCounts = $businessModel->getCategoryCounts();
        $categoryTotalsMap = [];
        foreach ($categoryCounts as $row) {
            $categoryTotalsMap[$row['category_id']] = $row['total'];
        }

        $selectedCategory = $activeCategory['id'] ?? null;
        $directoryBaseUrl = $activeCategory['url'] ?? base_url('directory');
        $pageTitle = lang('App.nav_directory') . ' | ' . lang('App.brand_name');
        $metaDescription = lang('App.directory_subtitle');

        if ($activeCategory) {
            $pageTitle = ($activeCategory['display_name'] ?? $activeCategory['name_en']) . ' in Kot Sultan | ' . lang('App.brand_name');
            $metaDescription = 'Find ' . ($activeCategory['name_en'] ?? 'local') . ' listings in Kot Sultan, District Layyah.';
        }

        return view('directory', [
            'lang'             => $lang,
            'title'            => $pageTitle,
            'metaDescription'  => $metaDescription,
            'canonical'        => current_url(),
            'categories'       => $categories,
            'businesses'       => $searchData['businesses'],
            'totalResults'     => $searchData['total'],
            'currentPage'      => $searchData['page'],
            'totalPages'       => $searchData['totalPages'],
            'perPage'          => $searchData['perPage'],
            'searchQuery'      => $query,
            'selectedCategory' => $selectedCategory,
            'directoryBaseUrl' => $directoryBaseUrl,
            'categoryTotals'   => $categoryTotalsMap,
        ]);
    }

    public function listings()
    {
        return $this->directory();
    }

    public function emergency()
    {
        $lang = service('request')->getLocale();

        $emergencyModel = new \App\Models\EmergencyModel();

        $query      = $this->request->getGet('q');
        $category   = $this->request->getGet('category') ?? 'all';
        $page       = max(1, (int) ($this->request->getGet('page') ?? 1));

        $searchData = $emergencyModel->searchEmergencyContacts($query, $category, $page, 60);
        $categories = $emergencyModel->getCategories();

        $allCount = 0;
        foreach ($categories as $cat) {
            $allCount += (int)$cat['count'];
        }

        return view('emergency', [
            'lang'             => $lang,
            'contacts'         => $searchData['contacts'],
            'totalResults'     => $searchData['total'],
            'currentPage'      => $searchData['page'],
            'totalPages'       => $searchData['totalPages'],
            'perPage'          => $searchData['perPage'],
            'searchQuery'      => $query,
            'selectedCategory' => $category,
            'categories'       => $categories,
            'allCount'         => $allCount,
        ]);
    }

    public function wall()
    {
        $lang = service('request')->getLocale();

        $wallCatModel = new \App\Models\WallCategoryModel();
        $wallModel    = new WallModel();

        $query      = $this->request->getGet('q');
        $category   = $this->request->getGet('category');
        $sort       = $this->request->getGet('sort') ?? 'newest';
        $page       = max(1, (int) ($this->request->getGet('page') ?? 1));

        $categories = $wallCatModel->getActiveCategories();
        $searchData = $wallModel->searchWallEntries($query, $category, $sort, $page, 18);

        return view('wall', [
            'lang'             => $lang,
            'wallCategories'   => $categories,
            'wallEntries'      => $searchData['entries'],
            'totalResults'     => $searchData['total'],
            'currentPage'      => $searchData['page'],
            'totalPages'       => $searchData['totalPages'],
            'perPage'          => $searchData['perPage'],
            'searchQuery'      => $query,
            'selectedCategory' => $category,
            'selectedSort'     => $sort,
        ]);
    }

    public function wallProfile($idOrSlug = null)
    {
        $lang = service('request')->getLocale();
        if ($idOrSlug === null) {
            $idOrSlug = $this->request->getGet('slug') ?: $this->request->getGet('id');
        }

        $wallModel   = new WallModel();
        $personality = $wallModel->getWallPersonality($idOrSlug);

        if (!$personality) {
            return redirect()->to(base_url('wall-of-kot-sultan'));
        }

        $related = $wallModel->getRelatedPersonalities($personality['category_id'] ?? null, $personality['id'], 3);

        return view('wall_profile', [
            'lang'        => $lang,
            'item'        => $personality,
            'related'     => $related,
        ]);
    }

    public function volunteer()
    {
        $lang = service('request')->getLocale();

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
        $lang = service('request')->getLocale();

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
        $lang = service('request')->getLocale();

        // Local Kot Sultan Emergency & Service Contacts
        $emergencyContacts = [
            [
                'name_en' => 'Police Station Kot Sultan',
                'name_ur' => 'تھانہ کوٹ سلطان',
                'phone'   => '068-5544100',
                'category_en' => 'Police / Law',
                'category_ur' => 'پولیس و قانون',
                'icon'    => 'shield-alert',
            ],
            [
                'name_en' => 'THQ Hospital Kot Sultan',
                'name_ur' => 'تحصیل ہیڈکوارٹر ہسپتال کوٹ سلطان',
                'phone'   => '068-5544200',
                'category_en' => 'Medical / Emergency',
                'category_ur' => 'طبی سہولیات / ایمرجنسی',
                'icon'    => 'hospital',
            ],
            [
                'name_en' => 'Rescue 1122 Emergency',
                'name_ur' => 'ریسکیو 1122 ایمرجنسی',
                'phone'   => '1122',
                'category_en' => 'Disaster / Ambulance',
                'category_ur' => 'آفات / ایمبولینس',
                'icon'    => 'ambulance',
            ],
            [
                'name_en' => 'MEPCO WAPDA Grid Station',
                'name_ur' => 'میپکو واپڈا گرڈ اسٹیشن',
                'phone'   => '068-5544300',
                'category_en' => 'Electricity / Power',
                'category_ur' => 'بجلی اور محکمہ جات',
                'icon'    => 'zap',
            ],
        ];

        return view('contact', [
            'lang'              => $lang,
            'emergencyContacts' => $emergencyContacts,
        ]);
    }

    public function business($idOrSlug = null)
    {
        $lang = service('request')->getLocale();
        helper('seo');

        if ($idOrSlug === null) {
            $idOrSlug = $this->request->getGet('slug') ?: $this->request->getGet('id');
        }

        $businessModel = new BusinessModel();
        $business      = $businessModel->getLocalizedBusiness($idOrSlug);

        if (! $business) {
            return $this->not_found();
        }

        $canonicalSlug = $business['seo_slug'] ?? seo_listing_slug_from_row($business);
        $incoming      = rawurldecode(trim((string) $idOrSlug));

        // Keep DB slug healthy for future requests (fixes legacy Urdu/broken slugs).
        if (! empty($canonicalSlug) && ($business['slug'] ?? '') !== $canonicalSlug) {
            $businessModel->update((int) $business['id'], ['slug' => $canonicalSlug]);
            $business['slug']     = $canonicalSlug;
            $business['seo_slug'] = $canonicalSlug;
            $business['url']      = base_url('listing/' . $canonicalSlug);
        }

        $canonicalUrl = base_url('listing/' . $canonicalSlug);

        // Redirect only when the URL segment is not already the canonical SEO slug
        // (avoids path/baseURL comparison loops on shared hosting).
        $needsRedirect = ($incoming !== $canonicalSlug)
            || $this->request->getGet('id') !== null
            || $this->request->getGet('slug') !== null;

        if ($needsRedirect) {
            return redirect()->to($canonicalUrl, 301);
        }

        $name = $business['display_name'] ?: ($business['name_en'] ?? 'Listing');

        return view('business', [
            'lang'            => $lang,
            'business'        => $business,
            'title'           => $name . ' in Kot Sultan | ' . lang('App.brand_name'),
            'metaDescription' => trim(($business['display_description'] ?? '') ?: ($name . ' in Kot Sultan — contact, address, and details on KotSultan.com')),
            'canonical'       => $canonicalUrl,
        ]);
    }

    public function login()
    {
        $lang = service('request')->getLocale();
        return view('login', ['lang' => $lang]);
    }

    public function signup()
    {
        $lang = service('request')->getLocale();
        return view('signup', ['lang' => $lang]);
    }

    public function dashboard()
    {
        $lang = service('request')->getLocale();
        return view('dashboard', ['lang' => $lang]);
    }

    public function not_found()
    {
        service('request')->getLocale();
        return view('errors/html/error_404');
    }
}
