# Logistics Coordinator Workflow Implementation

## Tasks to Complete

### 1. Modify Purchase Request Approval
- [ ] Update `PurchaseRequestModel::approveRequest()` to notify Logistics Coordinators when a PO is created
- [ ] Add notification logic for logistics coordinators

### 2. Enhance Purchase Order Creation
- [ ] Update `PurchaseOrderModel::createFromPurchaseRequest()` to set appropriate status for logistics review
- [ ] Add logistics coordinator assignment

### 3. Add Workflow Methods to LogisticsCoordinator Controller
- [x] Implement `reviewApprovedPO()` method for Step 1
- [x] Implement `coordinateWithSupplier()` method for Step 2
- [x] Implement `createDeliverySchedule()` method for Step 3
- [x] Implement `updateDeliveryStatus()` method for Step 4 (enhance existing)
- [x] Implement `coordinateWithBranch()` method for Step 5
- [x] Implement `closeDeliveryRecord()` method for Step 6

### 4. Update Dashboard View
- [ ] Modify logistics dashboard to show workflow steps and actions
- [ ] Add workflow progress indicators
- [ ] Update action buttons for each step

### 5. Enhance Notifications
- [ ] Add specific notifications for each workflow step
- [ ] Update `NotificationModel::notifyStatusChange()` for logistics workflow

### 6. Testing and Verification
- [ ] Test complete workflow from approval to delivery completion
- [ ] Verify notifications are sent to appropriate users
- [ ] Ensure status updates trigger next workflow steps

### 7. Database Schema Updates
- [x] Update delivery_schedules table to use po_id instead of delivery_id
- [x] Add new columns: po_id, coordinator_id, driver_id, vehicle_id
- [x] Update foreign keys accordingly
- [x] Run migration to apply schema changes

### 8. Model Updates
- [x] Update DeliveryScheduleModel to work with PO-based schedules
- [x] Update joins in getCalendarData() and other methods
- [x] Update optimizeRoutes() to work with PO IDs
- [x] Update performance metrics calculation

### 9. Controller Updates
- [x] Update LogisticsCoordinator controller methods to use po_ids instead of delivery_ids
- [x] Update scheduleDeliveries() method
- [x] Update getCoordinatorPerformanceMetrics() to use PO actual_delivery_date
