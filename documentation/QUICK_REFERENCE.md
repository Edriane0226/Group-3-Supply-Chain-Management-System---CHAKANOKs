# ⚡ Quick Reference Card - ChakaNoks SCMS

## 🔑 Login Credentials (All passwords: `password123`)

| Role | ID | Email |
|------|-----|-------|
| System Admin | 23116005 | admin@chakanoks.com |
| Central Office Admin | 23116000 | Ed@gmail.com |
| Branch Manager | 23116002 | marcobatiller@gmail.com |
| Inventory Staff | 23116001 | jaspercanitan@gmail.com |
| Logistics Coordinator | 23116003 | vincentdionio@gmail.com |
| Franchise Manager | 23116004 | kristineamojallas@gmail.com |
| Supplier | 1001 | (San Miguel Foods - use Supplier ID) |

---

## 🎯 Quick Test Flow (5 Minutes)

### 1. **System Admin** (ID: 23116005)
- ✅ Login → Check Dashboard
- ✅ Go to **User Management** → Create User
- ✅ Go to **Contact Messages** → Check notifications
- ✅ Try **Delete User** → Test secure confirmation

### 2. **Branch Manager** (ID: 23116002)
- ✅ Login → View Dashboard
- ✅ Go to **Purchase Request** → Create Request

### 3. **Inventory Staff** (ID: 23116001)
- ✅ Login → View Overview
- ✅ Go to **Stock In** → Add items

### 4. **Franchise Manager** (ID: 23116004)
- ✅ Login → View Dashboard
- ✅ Go to **Applications** → Create Application

### 5. **Supplier** (ID: 1001)
- ✅ Login → View Dashboard
- ✅ Go to **Purchase Orders** → View orders

---

## 🔗 Quick Links

- **Login:** `http://localhost/CHAKANOKS_SCMS/login`
- **System Admin:** `http://localhost/CHAKANOKS_SCMS/admin`
- **Franchise:** `http://localhost/CHAKANOKS_SCMS/franchise`
- **Contact Us:** `http://localhost/CHAKANOKS_SCMS/contact`

---

## 📋 Role Access Summary

| Role | Main Access | Key Features |
|------|-------------|--------------|
| **System Admin** | `/admin` | Users, Roles, Settings, Backup |
| **Central Office** | `/dashboard` | All branches, Approve requests |
| **Branch Manager** | `/dashboard` | Branch dashboard, Create requests |
| **Inventory Staff** | `/inventory/overview` | Stock In/Out, Deliveries, Reports |
| **Logistics** | `/logistics-coordinator` | Delivery schedules, Tracking |
| **Franchise Manager** | `/franchise` | Applications, Payments, Allocations |
| **Supplier** | `/supplier/dashboard` | Orders, Deliveries, Invoices |

---

## ⚠️ Common Commands

```bash
# Run migrations
php spark migrate

# Run seeders
php spark db:seed

# Clear cache
php spark cache:clear
```

---

## 🎨 UI Features to Test

- ✅ Sidebar branding (role name)
- ✅ Notification badges (unread messages)
- ✅ Live search (User Management)
- ✅ Secure delete (type "DELETE")
- ✅ Dropdown menus
- ✅ Modals and forms
- ✅ Success/Error messages

---

**For detailed guide, see: `TESTING_GUIDE.md`**

