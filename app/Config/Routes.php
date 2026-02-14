<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);

$routes->get('/', 'AuthController::login', ['filter' => 'guest']);
$routes->match(['get', 'post'], 'login', 'AuthController::login', ['filter' => 'guest']);
$routes->get('logout', 'AuthController::logout', ['filter' => 'auth']);
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);

$routes->group('patients', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'PatientController::index');
    $routes->get('new', 'PatientController::new');
    $routes->post('create', 'PatientController::create');
    $routes->get('edit/(:num)', 'PatientController::edit/$1');
    $routes->post('update/(:num)', 'PatientController::update/$1');
    $routes->post('delete/(:num)', 'PatientController::delete/$1', ['filter' => 'role:admin']);
    $routes->get('show/(:num)', 'PatientController::show/$1');
    $routes->get('search', 'PatientController::search');
    $routes->get('card', 'PatientController::cardForm');
    $routes->post('card/import', 'PatientController::importFromCard');
});

$routes->group('visits', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'VisitController::index');
    $routes->get('new/(:num)', 'VisitController::new/$1');
    $routes->post('create/(:num)', 'VisitController::create/$1');
    $routes->get('edit/(:num)', 'VisitController::edit/$1', ['filter' => 'role:doctor,admin']);
    $routes->post('update/(:num)', 'VisitController::update/$1', ['filter' => 'role:doctor,admin']);
    $routes->get('timeline/(:num)', 'VisitController::timeline/$1');
});

$routes->group('reports', ['filter' => 'auth'], static function ($routes) {
    $routes->get('daily', 'ReportController::daily');
    $routes->get('excel', 'ReportController::exportExcel');
    $routes->get('pdf', 'ReportController::exportPdf');
});

$routes->group('users', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'UserController::index', ['filter' => 'role:admin']);
    $routes->get('new', 'UserController::new', ['filter' => 'role:admin']);
    $routes->post('create', 'UserController::create', ['filter' => 'role:admin']);
    $routes->get('edit/(:num)', 'UserController::edit/$1', ['filter' => 'role:admin']);
    $routes->post('update/(:num)', 'UserController::update/$1', ['filter' => 'role:admin']);
    $routes->post('delete/(:num)', 'UserController::delete/$1', ['filter' => 'role:admin']);
});

$routes->group('api/card', static function ($routes) {
    $routes->post('read', 'Api\\CardController::read');
});

$routes->get('api/icd10/suggest', 'Api\\IcdController::suggest');
