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
$routes->post('inventory/confirm-delivery/(:num)', 'Inventory::confirmDelivery/$1');
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

// Supplier Contract Routes (Central Office Admin only)
$routes->group('supplier-contracts', function($routes) {
    $routes->get('/', 'SupplierContract::index');
    $routes->get('create', 'SupplierContract::create');
    $routes->post('store', 'SupplierContract::store');
    $routes->get('view/(:num)', 'SupplierContract::view/$1');
    $routes->get('edit/(:num)', 'SupplierContract::edit/$1');
    $routes->post('update/(:num)', 'SupplierContract::update/$1');
    $routes->post('delete/(:num)', 'SupplierContract::delete/$1');
    $routes->post('activate/(:num)', 'SupplierContract::activate/$1');
    $routes->get('renew/(:num)', 'SupplierContract::renew/$1');
    $routes->post('renew/(:num)', 'SupplierContract::processRenewal/$1');
});
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
$routes->get('logistics-coordinator/delivery-schedules', 'LogisticsCoordinator::deliverySchedules');
$routes->get('logistics-coordinator/active-deliveries', 'LogisticsCoordinator::activeDeliveries');
$routes->get('logistics-coordinator/performance-reports', 'LogisticsCoordinator::performanceReports');
$routes->get('logistics-coordinator/schedule-details/(:num)', 'LogisticsCoordinator::getScheduleDetails/$1');
$routes->post('logistics-coordinator/update-schedule-status/(:num)', 'LogisticsCoordinator::updateScheduleStatus/$1');
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

// Franchise Management Routes
$routes->group('franchise', function($routes) {
    $routes->get('/', 'FranchiseManagement::index');
    $routes->get('dashboard', 'FranchiseManagement::index');
    $routes->get('applications', 'FranchiseManagement::applications');
    $routes->get('application/(:num)', 'FranchiseManagement::viewApplication/$1');
    $routes->get('create', 'FranchiseManagement::create');
    $routes->post('store', 'FranchiseManagement::store');
    $routes->post('approve/(:num)', 'FranchiseManagement::approve/$1');
    $routes->post('reject/(:num)', 'FranchiseManagement::reject/$1');
    $routes->post('review/(:num)', 'FranchiseManagement::markUnderReview/$1');
    $routes->get('list', 'FranchiseManagement::franchises');
    $routes->get('view/(:num)', 'FranchiseManagement::viewFranchise/$1');
    $routes->post('activate/(:num)', 'FranchiseManagement::activate/$1');
    $routes->post('suspend/(:num)', 'FranchiseManagement::suspend/$1');
    $routes->post('reactivate/(:num)', 'FranchiseManagement::reactivate/$1');
    $routes->post('terminate/(:num)', 'FranchiseManagement::terminate/$1');
    $routes->get('payments', 'FranchiseManagement::payments');
    $routes->get('payments/(:num)', 'FranchiseManagement::payments/$1');
    $routes->post('payment/(:num)', 'FranchiseManagement::recordPayment/$1');
    $routes->get('allocations', 'FranchiseManagement::allocations');
    $routes->get('allocations/(:num)', 'FranchiseManagement::allocations/$1');
    $routes->get('allocate/(:num)', 'FranchiseManagement::allocateSupply/$1');
    $routes->post('allocate/(:num)', 'FranchiseManagement::processAllocation/$1');
    $routes->post('allocation-status/(:num)', 'FranchiseManagement::updateAllocationStatus/$1');
    $routes->get('reports', 'FranchiseManagement::reports');
    $routes->get('search', 'FranchiseManagement::search');
});

// System Administration Routes
$routes->group('admin', function($routes) {
    $routes->get('/', 'SystemAdmin::index');
    $routes->get('dashboard', 'SystemAdmin::index');
    
    // User Management
    $routes->get('users', 'SystemAdmin::users');
    $routes->get('users/create', 'SystemAdmin::createUser');
    $routes->post('users/store', 'SystemAdmin::storeUser');
    $routes->get('users/edit/(:num)', 'SystemAdmin::editUser/$1');
    $routes->post('users/update/(:num)', 'SystemAdmin::updateUser/$1');
    $routes->post('users/delete/(:num)', 'SystemAdmin::deleteUser/$1');
    $routes->post('users/reset-password/(:num)', 'SystemAdmin::resetPassword/$1');
    
    // Role Management
    $routes->get('roles', 'SystemAdmin::roles');
    $routes->post('roles/create', 'SystemAdmin::createRole');
    $routes->post('roles/update/(:num)', 'SystemAdmin::updateRole/$1');
    $routes->post('roles/delete/(:num)', 'SystemAdmin::deleteRole/$1');
    
    // Branch Management
    $routes->get('branches', 'SystemAdmin::branches');
    
    // Activity Logs
    $routes->get('activity-logs', 'SystemAdmin::activityLogs');
    $routes->post('activity-logs/clear', 'SystemAdmin::clearLogs');
    
    // System Settings
    $routes->get('settings', 'SystemAdmin::settings');
    $routes->post('settings/update', 'SystemAdmin::updateSettings');
    
    // Backup & Maintenance
    $routes->get('backup', 'SystemAdmin::backup');
    $routes->post('backup/create', 'SystemAdmin::createBackup');
    $routes->get('backup/download/(:any)', 'SystemAdmin::downloadBackup/$1');
    $routes->post('backup/delete/(:any)', 'SystemAdmin::deleteBackup/$1');
    $routes->post('cache/clear', 'SystemAdmin::clearCache');
    
    // Contact Messages
    $routes->get('contact-messages', 'SystemAdmin::contactMessages');
    $routes->get('contact-messages/view/(:num)', 'SystemAdmin::viewContactMessage/$1');
    $routes->post('contact-messages/status/(:num)', 'SystemAdmin::updateMessageStatus/$1');
    $routes->post('contact-messages/delete/(:num)', 'SystemAdmin::deleteContactMessage/$1');
});

// Contact Form Routes
$routes->get('contact', 'Contact::index');
$routes->post('contact/send', 'Contact::send');

// Removed duplicate/invalid Branch routes for dashboard and inventory
