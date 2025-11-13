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

// Purchase Request Routes
$routes->get('purchase-request', 'PurchaseRequest::index');
$routes->get('purchase-requests', 'PurchaseRequest::index');
$routes->get('purchase-requests/create', 'PurchaseRequest::create');
$routes->post('purchase-requests', 'PurchaseRequest::store');
$routes->post('purchase-requests/approve/(:num)', 'PurchaseRequest::approve/$1');
$routes->post('purchase-requests/cancel/(:num)', 'PurchaseRequest::cancel/$1');
$routes->post('purchase-requests/reject/(:num)', 'PurchaseRequest::reject/$1');
$routes->get('orders', 'Orders::index'); // legacy link, can be removed later
$routes->get('deliveries', 'Deliveries::index');

$routes->get('deliveries/details/(:num)', 'Deliveries::details/$1');
$routes->post('deliveries/receive/(:num)', 'Deliveries::receive/$1');
$routes->post('deliveries/cancel/(:num)', 'Deliveries::cancel/$1');

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

// Logistics Coordinator routes
$routes->get('logistics-coordinator', 'LogisticsCoordinator::index');
$routes->post('logistics-coordinator/schedule-deliveries', 'LogisticsCoordinator::scheduleDeliveries');
$routes->post('logistics-coordinator/update-delivery-status/(:num)', 'LogisticsCoordinator::updateDeliveryStatus/$1');
$routes->get('logistics-coordinator/get-calendar-data', 'LogisticsCoordinator::getCalendarData');
$routes->get('logistics-coordinator/get-delivery-details/(:num)', 'LogisticsCoordinator::getDeliveryDetails/$1');
$routes->get('logistics-coordinator/get-notifications', 'LogisticsCoordinator::getNotifications');
$routes->post('logistics-coordinator/mark-notification-read/(:num)', 'LogisticsCoordinator::markNotificationRead/$1');

// Logistics Workflow routes
$routes->post('logistics-coordinator/review-po/(:num)', 'LogisticsCoordinator::reviewApprovedPO/$1');
$routes->post('logistics-coordinator/coordinate-supplier/(:num)', 'LogisticsCoordinator::coordinateWithSupplier/$1');
$routes->post('logistics-coordinator/create-delivery-schedule/(:num)', 'LogisticsCoordinator::createDeliverySchedule/$1');
$routes->post('logistics-coordinator/update-delivery-status/(:num)', 'LogisticsCoordinator::updateLogisticsDeliveryStatus/$1');
$routes->post('logistics-coordinator/coordinate-branch/(:num)', 'LogisticsCoordinator::coordinateWithBranch/$1');
$routes->post('logistics-coordinator/close-delivery/(:num)', 'LogisticsCoordinator::closeDeliveryRecord/$1');
$routes->get('logistics-coordinator/po-details/(:num)', 'LogisticsCoordinator::getPODetails/$1');

// Supplier routes
$routes->group('supplier', function($routes) {
    $routes->get('dashboard', 'Supplier::dashboard');
    $routes->get('orders', 'Supplier::orders');
    $routes->get('order-details/(:num)', 'Supplier::orderDetails/$1');
    $routes->post('update-order-status/(:num)', 'Supplier::updateOrderStatus/$1');
    $routes->get('deliveries', 'Supplier::deliveries');
    $routes->get('delivery-details/(:num)', 'Supplier::deliveryDetails/$1');
    $routes->get('invoices', 'Supplier::invoices');
    $routes->get('notifications', 'Supplier::notifications');
    $routes->get('profile', 'Supplier::profile');
    $routes->post('update-profile', 'Supplier::updateProfile');
    $routes->post('change-password', 'Supplier::changePassword');
});

// Contact Form Routes
$routes->get('contact', 'Contact::index');
$routes->post('contact/send', 'Contact::send');

// Removed duplicate/invalid Branch routes for dashboard and inventory
