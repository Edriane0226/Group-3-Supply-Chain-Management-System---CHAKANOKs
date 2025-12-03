# 🎬 ChakaNoks SCMS - Visual Walkthrough Guide

## 📸 **CURRENT SCREEN: Purchase Request Form**

Nandito tayo ngayon sa **"New Purchase Request"** form. Ipapakita ko sa'yo kung ano ang nakikita mo at kung ano ang gagawin.

---

## 👀 **ANO ANG NAKIKITA MO NGAYON:**

### **Left Sidebar (Orange):**
- ✅ **ChakaNoks Logo** - Chicken logo
- ✅ **"Central"** - Branch name
- ✅ **Navigation Menu:**
  - Dashboard
  - Inventory
  - **Purchase Request** ← (Currently active - highlighted)
  - Deliveries
  - Logout

### **Main Content Area:**
- ✅ **Title:** "New Purchase Request"
- ✅ **Back Button** (top right)
- ✅ **Form Fields:**
  1. **Supplier** - Dropdown (Select supplier)
  2. **Item Name** - Dropdown (Select supplier first)
  3. **Quantity** - Number input
  4. **Unit** - Dropdown (pcs, box, kg, liter)
  5. **Unit Price (₱)** - Number input
  6. **Description** - Text area
- ✅ **Buttons:**
  - **Add Item** (gray button)
  - **Submit Request** (blue button)

---

## 📝 **STEP-BY-STEP: Paano Gumawa ng Purchase Request**

### **STEP 1: Select Supplier**
```
1. Click sa "Supplier" dropdown
2. Makikita mo ang list:
   - San Miguel Foods Inc.
   - Bounty Fresh Chicken Supply
   - NutriAsia Condiments Distributor
   - Mega Packaging Solutions
   - PureOil Philippines
   - FastServe Kitchen Equipment Corp.
   - CleanPro Janitorial Supplies
   - FreshVeg Produce Supplier
3. Select: "San Miguel Foods Inc."
```

**What happens:**
- Item Name dropdown mag-u-update
- Makikita mo na ang items from San Miguel Foods

---

### **STEP 2: Select Item**
```
1. Click sa "Item Name" dropdown
2. Makikita mo ang items from San Miguel Foods:
   - Chicken Breast
   - Pork Belly
   - Ground Beef
3. Select: "Chicken Breast"
```

**What happens:**
- Unit Price maaaring auto-fill (depende sa implementation)
- Ready ka na mag-enter ng quantity

---

### **STEP 3: Enter Quantity**
```
1. Click sa "Quantity" field
2. Type: 50
```

**What happens:**
- Quantity set to 50

---

### **STEP 4: Select Unit**
```
1. Click sa "Unit" dropdown
2. Options:
   - pcs (pieces)
   - box
   - kg (kilograms)
   - liter
3. Select: "kg" (kilos)
```

**What happens:**
- Unit set to kg

---

### **STEP 5: Enter Unit Price**
```
1. Click sa "Unit Price (₱)" field
2. Type: 210.00
```

**What happens:**
- Unit price set to ₱210.00
- Total automatically calculated (50 kg × ₱210 = ₱10,500)

---

### **STEP 6: Add Description (Optional)**
```
1. Click sa "Description" field
2. Type: "For branch inventory restocking"
```

**What happens:**
- Description added

---

### **STEP 7: Add More Items (Optional)**
```
Kung gusto mo mag-add ng more items:
1. Click "Add Item" button
2. Repeat Steps 1-6 for new item
3. Puwede ka mag-add ng multiple items
```

---

### **STEP 8: Submit Request**
```
1. Review lahat ng details
2. Make sure tama ang:
   - Supplier
   - Items
   - Quantities
   - Prices
3. Click "Submit Request" button
```

**What happens:**
- Purchase Request created
- Status = "Pending"
- Redirected to Purchase Request list
- Central Office Admin makikita ang request

---

## 🎯 **NEXT STEPS AFTER SUBMITTING:**

### **After You Submit:**
1. **You'll see:** Success message
2. **You'll be redirected to:** Purchase Request list page
3. **Your request will show:** Status = "Pending"

### **What Happens Next:**
1. **Central Office Admin** will see your request
2. **Central Office Admin** will approve/reject
3. If approved → Purchase Order created
4. Logistics Coordinator will schedule delivery
5. Supplier will deliver
6. Inventory Staff will receive

---

## 🔄 **COMPLETE FLOW VISUALIZATION:**

```
┌─────────────────────────────────────────┐
│  YOU ARE HERE:                         │
│  📝 Creating Purchase Request          │
└─────────────────────────────────────────┘
              │
              │ Submit
              ▼
┌─────────────────────────────────────────┐
│  ✅ Request Created (Pending)          │
└─────────────────────────────────────────┘
              │
              │ Central Office Admin sees
              ▼
┌─────────────────────────────────────────┐
│  👔 Central Office Admin               │
│  - Reviews Request                     │
│  - Approves/Rejects                    │
└─────────────────────────────────────────┘
              │
              │ If Approved
              ▼
┌─────────────────────────────────────────┐
│  📦 Purchase Order Created             │
└─────────────────────────────────────────┘
              │
              │ Logistics schedules
              ▼
┌─────────────────────────────────────────┐
│  🚚 Delivery Scheduled                 │
└─────────────────────────────────────────┘
              │
              │ Supplier delivers
              ▼
┌─────────────────────────────────────────┐
│  ✅ Inventory Staff Receives           │
│  - Stock Updated                       │
└─────────────────────────────────────────┘
```

---

## 💡 **TIPS WHILE FILLING UP FORM:**

### **1. Always Select Supplier First**
- Item Name dropdown ay dependent sa Supplier
- Kailangan mo muna pumili ng Supplier bago makita ang items

### **2. Check Prices**
- Verify kung tama ang unit price
- Check kung reasonable ang total amount

### **3. Add Description**
- Helpful para sa Central Office Admin
- Explain kung bakit kailangan ang items

### **4. Review Before Submit**
- Double-check lahat ng details
- Make sure tama ang quantities

---

## 🎬 **PRACTICE SCENARIO:**

### **Scenario: Order Chicken Supplies**

**Step 1:** Select Supplier → "Bounty Fresh Chicken Supply"  
**Step 2:** Select Item → "Whole Chicken"  
**Step 3:** Enter Quantity → 30  
**Step 4:** Select Unit → "kg"  
**Step 5:** Enter Price → 190.00  
**Step 6:** Description → "Weekly restocking for branch"  
**Step 7:** Click "Submit Request"

**Result:**
- Request created
- Total: 30 kg × ₱190 = ₱5,700
- Status: Pending
- Waiting for Central Office Admin approval

---

## 📋 **QUICK CHECKLIST:**

Before submitting, make sure:
- [ ] Supplier selected
- [ ] Item selected
- [ ] Quantity entered
- [ ] Unit selected
- [ ] Price entered
- [ ] Description added (optional but recommended)
- [ ] All details reviewed

---

## 🚨 **COMMON MISTAKES:**

### **❌ Mistake 1: Forgot to Select Supplier**
- **Problem:** Item Name dropdown walang options
- **Solution:** Select Supplier first

### **❌ Mistake 2: Wrong Quantity**
- **Problem:** Ordered too much or too little
- **Solution:** Double-check quantity before submit

### **❌ Mistake 3: Wrong Unit**
- **Problem:** Selected "pcs" instead of "kg"
- **Solution:** Make sure unit matches the item

### **❌ Mistake 4: Submitted Without Review**
- **Problem:** Wrong details submitted
- **Solution:** Always review before submit

---

## ✅ **AFTER YOU SUBMIT:**

### **You'll See:**
1. Success message: "Purchase Request created successfully"
2. Redirected to Purchase Request list
3. Your request will show with status "Pending"

### **You Can:**
- View your request
- See status
- Wait for approval
- Create another request

---

## 🎯 **NEXT: What to Do After Creating Request**

### **Option 1: Wait for Approval**
- Check Purchase Request list regularly
- See if status changed to "Approved"

### **Option 2: Create Another Request**
- Click "New Request" again
- Create request for different supplier/items

### **Option 3: Check Dashboard**
- Go back to Dashboard
- See if may updates

---

**Practice mo lang! Try mo mag-create ng Purchase Request ngayon! 🚀**

