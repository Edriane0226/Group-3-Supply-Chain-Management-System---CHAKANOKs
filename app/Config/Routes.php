<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');
$routes->get('/logout', 'Auth::logout');

$routes->get('dashboard', 'Dashboard::index');

//Central
$routes->get('/central', 'Auth::centralDashboard');

// Inventory endpoints for inventory
// Route landing inventory to Auth::inventory for role-based routing (BM -> InventoryBranch view)
$routes->get('inventory', 'Auth::inventory');
$routes->get('inventory/overview', 'Inventory::overviewPage');

$routes->get('inventory/stockin', 'Inventory::stockInPage');
$routes->post('inventory/stockin', 'Inventory::stockin');
$routes->get('inventory/stockout', 'Inventory::stockOutPage');
$routes->post('inventory/stockout', 'Inventory::stockout');
$routes->get('inventory/reports', 'Inventory::reportsPage');
$routes->get('inventory/scan', 'Inventory::scanPage');
$routes->get('/inventory/live', 'Inventory::liveInventory');
$routes->get('/inventory/summary', 'Inventory::summary');
$routes->get('/inventory/find', 'Inventory::findByBarcode');
$routes->get('/inventory/balance', 'Inventory::balance');
$routes->get('/inventory/export', 'Inventory::export');

// Branch Manager simple pages
$routes->get('purchase-request', 'PurchaseRequest::index');
$routes->get('orders', 'Orders::index'); // legacy link, can be removed later
$routes->get('deliveries', 'Deliveries::index');

//Para sa User Management CRUD ug Page
$routes->get('users', 'UserManagement::index');
$routes->get('create', 'UserManagement::create');
$routes->post('store', 'UserManagement::store');
$routes->get('edit/(:num)', 'UserManagement::edit/$1');
$routes->post('update/(:num)', 'UserManagement::update/$1');
$routes->get('delete/(:num)', 'UserManagement::delete/$1');

//Para sa Branch Management CRUD ug Page
$routes->get('branches', 'BranchManagement::index');
$routes->get('branches/create', 'BranchManagement::create');
$routes->post('branches/store', 'BranchManagement::store');
$routes->get('branches/edit/(:num)', 'BranchManagement::edit/$1');
$routes->post('branches/update/(:num)', 'BranchManagement::update/$1');
$routes->get('branches/delete/(:num)', 'BranchManagement::delete/$1');

// Removed duplicate/invalid Branch routes for dashboard and inventory
