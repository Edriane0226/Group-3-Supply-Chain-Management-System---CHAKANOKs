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
//Central 
$routes->get('/central', 'Auth::centralDashboard');

// Inventory endpoints for inventory 
$routes->get('inventory', 'Auth::inventory');
$routes->get('/inventory/summary', 'Inventory::summary');
$routes->get('/inventory/find', 'Inventory::findByBarcode');
$routes->post('/inventory/update', 'Inventory::updateStock');
$routes->post('/inventory/receive', 'Inventory::receive');
$routes->post('/inventory/damage', 'Inventory::reportDamage');












