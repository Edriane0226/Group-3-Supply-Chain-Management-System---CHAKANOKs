<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');

// Inventory endpoints for staff dashboard
$routes->get('/inventory/summary', 'Inventory::summary');
$routes->get('/inventory/find', 'Inventory::findByBarcode');
$routes->post('/inventory/update', 'Inventory::updateStock');
$routes->post('/inventory/receive', 'Inventory::receive');
$routes->post('/inventory/damage', 'Inventory::reportDamage');

$routes->get('dashboard', 'Auth::dashboard');








