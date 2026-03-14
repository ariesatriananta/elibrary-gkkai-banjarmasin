<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->post('logout', 'AuthController::logout');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->post('account/password', 'AuthController::changePassword');
    $routes->get('books', 'BookController::index');
    $routes->get('books/export', 'BookController::export');
    $routes->get('books/create', 'BookController::create');
    $routes->post('books', 'BookController::store');
    $routes->get('books/(:num)/edit', 'BookController::edit/$1');
    $routes->post('books/(:num)', 'BookController::update/$1');
    $routes->post('books/(:num)/delete', 'BookController::destroy/$1');
    $routes->post('books/(:num)/copies', 'BookController::storeCopy/$1');
    $routes->post('books/(:num)/copies/(:num)', 'BookController::updateCopy/$1/$2');
    $routes->post('books/(:num)/copies/(:num)/delete', 'BookController::destroyCopy/$1/$2');
    $routes->get('members', 'MemberController::index');
    $routes->get('members/create', 'MemberController::create');
    $routes->post('members', 'MemberController::store');
    $routes->get('members/(:num)/edit', 'MemberController::edit/$1');
    $routes->post('members/(:num)', 'MemberController::update/$1');
    $routes->post('members/(:num)/delete', 'MemberController::destroy/$1');
    $routes->get('transactions', 'TransactionController::index');
    $routes->post('transactions/borrow', 'TransactionController::borrow');
    $routes->post('transactions/return', 'TransactionController::return');
    $routes->get('fines', 'FineController::index');
    $routes->post('fines/settings', 'FineController::updateSettings');
    $routes->post('fines/(:num)/pay', 'FineController::pay/$1');
    $routes->post('fines/(:num)/resolve', 'FineController::resolve/$1');
    $routes->post('fines/loan/(:num)/bonus-note', 'FineController::addBonusNote/$1');
});
