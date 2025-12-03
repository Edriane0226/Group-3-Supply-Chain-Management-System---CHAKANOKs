# 📚 ChakaNoks SCMS - Complete System Flow Guide

## 🎯 **PANOORIN: Overall System Flow**

```
┌─────────────────────────────────────────────────────────────┐
│                    CHAKANOKS SCMS FLOW                       │
└─────────────────────────────────────────────────────────────┘

1. BRANCH NEEDS SUPPLIES
   ↓
2. BRANCH MANAGER creates Purchase Request
   ↓
3. CENTRAL OFFICE ADMIN approves/rejects Purchase Request
   ↓
4. If APPROVED → Creates Purchase Order
   ↓
5. SUPPLIER receives Purchase Order
   ↓
6. LOGISTICS COORDINATOR schedules delivery
   ↓
7. SUPPLIER updates delivery status
   ↓
8. INVENTORY STAFF confirms delivery & updates stock
   ↓
9. System tracks everything (inventory, payments, reports)
```

---

## 🔄 **MAIN WORKFLOWS (Step-by-Step)**

### **WORKFLOW 1: Purchase Request to Delivery** 📦

#### **Step 1: Branch Manager Creates Purchase Request**
**Who:** Branch Manager  
**Where:** `/purchase-request` or `/purchase-requests/create`

**What to do:**
1. Login as Branch Manager (ID: 3, password: password123)
2. Go to **Purchase Request** sa sidebar
3. Click **"Create Purchase Request"** or **"New Request"**
4. Fill up:
   - Select **Supplier** (e.g., San Miguel Foods)
   - Select **Items** na kailangan
   - Enter **Quantities** for each item
   - Add **Notes** (optional)
5. Click **"Submit"** or **"Create Request"**

**What happens:**
- Purchase Request status = **"Pending"**
- Central Office Admin makikita ang request

---

#### **Step 2: Central Office Admin Reviews & Approves**
**Who:** Central Office Admin  
**Where:** `/purchase-request`

**What to do:**
1. Login as Central Office Admin (ID: 1, password: password123)
2. Go to **Purchase Request** sa sidebar
3. Makikita mo ang list ng **Pending Requests**
4. Click **"View"** or **"Details"** para makita ang details
5. Review:
   - Items requested
   - Quantities
   - Supplier
   - Total amount
6. Choose action:
   - **Approve** → Creates Purchase Order
   - **Reject** → Request cancelled (may reason)
   - **Request Changes** → Send back to Branch Manager

**What happens:**
- If **Approved**: 
  - Purchase Order automatically created
  - Status = **"Approved"**
  - Supplier makikita ang order
- If **Rejected**:
  - Status = **"Rejected"**
  - Branch Manager makikita ang rejection

---

#### **Step 3: Logistics Coordinator Schedules Delivery**
**Who:** Logistics Coordinator  
**Where:** `/logistics-coordinator`

**What to do:**
1. Login as Logistics Coordinator (ID: 4, password: password123)
2. Go to **Dashboard** → Makikita ang **Pending Purchase Orders**
3. Click **"Schedule Delivery"** or go to **Delivery Schedules**
4. Select Purchase Order
5. Set:
   - **Delivery Date**
   - **Delivery Time**
   - **Route Sequence** (kung maraming deliveries)
6. Click **"Schedule"**

**What happens:**
- Delivery Schedule created
- Status = **"Scheduled"**
- Branch at Supplier notified

---

#### **Step 4: Supplier Updates Delivery Status**
**Who:** Supplier  
**Where:** `/supplier/deliveries`

**What to do:**
1. Login as Supplier (ID: 1001, password: password123)
2. Go to **Purchase Orders** → Makikita ang orders
3. Click **"View Order"** or **"Order Details"**
4. Update status:
   - **Confirmed** → Order confirmed
   - **Preparing** → Preparing items
   - **In Transit** → On the way
   - **Delivered** → Delivered to branch
5. Click **"Update Status"**

**What happens:**
- Status updated sa system
- Branch Manager at Logistics Coordinator notified

---

#### **Step 5: Inventory Staff Confirms Delivery**
**Who:** Inventory Staff  
**Where:** `/deliveries` or `/inventory/overview`

**What to do:**
1. Login as Inventory Staff (ID: 2, password: password123)
2. Go to **Deliveries** sa sidebar
3. Makikita ang **Pending Deliveries**
4. Click **"Confirm Delivery"** or **"Receive"**
5. Verify items:
   - Check kung tama ang items
   - Check kung tama ang quantities
   - Check kung may damaged items
6. Click **"Confirm"** or **"Receive Delivery"**

**What happens:**
- Delivery status = **"Received"** or **"Completed"**
- Items automatically added sa inventory (Stock In)
- Inventory levels updated
- Branch Manager notified

---

#### **Step 6: Inventory Staff Updates Stock (if needed)**
**Who:** Inventory Staff  
**Where:** `/inventory/stockin` or `/inventory/stockout`

**What to do:**
1. Go to **Stock In** (kung may additional items)
   - Select item
   - Enter quantity
   - Set expiry date (kung may expiry)
   - Click **"Add Stock"**

2. Go to **Stock Out** (kung may damaged/expired items)
   - Select item
   - Enter quantity
   - Select reason (damaged, expired, used)
   - Click **"Remove Stock"**

**What happens:**
- Inventory levels updated
- Reports updated
- Low stock alerts triggered (kung below threshold)

---

### **WORKFLOW 2: Franchise Application to Supply Allocation** 🏪

#### **Step 1: Franchise Manager Creates Application**
**Who:** Franchise Manager  
**Where:** `/franchise/applications` or `/franchise/create`

**What to do:**
1. Login as Franchise Manager (ID: 5, password: password123)
2. Go to **Applications** sa sidebar
3. Click **"New Application"** or **"Create Application"**
4. Fill up:
   - Applicant name
   - Contact information
   - Proposed location
   - Business experience
   - Investment capacity
   - Other required fields
5. Click **"Submit Application"**

**What happens:**
- Application status = **"Pending"** or **"Under Review"**
- Franchise Manager makikita ang application

---

#### **Step 2: Franchise Manager Reviews Application**
**Who:** Franchise Manager  
**Where:** `/franchise/applications` or `/franchise/application/{id}`

**What to do:**
1. Go to **Applications** → Makikita ang list
2. Click **"View"** para makita ang full details
3. Review application:
   - Check applicant info
   - Check proposed location
   - Check business experience
   - Check investment capacity
4. Choose action:
   - **Approve** → Application approved
   - **Reject** → Application rejected (may reason)
   - **Mark Under Review** → Need more info

**What happens:**
- If **Approved**: Status = **"Approved"**, ready for activation
- If **Rejected**: Status = **"Rejected"**, applicant notified
- If **Under Review**: Status = **"Under Review"**, pending

---

#### **Step 3: Franchise Manager Activates Franchise**
**Who:** Franchise Manager  
**Where:** `/franchise/list` or `/franchise/view/{id}`

**What to do:**
1. Go to **Active Franchises** or **Applications**
2. Find approved application
3. Click **"View"** or **"Activate"**
4. Select **Branch** (kung may branch assignment)
5. Click **"Activate Franchise"**

**What happens:**
- Franchise status = **"Active"**
- Franchise linked to branch
- Ready for supply allocation

---

#### **Step 4: Franchise Manager Allocates Supplies**
**Who:** Franchise Manager  
**Where:** `/franchise/allocations` or `/franchise/allocate/{id}`

**What to do:**
1. Go to **Supply Allocations** sa sidebar
2. Click **"Allocate Supplies"** or select franchise
3. Fill up:
   - Select **Franchise**
   - Enter **Item Name**
   - Enter **Quantity**
   - Set **Unit** (kg, pcs, etc.)
   - Set **Unit Price**
   - Set **Delivery Date**
4. Click **"Allocate"** or **"Submit"**

**What happens:**
- Supply allocation created
- Status = **"Pending"** or **"Scheduled"**
- Franchise notified
- Delivery scheduled

---

#### **Step 5: Franchise Manager Records Payment**
**Who:** Franchise Manager  
**Where:** `/franchise/payments` or `/franchise/payments/{id}`

**What to do:**
1. Go to **Payments** sa sidebar
2. Select **Franchise**
3. Click **"Record Payment"**
4. Fill up:
   - Payment Type (Franchise Fee, Royalty, etc.)
   - Amount
   - Payment Date
   - Payment Method
   - Reference Number
5. Click **"Record Payment"**

**What happens:**
- Payment recorded
- Payment history updated
- Reports updated

---

### **WORKFLOW 3: Supplier Contract Management** 📄

#### **Step 1: Central Office Admin Creates Contract**
**Who:** Central Office Admin  
**Where:** `/supplier-contracts/create`

**What to do:**
1. Login as Central Office Admin (ID: 1, password: password123)
2. Go to **Supplier Contracts** sa sidebar
3. Click **"New Contract"**
4. Fill up:
   - Select **Supplier**
   - Contract Type (Supply Agreement, Service Contract, etc.)
   - Start Date & End Date
   - Payment Terms (Net 30, Net 15, COD)
   - Minimum Order Value
   - Discount Rate
   - Delivery Terms
   - Quality Standards
   - Penalty Clauses
   - Notes
5. Click **"Create Contract"**

**What happens:**
- Contract created with auto-generated number (CNT-YYYYMM-####)
- Status = **"Draft"**
- Ready for activation

---

#### **Step 2: Central Office Admin Activates Contract**
**Who:** Central Office Admin  
**Where:** `/supplier-contracts/view/{id}`

**What to do:**
1. Go to **Supplier Contracts** → Find contract
2. Click **"View"** para makita ang details
3. Review contract details
4. Click **"Activate Contract"**

**What happens:**
- Contract status = **"Active"**
- Signed by admin = **Yes**
- Signed date = Today
- Contract is now active

---

#### **Step 3: Monitor Expiring Contracts**
**Who:** Central Office Admin  
**Where:** `/supplier-contracts`

**What to do:**
1. Go to **Supplier Contracts**
2. Check **Statistics Cards**:
   - **Expiring Soon** → Contracts expiring within 30 days
   - **Expired** → Contracts na expired na
3. Click on contract to view details
4. If expiring soon → Prepare renewal

**What happens:**
- System automatically tracks expiring contracts
- Alerts shown sa dashboard

---

#### **Step 4: Renew Contract (if needed)**
**Who:** Central Office Admin  
**Where:** `/supplier-contracts/renew/{id}`

**What to do:**
1. Go to contract details
2. Click **"Renew Contract"**
3. Fill up new dates:
   - New Start Date
   - New End Date
   - Update terms (if needed)
4. Click **"Renew Contract"**

**What happens:**
- Old contract status = **"Renewed"**
- New contract created with new number
- New contract status = **"Draft"** (need to activate)

---

### **WORKFLOW 4: System Administration** ⚙️

#### **Step 1: System Admin Manages Users**
**Who:** System Administrator  
**Where:** `/admin/users`

**What to do:**
1. Login as System Admin (ID: 7, password: password123)
2. Go to **User Management** sa sidebar
3. **Create User:**
   - Click **"Create User"**
   - Fill up: Name, Email, Role, Branch, Password
   - Click **"Create"**
4. **Edit User:**
   - Click **"Edit"** sa user
   - Update info
   - Click **"Update"**
5. **Delete User:**
   - Click **"Delete"**
   - Type **"DELETE"** to confirm
   - Click **"Delete User"**

**What happens:**
- User created/updated/deleted
- Activity logged
- User can login (if created)

---

#### **Step 2: System Admin Manages Roles**
**Who:** System Administrator  
**Where:** `/admin/roles`

**What to do:**
1. Go to **Role Management** sa sidebar
2. **Create Role:**
   - Click **"Create Role"**
   - Enter role name
   - Click **"Create"**
3. **Edit Role:**
   - Click **"Edit"** sa role
   - Update name
   - Click **"Update"**
4. **Delete Role:**
   - Click **"Delete"**
   - Confirm deletion

**What happens:**
- Role created/updated/deleted
- Users with that role affected

---

#### **Step 3: System Admin Views Activity Logs**
**Who:** System Administrator  
**Where:** `/admin/activity-logs`

**What to do:**
1. Go to **Activity Logs** sa sidebar
2. View logs:
   - All system activities
   - User actions
   - System changes
3. Filter by:
   - Date range
   - User
   - Action type
4. **Clear Old Logs:**
   - Click **"Clear Old Logs"**
   - Enter days to keep (e.g., 90 days)
   - Click **"Clear"**

**What happens:**
- Logs displayed
- Old logs deleted (if cleared)
- System performance improved

---

#### **Step 4: System Admin Manages Contact Messages**
**Who:** System Administrator  
**Where:** `/admin/contact-messages`

**What to do:**
1. Go to **Contact Messages** sa sidebar
2. Makikita ang list ng messages (may badge kung may unread)
3. **View Message:**
   - Click **"View"** para makita ang full message
4. **Mark as Read:**
   - Click **"Mark as Read"** icon
5. **Mark as Replied:**
   - Click **"Mark as Replied"** icon
6. **Archive:**
   - Click **"Archive"** icon
7. **Delete:**
   - Click **"Delete"** icon
   - Confirm deletion

**What happens:**
- Message status updated
- Badge count updated
- Message organized

---

#### **Step 5: System Admin Creates Backup**
**Who:** System Administrator  
**Where:** `/admin/backup`

**What to do:**
1. Go to **Backup & Maintenance** sa sidebar
2. Click **"Create Backup"**
3. Wait for backup to complete
4. **Download Backup:**
   - Click **"Download"** sa backup file
5. **Delete Backup:**
   - Click **"Delete"** sa backup file
   - Confirm deletion

**What happens:**
- Database backup created
- Backup file saved
- Can restore from backup if needed

---

## 🔄 **COMPLETE END-TO-END FLOW EXAMPLE**

### **Scenario: Branch Needs Chicken Supplies**

```
1. BRANCH MANAGER (ID: 3)
   → Login
   → Go to Purchase Request
   → Create Request:
     - Supplier: Bounty Fresh Chicken Supply
     - Items: Whole Chicken (50kg), Chicken Wings (30kg)
     - Submit
   
2. CENTRAL OFFICE ADMIN (ID: 1)
   → Login
   → Go to Purchase Request
   → See Pending Request
   → Review details
   → Approve Request
   
3. LOGISTICS COORDINATOR (ID: 4)
   → Login
   → Go to Dashboard
   → See Approved Purchase Order
   → Schedule Delivery:
     - Date: Tomorrow
     - Time: 9:00 AM
     - Schedule
   
4. SUPPLIER (ID: 1002 - Bounty Fresh)
   → Login
   → Go to Purchase Orders
   → See Order
   → Update Status: "Preparing"
   → Later: Update Status: "In Transit"
   → Later: Update Status: "Delivered"
   
5. INVENTORY STAFF (ID: 2)
   → Login
   → Go to Deliveries
   → See Pending Delivery
   → Confirm Delivery
   → Verify items received
   → Confirm
   
6. INVENTORY STAFF
   → Go to Stock In (if needed)
   → Add items to inventory
   → Inventory updated
   
7. BRANCH MANAGER
   → Go to Dashboard
   → See updated inventory
   → See delivery completed
```

---

## 📋 **QUICK REFERENCE: What Each Role Does**

### **Branch Manager**
- ✅ Monitor branch inventory
- ✅ Create purchase requests
- ✅ View delivery schedules
- ✅ Approve intra-branch transfers

### **Inventory Staff**
- ✅ Update stock (Stock In/Out)
- ✅ Receive deliveries
- ✅ Report damaged/expired goods
- ✅ Generate inventory reports
- ✅ Scan barcodes

### **Central Office Admin**
- ✅ Oversee all branches
- ✅ Approve/reject purchase requests
- ✅ Manage supplier contracts
- ✅ View performance reports
- ✅ Manage users & branches

### **Supplier**
- ✅ View purchase orders
- ✅ Update delivery status
- ✅ Submit invoices
- ✅ View notifications

### **Logistics Coordinator**
- ✅ Schedule deliveries
- ✅ Track active deliveries
- ✅ Optimize routes
- ✅ View performance reports

### **Franchise Manager**
- ✅ Handle franchise applications
- ✅ Approve/reject applications
- ✅ Allocate supplies to franchises
- ✅ Record franchise payments
- ✅ View franchise reports

### **System Administrator**
- ✅ Manage users (create, edit, delete)
- ✅ Manage roles
- ✅ View activity logs
- ✅ Manage contact messages
- ✅ System settings
- ✅ Create backups

---

## 🎯 **COMMON TASKS & WHERE TO FIND THEM**

| Task | Who | Where to Go |
|------|-----|-------------|
| Create Purchase Request | Branch Manager | `/purchase-request` → Create |
| Approve Purchase Request | Central Office Admin | `/purchase-request` → Approve |
| Schedule Delivery | Logistics Coordinator | `/logistics-coordinator` → Schedule |
| Update Delivery Status | Supplier | `/supplier/orders` → Update Status |
| Confirm Delivery | Inventory Staff | `/deliveries` → Confirm |
| Add Stock | Inventory Staff | `/inventory/stockin` |
| Remove Stock | Inventory Staff | `/inventory/stockout` |
| Create Contract | Central Office Admin | `/supplier-contracts` → Create |
| Create Franchise Application | Franchise Manager | `/franchise/applications` → Create |
| Allocate Supplies | Franchise Manager | `/franchise/allocations` → Allocate |
| Create User | System Admin | `/admin/users` → Create |
| View Activity Logs | System Admin | `/admin/activity-logs` |
| Create Backup | System Admin | `/admin/backup` → Create |

---

## 💡 **TIPS & BEST PRACTICES**

### **1. Always Check Status**
- Bago mag-action, check muna ang status
- Example: Bago mag-approve, check kung "Pending" ba

### **2. Use Filters & Search**
- Gamitin ang filters para makita ang specific data
- Use search para mabilis makita ang item

### **3. Check Notifications**
- Always check notifications (may badge sa sidebar)
- Respond agad sa important notifications

### **4. Verify Before Confirming**
- Always verify details bago mag-confirm
- Example: Verify items bago mag-confirm delivery

### **5. Use Reports**
- Check reports regularly para makita ang trends
- Use reports para sa decision-making

### **6. Keep Contracts Updated**
- Monitor expiring contracts
- Renew contracts before expiration

---

## 🚨 **IMPORTANT REMINDERS**

1. **Always Login First** - Lahat ng actions need login
2. **Check Your Role** - Bawat role may specific access
3. **Verify Data** - Always verify bago mag-confirm
4. **Use Proper Status** - Update status correctly
5. **Check Notifications** - Important updates sa notifications
6. **Backup Regularly** - System Admin dapat mag-backup regularly

---

## 📞 **TROUBLESHOOTING**

### **Problem: Cannot see Purchase Request**
- **Solution:** Check kung tama ang role (Branch Manager or Central Office Admin)
- **Solution:** Check kung may existing requests

### **Problem: Cannot approve request**
- **Solution:** Check kung Central Office Admin ka
- **Solution:** Check kung "Pending" ang status

### **Problem: Delivery not showing**
- **Solution:** Check kung na-schedule na ng Logistics Coordinator
- **Solution:** Check kung tama ang branch

### **Problem: Inventory not updating**
- **Solution:** Check kung na-confirm na ang delivery
- **Solution:** Check kung tama ang Stock In/Out

---

**Happy Learning! 🎓**

Ito ang complete flow ng system ninyo. Practice mo lang step by step para masanay ka!

