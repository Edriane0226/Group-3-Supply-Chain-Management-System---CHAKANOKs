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

//Para sa User Management CRUD ug Page
$routes->get('users', 'UserManagement::index');
$routes->get('create', 'UserManagement::create');
$routes->post('store', 'UserManagement::store');
$routes->get('edit/(:num)', 'UserManagement::edit/$1');
$routes->post('update/(:num)', 'UserManagement::update/$1');
$routes->get('delete/(:num)', 'UserManagement::delete/$1');

//Para sa Branch Management CRUD ug Page
$routes->get('branches', 'BranchManagement::index');
$routes->get('createBranch', 'BranchManagement::create');
$routes->post('storeBranch', 'BranchManagement::store');
$routes->get('editBranch/(:num)', 'BranchManagement::edit/$1');
$routes->post('updateBranch/(:num)', 'BranchManagement::update/$1');
$routes->get('deleteBranch/(:num)', 'BranchManagement::delete/$1');












