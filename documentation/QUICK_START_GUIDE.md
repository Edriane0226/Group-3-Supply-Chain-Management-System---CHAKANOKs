# 🚀 ChakaNoks SCMS - Quick Start Guide

## 📖 **SIMPLE FLOW EXPLANATION**

### **🎯 MAIN IDEA:**
Ang system ninyo ay para sa **Supply Chain Management** - from ordering supplies hanggang sa delivery at inventory tracking.

---

## 🔄 **THE BIG PICTURE:**

```
1. BRANCH NEEDS SUPPLIES
   ↓
2. BRANCH MANAGER → Creates Purchase Request
   ↓
3. CENTRAL OFFICE ADMIN → Approves Request
   ↓
4. LOGISTICS COORDINATOR → Schedules Delivery
   ↓
5. SUPPLIER → Delivers Items
   ↓
6. INVENTORY STAFF → Receives & Updates Stock
   ↓
7. EVERYONE → Can View Reports & Status
```

---

## 📝 **STEP-BY-STEP: Simple Example**

### **Example: Branch Needs Chicken**

#### **STEP 1: Branch Manager Creates Request**
```
Login: ID 3, Password: password123
→ Click "Purchase Request"
→ Click "Create Request"
→ Select Supplier: "Bounty Fresh Chicken"
→ Add Items: "Whole Chicken - 50kg"
→ Click "Submit"
✅ DONE! Request sent to Central Office
```

#### **STEP 2: Central Office Admin Approves**
```
Login: ID 1, Password: password123
→ Click "Purchase Request"
→ See "Pending" request
→ Click "View" to see details
→ Click "Approve"
✅ DONE! Purchase Order created
```

#### **STEP 3: Logistics Schedules Delivery**
```
Login: ID 4, Password: password123
→ Go to Dashboard
→ See "Pending Purchase Order"
→ Click "Schedule Delivery"
→ Set Date: Tomorrow, 9:00 AM
→ Click "Schedule"
✅ DONE! Delivery scheduled
```

#### **STEP 4: Supplier Updates Status**
```
Login: ID 1002, Password: password123
→ Go to "Purchase Orders"
→ See order
→ Click "Update Status"
→ Select: "In Transit"
→ Click "Update"
✅ DONE! Status updated
```

#### **STEP 5: Inventory Staff Receives**
```
Login: ID 2, Password: password123
→ Go to "Deliveries"
→ See "Pending Delivery"
→ Click "Confirm Delivery"
→ Verify items
→ Click "Confirm"
✅ DONE! Items added to inventory
```

---

## 🎯 **ROLE-BY-ROLE: What Each Person Does**

### **👤 Branch Manager (ID: 3)**
**Main Job:** Manage branch operations

**Daily Tasks:**
1. Check **Dashboard** → See branch status
2. Check **Inventory** → See stock levels
3. Create **Purchase Request** → Order supplies
4. View **Deliveries** → See incoming deliveries

**Where to Go:**
- Dashboard: `/dashboard`
- Purchase Request: `/purchase-request`
- Inventory: `/inventory`
- Deliveries: `/deliveries`

---

### **👤 Inventory Staff (ID: 2)**
**Main Job:** Manage inventory & receive deliveries

**Daily Tasks:**
1. Check **Overview** → See inventory summary
2. **Stock In** → Add items to inventory
3. **Stock Out** → Remove items (damaged/expired)
4. **Confirm Deliveries** → Receive deliveries
5. **Reports** → Generate reports

**Where to Go:**
- Overview: `/inventory/overview`
- Stock In: `/inventory/stockin`
- Stock Out: `/inventory/stockout`
- Deliveries: `/deliveries`
- Reports: `/inventory/reports`

---

### **👤 Central Office Admin (ID: 1)**
**Main Job:** Oversee all branches & approve requests

**Daily Tasks:**
1. Check **Dashboard** → See all branches
2. **Approve Purchase Requests** → Review & approve
3. **Manage Supplier Contracts** → Create/edit contracts
4. **User Management** → Manage users
5. **View Reports** → Performance reports

**Where to Go:**
- Dashboard: `/dashboard`
- Purchase Request: `/purchase-request`
- Supplier Contracts: `/supplier-contracts`
- User Management: `/users`
- Branches: `/branches`

---

### **👤 Supplier (ID: 1001-1008)**
**Main Job:** Receive orders & deliver supplies

**Daily Tasks:**
1. Check **Dashboard** → See orders
2. View **Purchase Orders** → See orders from branches
3. **Update Delivery Status** → Update when delivering
4. **Submit Invoices** → Submit invoices
5. **View Notifications** → Check notifications

**Where to Go:**
- Dashboard: `/supplier/dashboard`
- Orders: `/supplier/orders`
- Deliveries: `/supplier/deliveries`
- Invoices: `/supplier/invoices`

---

### **👤 Logistics Coordinator (ID: 4)**
**Main Job:** Schedule & track deliveries

**Daily Tasks:**
1. Check **Dashboard** → See pending orders
2. **Schedule Deliveries** → Set delivery dates/times
3. **Track Active Deliveries** → Monitor ongoing deliveries
4. **View Reports** → Performance reports

**Where to Go:**
- Dashboard: `/logistics-coordinator`
- Delivery Schedules: `/logistics-coordinator/delivery-schedules`
- Active Deliveries: `/logistics-coordinator/active-deliveries`
- Reports: `/logistics-coordinator/performance-reports`

---

### **👤 Franchise Manager (ID: 5)**
**Main Job:** Manage franchises

**Daily Tasks:**
1. Check **Dashboard** → See franchise stats
2. **Review Applications** → Approve/reject applications
3. **Allocate Supplies** → Allocate supplies to franchises
4. **Record Payments** → Track franchise payments
5. **View Reports** → Franchise reports

**Where to Go:**
- Dashboard: `/franchise`
- Applications: `/franchise/applications`
- Active Franchises: `/franchise/list`
- Allocations: `/franchise/allocations`
- Payments: `/franchise/payments`

---

### **👤 System Administrator (ID: 7)**
**Main Job:** Maintain system & manage users

**Daily Tasks:**
1. Check **Dashboard** → See system stats
2. **Manage Users** → Create/edit/delete users
3. **Manage Roles** → Create/edit roles
4. **View Activity Logs** → Monitor system activities
5. **Manage Contact Messages** → Handle contact form messages
6. **Create Backups** → Backup database

**Where to Go:**
- Dashboard: `/admin`
- Users: `/admin/users`
- Roles: `/admin/roles`
- Activity Logs: `/admin/activity-logs`
- Contact Messages: `/admin/contact-messages`
- Backup: `/admin/backup`

---

## 🎬 **PRACTICE SCENARIOS**

### **Scenario 1: New Order Flow**
```
1. Branch Manager creates Purchase Request
2. Central Office Admin approves
3. Logistics Coordinator schedules delivery
4. Supplier updates status to "In Transit"
5. Inventory Staff confirms delivery
6. Stock automatically updated
```

### **Scenario 2: New Franchise**
```
1. Franchise Manager creates application
2. Franchise Manager reviews & approves
3. Franchise Manager activates franchise
4. Franchise Manager allocates supplies
5. Franchise Manager records payments
```

### **Scenario 3: New Supplier Contract**
```
1. Central Office Admin creates contract
2. Central Office Admin activates contract
3. Contract is now active
4. Monitor for expiration
5. Renew contract when needed
```

---

## 🔑 **KEY CONCEPTS TO REMEMBER**

### **1. Status Flow**
```
Pending → Approved → Scheduled → In Transit → Delivered → Received
```

### **2. Who Can Do What**
- **Branch Manager** → Creates requests, views inventory
- **Central Office Admin** → Approves requests, manages contracts
- **Logistics** → Schedules deliveries
- **Supplier** → Updates delivery status
- **Inventory Staff** → Receives deliveries, updates stock

### **3. Data Flow**
```
Request → Approval → Order → Schedule → Delivery → Inventory
```

---

## 📱 **QUICK NAVIGATION TIPS**

1. **Always check sidebar** - Lahat ng links nandun
2. **Use search/filter** - Para mabilis makita ang data
3. **Check notifications** - May badge kung may updates
4. **View details first** - Bago mag-action, view muna details
5. **Confirm carefully** - Always verify bago mag-confirm

---

## ✅ **CHECKLIST: Am I Doing It Right?**

### **Before Creating Request:**
- [ ] Naka-login ako as Branch Manager?
- [ ] Naka-check ko ang current inventory?
- [ ] Alam ko kung anong items ang kailangan?
- [ ] Naka-select ko ang correct supplier?

### **Before Approving Request:**
- [ ] Naka-login ako as Central Office Admin?
- [ ] Na-review ko ang request details?
- [ ] Na-check ko kung reasonable ang quantities?
- [ ] Na-verify ko ang supplier?

### **Before Confirming Delivery:**
- [ ] Naka-login ako as Inventory Staff?
- [ ] Na-verify ko ang items received?
- [ ] Na-check ko kung tama ang quantities?
- [ ] Na-check ko kung may damaged items?

---

## 🎓 **LEARNING PATH**

### **Week 1: Basic Operations**
- Learn how to login
- Learn how to navigate
- Learn how to view data

### **Week 2: Create & Approve**
- Learn how to create Purchase Request
- Learn how to approve requests
- Learn how to view status

### **Week 3: Delivery & Inventory**
- Learn how to schedule deliveries
- Learn how to update delivery status
- Learn how to receive deliveries
- Learn how to update inventory

### **Week 4: Advanced Features**
- Learn contract management
- Learn franchise management
- Learn reporting
- Learn system administration

---

**Start with simple tasks, then gradually learn complex ones! 🚀**

