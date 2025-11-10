# Supplier & Delivery Module Implementation TODO

## 1. Supplier Management Enhancements
- [ ] Create migration to add performance metrics fields to suppliers table (on_time_delivery_rate, quality_rating, total_orders, total_deliveries)
- [ ] Update SupplierModel with methods for CRUD operations by Central Office Admin
- [ ] Add supplier performance calculation methods
- [ ] Update supplier views for admin management

## 2. Purchase Order Integration
- [ ] Update purchase_orders table migration if needed (add tracking fields)
- [ ] Create PurchaseOrderModel with methods for creating from approved requests
- [ ] Update PurchaseRequest controller to generate purchase orders on approval
- [ ] Add purchase order tracking statuses

## 3. Delivery Tracking Enhancements
- [ ] Update deliveries table migration to include new statuses (Approved, In Transit, Delivered)
- [ ] Add tracking fields (estimated_eta, actual_delivery_time, logistics_coordinator_id)
- [ ] Update InventoryModel delivery methods for new statuses
- [ ] Add real-time status updates

## 4. Logistics Coordinator Role
- [ ] Add Logistics Coordinator to roles table seeder
- [ ] Update role-based access in controllers
- [ ] Create LogisticsController for scheduling and route management

## 5. Delivery Scheduling & Route Optimization
- [ ] Create delivery_schedules table migration
- [ ] Add Google Maps API integration for routes
- [ ] Implement route optimization logic
- [ ] Add delivery calendar view

## 6. Notifications System
- [ ] Create notifications table migration
- [ ] Implement email/SMS API integration (placeholder for now)
- [ ] Add notification triggers for status changes

## 7. Branch & Central Office Interaction
- [ ] Update delivery views for branch managers (confirm receipt, report issues)
- [ ] Add central office monitoring dashboard
- [ ] Update deliveries page with new features

## 8. Audit Logs & Security
- [ ] Create audit_logs table migration
- [ ] Implement logging for all order/delivery activities
- [ ] Update role-based access controls

## 9. Testing & Validation
- [ ] Test all new functionalities
- [ ] Validate role-based permissions
- [ ] Ensure data integrity
