<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('/directory', 'Home::directory');
$routes->get('/listings', 'Home::directory'); // Alias for backward compatibility
$routes->get('/wall-of-kot-sultan', 'Home::wall');
$routes->get('/volunteer', 'Home::volunteer');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');
$routes->get('/business', 'Home::business');
$routes->get('/login', 'Home::login');
$routes->get('/signup', 'Home::signup');
$routes->get('/dashboard', 'Home::dashboard');
$routes->get('/admin', 'Home::admin');
$routes->get('/lang/(:segment)', 'Home::lang/$1');
$routes->get('/404', 'Home::not_found');
