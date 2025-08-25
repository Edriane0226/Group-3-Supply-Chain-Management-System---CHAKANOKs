<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Login::index');
$routes->post('/login/auth', 'Login::auth');
$routes->get('/logout', 'Login::logout');

$routes->get('/admin/dashboard', function () {
    echo "<h1>Admin Dashboard</h1>";
});

$routes->get('/user/dashboard', function () {
    echo "<h1>User Dashboard</h1>";
});
