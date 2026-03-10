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
    $routes->get('books', 'BookController::index');
    $routes->get('members', 'MemberController::index');
    $routes->get('transactions', 'TransactionController::index');
    $routes->get('fines', 'FineController::index');
});
