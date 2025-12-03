# 📋 ChakaNoks SCMS - Testing Guide

## 🚀 Paano i-access ang System

1. **I-start ang XAMPP** (Apache at MySQL)
2. **I-open ang browser** at i-access: `http://localhost/CHAKANOKS_SCMS/` o `http://localhost/CHAKANOKS_SCMS/login`
3. **Mag-login** gamit ang credentials sa ibaba

---

## 🔐 Default Login Credentials

### **Regular Users** (ID: 1-7)
Lahat ng regular users ay may **password: `password123`**

| Role | User ID | Email | Name |
|------|---------|-------|------|
| **Central Office Admin** | 1 | Ed@gmail.com | Edriane Bangonon |
| **Inventory Staff** | 2 | maria@example.com | Maria Santos |
| **Branch Manager** | 3 | pedro@example.com | Pedro Reyes |
| **Logistics Coordinator** | 4 | juan@example.com | Juan Dela Cruz |
| **Franchise Manager** | 5 | ana@example.com | Ana Lopez |
| **System Administrator** | 7 | admin@chakanoks.com | Admin System |

### **Suppliers** (ID: 1001-1008)
Lahat ng suppliers ay may **password: `password123`**

| Supplier ID | Supplier Name |
|-------------|---------------|
| 1001 | San Miguel Foods Inc. |
| 1002 | Bounty Fresh Chicken Supply |
| 1003 | NutriAsia Condiments Distributor |
| 1004 | Mega Packaging Solutions |
| 1005 | PureOil Philippines |
| 1006 | FastServe Kitchen Equipment Corp. |
| 1007 | CleanPro Janitorial Supplies |
| 1008 | FreshVeg Produce Supplier |

---

## 👥 Role Functions & Workflows

### 1️⃣ **System Administrator** (ID: 7)
**Access:** `/admin`

**Functions:**
- ✅ User Management (Create, Edit, Delete, Reset Password)
- ✅ Role Management (Create, Edit, Delete Roles)
- ✅ Branch Management (View all branches)
- ✅ Activity Logs (View system activities, Clear old logs)
- ✅ Contact Messages (View, Mark as Read/Replied/Archived, Delete)
- ✅ System Settings (Configure system parameters)
- ✅ Backup & Maintenance (Database backup, Clear cache)

**Testing Flow:**
1. Login as System Admin (ID: 7)
2. Check Dashboard - dapat may statistics
3. Go to **User Management** - try mag-create ng bagong user
4. Go to **Contact Messages** - dapat may notification badge kung may unread
5. Try mag-delete ng user - dapat may secure confirmation (type "DELETE")
6. Check **Activity Logs** - dapat may records ng actions mo

---

### 2️⃣ **Central Office Admin** (ID: 1)
**Access:** `/dashboard`

**Functions:**
- ✅ Central Dashboard (View all branches overview)
- ✅ User Management
- ✅ Branch Management
- ✅ Purchase Request Approval

**Testing Flow:**
1. Login as Central Office Admin (ID: 1)
2. View Dashboard - dapat may overview ng lahat ng branches
3. Check **Purchase Request** - dapat makita ang pending requests
4. Try mag-approve/reject ng purchase request

---

### 3️⃣ **Branch Manager** (ID: 3)
**Access:** `/dashboard`

**Functions:**
- ✅ Branch Dashboard (View branch statistics, upcoming deliveries)
- ✅ Inventory View (View branch inventory)
- ✅ Purchase Request (Create purchase requests)
- ✅ Deliveries (View delivery status)

**Testing Flow:**
1. Login as Branch Manager (ID: 3)
2. View Dashboard - dapat may branch-specific data
3. Go to **Purchase Request** - create ng bagong request
4. Check **Deliveries** - view delivery schedules
5. View **Inventory** - check branch inventory levels

---

### 4️⃣ **Inventory Staff** (ID: 2)
**Access:** `/inventory/overview`

**Functions:**
- ✅ Inventory Overview (View inventory summary)
- ✅ Stock In (Add items to inventory)
- ✅ Stock Out (Remove items from inventory)
- ✅ Deliveries (Confirm deliveries)
- ✅ Reports (Generate inventory reports)
- ✅ Scan (Barcode scanning)

**Testing Flow:**
1. Login as Inventory Staff (ID: 2)
2. View **Overview** - check inventory summary
3. Go to **Stock In** - add items to inventory
4. Go to **Stock Out** - remove items
5. Check **Deliveries** - confirm incoming deliveries
6. View **Reports** - generate inventory reports

---

### 5️⃣ **Logistics Coordinator** (ID: 4)
**Access:** `/logistics-coordinator`

**Functions:**
- ✅ Dashboard (View logistics overview)
- ✅ Delivery Schedules (Create and manage delivery schedules)
- ✅ Active Deliveries (Track ongoing deliveries)
- ✅ Performance Reports (View delivery performance)

**Testing Flow:**
1. Login as Logistics Coordinator (ID: 4)
2. View Dashboard - check logistics statistics
3. Go to **Delivery Schedules** - create new delivery schedule
4. Check **Active Deliveries** - track ongoing deliveries
5. View **Performance Reports** - check delivery metrics

---

### 6️⃣ **Franchise Manager** (ID: 5)
**Access:** `/franchise`

**Functions:**
- ✅ Dashboard (View franchise statistics)
- ✅ Applications (View and process franchise applications)
- ✅ Active Franchises (Manage active franchises)
- ✅ Payments (Track franchise payments)
- ✅ Supply Allocations (Allocate supplies to franchises)
- ✅ Reports (Generate franchise reports)

**Testing Flow:**
1. Login as Franchise Manager (ID: 5)
2. View Dashboard - check franchise statistics
3. Go to **Applications** - create new franchise application
4. Try mag-approve/reject ng application
5. Check **Active Franchises** - view list of franchises
6. Go to **Payments** - record franchise payment
7. Check **Supply Allocations** - allocate supplies to franchise

---

### 7️⃣ **Supplier** (ID: 1001-1008)
**Access:** `/supplier/dashboard`

**Functions:**
- ✅ Dashboard (View supplier overview)
- ✅ Purchase Orders (View and manage purchase orders)
- ✅ Delivery Management (Update delivery status)
- ✅ Invoices & Payments (Submit invoices)
- ✅ Notifications (View notifications)
- ✅ Profile & Settings (Update supplier profile)

**Testing Flow:**
1. Login as Supplier (ID: 1001)
2. View Dashboard - check supplier statistics
3. Go to **Purchase Orders** - view orders from branches
4. Check **Delivery Management** - update delivery status
5. Go to **Invoices & Payments** - submit invoice
6. Check **Notifications** - view system notifications

---

## 🔄 Common Workflows to Test

### **Workflow 1: Purchase Request Flow**
1. **Branch Manager** creates Purchase Request
2. **Central Office Admin** reviews and approves/rejects
3. **Supplier** receives Purchase Order
4. **Supplier** updates delivery status
5. **Logistics Coordinator** schedules delivery
6. **Inventory Staff** confirms delivery and updates stock

### **Workflow 2: Franchise Application Flow**
1. **Franchise Manager** creates new franchise application
2. **Franchise Manager** reviews application
3. **Franchise Manager** approves/rejects application
4. If approved, **Franchise Manager** allocates supplies
5. **Franchise Manager** tracks payments

### **Workflow 3: Contact Us Flow**
1. Public user submits message via **Contact Us** form
2. **System Administrator** receives notification
3. **System Administrator** views message
4. **System Administrator** marks as Read/Replied/Archived

### **Workflow 4: User Management Flow**
1. **System Administrator** creates new user
2. **System Administrator** assigns role and branch
3. New user can login
4. **System Administrator** can edit/delete user (with secure confirmation)

---

## 🧪 Step-by-Step Testing Checklist

### ✅ **Initial Setup**
- [ ] Run database migrations: `php spark migrate`
- [ ] Run seeders: `php spark db:seed`
- [ ] Verify all tables are created
- [ ] Verify default users are created

### ✅ **Authentication Testing**
- [ ] Test login with valid credentials
- [ ] Test login with invalid credentials
- [ ] Test logout functionality
- [ ] Test session timeout (if applicable)

### ✅ **Role-Based Access Testing**
- [ ] Login as each role
- [ ] Verify correct dashboard loads
- [ ] Verify sidebar shows correct menu items
- [ ] Try accessing unauthorized pages (should redirect)

### ✅ **CRUD Operations Testing**
- [ ] **Users:** Create, Read, Update, Delete
- [ ] **Branches:** Create, Read, Update, Delete
- [ ] **Purchase Requests:** Create, Approve, Reject
- [ ] **Franchises:** Create, Approve, Reject, Manage
- [ ] **Contact Messages:** View, Update Status, Delete

### ✅ **Security Testing**
- [ ] Test secure delete confirmation (type "DELETE")
- [ ] Test CSRF protection on forms
- [ ] Test unauthorized access attempts
- [ ] Test password hashing

### ✅ **UI/UX Testing**
- [ ] Check sidebar branding (correct role name)
- [ ] Check notification badges (unread messages)
- [ ] Test live search in User Management
- [ ] Test dropdown menus and modals
- [ ] Check responsive design (mobile/tablet)

---

## 🐛 Common Issues & Solutions

### **Issue: Cannot login**
- **Solution:** Check if database is running, verify user exists in database

### **Issue: "Unauthorized role" error**
- **Solution:** Check if role exists in `roles` table, verify user's `role_id` is correct

### **Issue: Missing columns error**
- **Solution:** Run migrations: `php spark migrate`

### **Issue: No data showing**
- **Solution:** Run seeders: `php spark db:seed`

### **Issue: Contact messages not showing**
- **Solution:** Submit a message via Contact Us form first, then check admin panel

---

## 📝 Notes

- **Default Password:** Lahat ng users ay may password: `password123`
- **Supplier IDs:** Suppliers use IDs 1001-1008 (not 1-8)
- **Session:** Users stay logged in until they logout or session expires
- **Activity Logs:** All actions are logged automatically (if implemented)
- **Notifications:** Contact messages show badge count on admin sidebar

---

## 🎯 Quick Test Scenarios

### **Scenario 1: New User Registration**
1. Login as System Admin
2. Go to User Management → Create User
3. Fill form and submit
4. Logout
5. Login with new user credentials

### **Scenario 2: Purchase Request**
1. Login as Branch Manager
2. Create Purchase Request
3. Logout
4. Login as Central Office Admin
5. Approve Purchase Request
6. Logout
7. Login as Supplier
8. View Purchase Order

### **Scenario 3: Franchise Application**
1. Login as Franchise Manager
2. Create Franchise Application
3. Approve Application
4. Allocate Supplies
5. Record Payment

### **Scenario 4: Contact Message**
1. Go to Contact Us page (public)
2. Submit message
3. Login as System Admin
4. Check Contact Messages (should have notification badge)
5. View message and mark as Read

---

## 📞 Support

Kung may problema, check:
1. Database connection
2. Migrations status
3. Seeders status
4. Browser console for errors
5. PHP error logs

---

**Happy Testing! 🚀**

