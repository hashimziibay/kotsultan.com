<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('/directory', 'Home::directory');
$routes->get('/directory/(:segment)', 'Home::directory/$1');
$routes->get('/category/(:segment)', 'Home::directory/$1');
$routes->get('/listings', 'Home::directory'); // Alias for backward compatibility
$routes->get('/wall-of-kot-sultan', 'Home::wall');
$routes->get('/wall-of-kot-sultan/(:segment)', 'Home::wallProfile/$1');
$routes->get('/volunteer', 'Home::volunteer');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');
$routes->get('/listing/(:segment)', 'Home::business/$1');
$routes->get('/business/(:segment)', 'Home::business/$1');
$routes->get('/business', 'Home::business');
$routes->get('/listing', 'Home::directory');

$routes->get('/emergency-numbers', 'Home::emergency');
$routes->get('/emergency', 'Home::emergency'); // Alias

$routes->get('/login', 'AccountController::login');
$routes->post('/login', 'AccountController::attemptLogin');
$routes->get('/signup', 'AccountController::signup');
$routes->post('/signup', 'AccountController::attemptSignup');
$routes->get('/logout', 'AccountController::logout');
$routes->get('/add-business', 'AccountController::addBusinessGate');
$routes->post('/add-business/check', 'AccountController::checkAddBusinessPhone');

$routes->group('', ['filter' => 'appUserAuth'], static function ($routes) {
    $routes->get('/dashboard', 'AccountController::dashboard');
    $routes->post('/dashboard/profile', 'AccountController::updateProfile');
    $routes->get('/dashboard/business/create', 'AccountController::businessCreate');
    $routes->post('/dashboard/business/create', 'AccountController::businessStore');
    $routes->get('/dashboard/business/edit/(:num)', 'AccountController::businessEdit/$1');
    $routes->post('/dashboard/business/edit/(:num)', 'AccountController::businessUpdate/$1');
});

$routes->get('/lang/(:segment)', 'Home::lang/$1');
$routes->get('/404', 'Home::not_found');

// Admin Public Unprotected Routes
$routes->get('/admin/login', 'Admin\AuthController::login');
$routes->post('/admin/login', 'Admin\AuthController::attemptLogin');
$routes->get('/admin/logout', 'Admin\AuthController::logout');

// Admin Protected Routes
$routes->group('admin', ['filter' => 'adminAuth'], static function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->get('change-password', 'Admin\AuthController::changePassword');
    $routes->post('change-password', 'Admin\AuthController::updatePassword');

    // Businesses
    $routes->get('businesses', 'Admin\BusinessController::index');
    $routes->get('businesses/create', 'Admin\BusinessController::create');
    $routes->post('businesses/create', 'Admin\BusinessController::store');
    $routes->get('businesses/edit/(:num)', 'Admin\BusinessController::edit/$1');
    $routes->post('businesses/edit/(:num)', 'Admin\BusinessController::update/$1');
    $routes->post('businesses/delete/(:num)', 'Admin\BusinessController::delete/$1');
    $routes->post('businesses/toggle/(:num)', 'Admin\BusinessController::toggle/$1');

    // App users / business accounts
    $routes->get('app-users', 'Admin\AppUsersController::index');
    $routes->get('app-users/(:num)', 'Admin\AppUsersController::show/$1');
    $routes->post('app-users/(:num)/toggle', 'Admin\AppUsersController::toggle/$1');
    $routes->post('app-users/business/(:num)/approve', 'Admin\AppUsersController::approveBusiness/$1');

    // Duplicates
    $routes->get('duplicates', 'Admin\DuplicateController::index');
    $routes->post('duplicates/merge', 'Admin\DuplicateController::merge');

    // Categories
    $routes->get('categories', 'Admin\CategoryController::index');
    $routes->get('categories/create', 'Admin\CategoryController::create');
    $routes->post('categories/create', 'Admin\CategoryController::store');
    $routes->get('categories/edit/(:num)', 'Admin\CategoryController::edit/$1');
    $routes->post('categories/edit/(:num)', 'Admin\CategoryController::update/$1');
    $routes->post('categories/delete/(:num)', 'Admin\CategoryController::delete/$1');
    $routes->post('categories/toggle/(:num)', 'Admin\CategoryController::toggle/$1');

    // Areas
    $routes->get('areas', 'Admin\AreaController::index');
    $routes->get('areas/create', 'Admin\AreaController::create');
    $routes->post('areas/create', 'Admin\AreaController::store');
    $routes->get('areas/edit/(:num)', 'Admin\AreaController::edit/$1');
    $routes->post('areas/edit/(:num)', 'Admin\AreaController::update/$1');
    $routes->post('areas/delete/(:num)', 'Admin\AreaController::delete/$1');

    // Villages
    $routes->get('villages', 'Admin\VillageController::index');
    $routes->get('villages/create', 'Admin\VillageController::create');
    $routes->post('villages/create', 'Admin\VillageController::store');
    $routes->get('villages/edit/(:num)', 'Admin\VillageController::edit/$1');
    $routes->post('villages/edit/(:num)', 'Admin\VillageController::update/$1');
    $routes->post('villages/delete/(:num)', 'Admin\VillageController::delete/$1');

    // Wall of Kot Sultan
    $routes->get('wall-of-kot-sultan', 'Admin\WallController::index');
    $routes->get('wall-of-kot-sultan/create', 'Admin\WallController::create');
    $routes->post('wall-of-kot-sultan/create', 'Admin\WallController::store');
    $routes->get('wall-of-kot-sultan/edit/(:num)', 'Admin\WallController::edit/$1');
    $routes->post('wall-of-kot-sultan/edit/(:num)', 'Admin\WallController::update/$1');
    $routes->post('wall-of-kot-sultan/delete/(:num)', 'Admin\WallController::delete/$1');
    $routes->post('wall-of-kot-sultan/toggle/(:num)', 'Admin\WallController::toggle/$1');
    $routes->post('wall-of-kot-sultan/(:num)/attachment/(:num)/delete', 'Admin\WallController::deleteAttachment/$1/$2');

    // Emergency Numbers
    $routes->get('emergency-numbers', 'Admin\EmergencyController::index');
    $routes->get('emergency-numbers/create', 'Admin\EmergencyController::create');
    $routes->post('emergency-numbers/create', 'Admin\EmergencyController::store');
    $routes->get('emergency-numbers/edit/(:num)', 'Admin\EmergencyController::edit/$1');
    $routes->post('emergency-numbers/edit/(:num)', 'Admin\EmergencyController::update/$1');
    $routes->post('emergency-numbers/delete/(:num)', 'Admin\EmergencyController::delete/$1');
    $routes->post('emergency-numbers/toggle/(:num)', 'Admin\EmergencyController::toggle/$1');
    $routes->get('emergency-numbers/export', 'Admin\EmergencyController::export');
    $routes->post('emergency-numbers/import', 'Admin\EmergencyController::import');

    // Images
    $routes->get('images', 'Admin\ImageController::index');
    $routes->post('images/upload', 'Admin\ImageController::upload');
    
    // Navigation Links
    $routes->get('nav-links', 'Admin\NavLinkController::index');
    $routes->get('nav-links/create', 'Admin\NavLinkController::create');
    $routes->post('nav-links/create', 'Admin\NavLinkController::store');
    $routes->get('nav-links/edit/(:num)', 'Admin\NavLinkController::edit/$1');
    $routes->post('nav-links/edit/(:num)', 'Admin\NavLinkController::update/$1');
    $routes->post('nav-links/delete/(:num)', 'Admin\NavLinkController::delete/$1');
    $routes->post('nav-links/toggle/(:num)', 'Admin\NavLinkController::toggle/$1');
    $routes->post('nav-links/reorder', 'Admin\NavLinkController::reorder');
    $routes->post('images/delete', 'Admin\ImageController::delete');

    // Database Audit
    $routes->get('database', 'Admin\DatabaseAuditController::index');
    $routes->post('database/import', 'Admin\DatabaseAuditController::importMissing');

    // Activity Logs & Settings
    $routes->get('activity-logs', 'Admin\SettingsController::activityLogs');
    $routes->get('settings', 'Admin\SettingsController::index');
    $routes->post('settings/password', 'Admin\SettingsController::updatePassword');
});

// Migration routes (backend only - no frontend modifications)
$routes->get('/migration', 'MigrationController::index');
$routes->get('/migration/execute', 'MigrationController::execute');
$routes->get('/migration/verify', 'MigrationController::verify');

// Mobile App JSON API
$routes->options('api/(.*)', static function () {
    return service('response')
        ->setHeader('Access-Control-Allow-Origin', '*')
        ->setHeader('Access-Control-Allow-Headers', 'Authorization, Content-Type, Accept, X-Api-Token, X-App-Locale')
        ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
        ->setHeader('Access-Control-Max-Age', '86400')
        ->setStatusCode(204);
});

$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    $routes->post('auth/register', 'AuthController::register');
    $routes->post('auth/login', 'AuthController::login');

    $routes->get('auth/me', 'AuthController::me', ['filter' => 'apiAuth']);
    $routes->put('auth/me', 'AuthController::updateMe', ['filter' => 'apiAuth']);
    $routes->post('auth/me', 'AuthController::updateMe', ['filter' => 'apiAuth']); // Android/proxy fallback

    $routes->get('my-businesses', 'MyBusinessController::index', ['filter' => 'apiAuth']);
    $routes->post('my-businesses', 'MyBusinessController::store', ['filter' => 'apiAuth']);
    $routes->get('my-businesses/(:num)', 'MyBusinessController::show/$1', ['filter' => 'apiAuth']);
    $routes->put('my-businesses/(:num)', 'MyBusinessController::update/$1', ['filter' => 'apiAuth']);
    $routes->post('my-businesses/(:num)', 'MyBusinessController::update/$1', ['filter' => 'apiAuth']);

    $routes->get('home', 'HomeController::index');
    $routes->get('categories', 'BusinessController::categories');
    $routes->get('businesses', 'BusinessController::index');
    $routes->get('businesses/(:segment)', 'BusinessController::show/$1');
    $routes->get('emergency', 'EmergencyController::index');
    $routes->get('wall', 'WallController::index');
    $routes->get('wall/(:segment)', 'WallController::show/$1');
});

// Route ALL unmatched URLs through Home::not_found so the 404 page is
// rendered AFTER global filters run and always follows the user's selected
// language (previously the exception handler rendered it with the default
// locale, ignoring the session/cookie language selection).
$routes->set404Override('\App\Controllers\Home::not_found');
