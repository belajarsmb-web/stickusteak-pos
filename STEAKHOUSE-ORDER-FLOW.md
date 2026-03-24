# Steakhouse Order Flow - Complete Guide

## 📋 Order Flow Overview

```
CUSTOMER ORDER → WAITER TAKE ORDER → POST TO TABLE → KITCHEN → ADD ORDER → PAYMENT
      ↓              ↓                 ↓            ↓           ↓          ↓
   Mobile QR     POS System      Print Bill   KDS Display  Add More   Cash/Card
   or Menu       or Mobile       & Tickets    & Bar        Items      & Receipt
```

---

## 🔄 Detailed Flow Steps

### STEP 1: Customer Order (Multiple Options)

#### Option A: Mobile QR Order (Self-Service)
```
1. Customer scans QR code on table
2. Opens mobile ordering page
3. Selects items with mandatory modifiers:
   - For STEAK: Must select Sauce, Doneness, Potato
   - System validates before adding to cart
4. Enters name & phone
5. Submits order
6. Order goes directly to Kitchen & Bar
```

#### Option B: Waiter Take Order
```
1. Waiter approaches table
2. Opens POS system on tablet
3. Selects table number
4. Browses menu by category
5. Adds items to cart
6. For STEAK items:
   - Selects sauce (mandatory)
   - Selects doneness (mandatory)  
   - Selects potato choice (mandatory)
7. Adds notes (optional: "rare inside", "no salt")
8. Submits order
```

---

### STEP 2: Order Reception & Table Posting

```
Order Received →
  ↓
Create/Update Ticket for Table
  ↓
Generate Ticket Number (auto)
  ↓
Print Pre-Check / Kitchen Ticket
  ↓
Ticket displays on KDS (Kitchen Display System)
```

**What happens in system:**
- If table has no open ticket → Create new ticket
- Generate unique ticket number (e.g., T01-001)
- Link all orders to this ticket
- Table status changes to "occupied"

---

### STEP 3: Kitchen Display (KDS)

#### Kitchen Screen (Food Items)
```
┌─────────────────────────────────────────────┐
│ 🍳 KITCHEN DISPLAY         12:45 PM        │
├─────────────────────────────────────────────┤
│ [NEW] T01-001    Table 1                   │
│ ─────────────────────────────────          │
│ 1x Ribeye 200gr                            │
│    → Medium Rare                            │
│    → Mushroom Sauce                        │
│    → Mashed Potato                         │
│    → Notes: "celebrating birthday"         │
│                                             │
│ 1x Creamy Mushroom Pasta                    │
│                                             │
│ ⏱️ 05:23                                   │
│ [START] [READY]                             │
└─────────────────────────────────────────────┘
```

#### Bar Screen (Beverages)
```
┌─────────────────────────────────────────────┐
│ 🍸 BAR DISPLAY             12:45 PM        │
├─────────────────────────────────────────────┤
│ [NEW] T01-001    Table 1                   │
│ ─────────────────────────────────          │
│ 2x Fresh Orange Juice                      │
│ 1x Latte                                    │
│ 1x Mango Smoothie                           │
│                                             │
│ ⏱️ 03:15                                   │
│ [SERVED]                                    │
└─────────────────────────────────────────────┘
```

---

### STEP 4: Add New Order / Additional Items

```
Scenario: Customer wants to add more items

Waiter:
1. Opens POS → Select same table
2. System shows existing ticket
3. Waiter can ADD items to same ticket
4. New items appear on KDS as "ADD-ON"

Kitchen sees:
┌─────────────────────────────────────────────┐
│ [ADD-ON] T01-001    Table 1    12:50 PM    │
│ ─────────────────────────────────          │
│ 1x Caesar Salad (ADD-ON)                   │
│ 1x Tiramisu (ADD-ON)                       │
└─────────────────────────────────────────────┘
```

---

### STEP 5: Order Completion & Payment

#### Payment Flow
```
1. Customer requests bill
2. Waiter closes order for table
3. System calculates:
   - Subtotal
   - Tax (11% PPN)
   - Service Charge (if any)
   - Total
4. Print Final Bill / Pre-Check
5. Customer pays:
   - Cash → Calculate change
   - Debit/Credit Card → Process payment
   - QR Payment (GoPay/OVO/DANA)
6. System records payment
7. Print Receipt
8. Close ticket
9. Table status → "available" or "cleaning"
```

---

## 🎯 Recommended Steak Restaurant Flow

### Flow A: Traditional (Waiter-Centric)
```
1. Customer sits → Waiter greets
2. Waiter takes order via POS tablet
3. Waiter inputs modifiers (sauce/doneness/potato)
4. Order sent to kitchen
5. Food runner brings food to table
6. Waiter checks satisfaction
7. Customer requests more items → Waiter adds
8. Customer requests bill → Waiter processes
9. Payment → Receipt → Table turn over
```

### Flow B: QR Self-Order + Service
```
1. Customer scans QR → Self-order via mobile
2. Must complete modifier selection for steaks
3. Order goes to KDS automatically
4. Kitchen prepares
5. Waiter delivers food (service touch)
6. Customer can add via QR again
7. Customer pays at counter or via QR payment
8. Table auto-clears after payment
```

### Flow C: Hybrid (Recommended)
```
1. Customer scans QR OR waiter takes order
2. Orders with modifiers flow to KDS
3. Kitchen marks items READY
4. Food runner system notifies waiters
5. Waiter delivers to table
6. "Is everything okay?" check-in
7. Add-on orders via POS tablet
8. Bill request → Print → Payment → Close
```

---

## 📊 Status Workflow

```
Order Status:
pending → sent_to_kitchen → preparing → ready → served → completed → paid

Ticket Status:
open → closed → paid

Table Status:
available → occupied → cleaning → available
```

---

## ⚠️ Important Notes for Steak Restaurant

### Modifier Validation (CRITICAL)
- Steak items MUST have:
  - ✅ Sauce selected (1 required)
  - ✅ Doneness selected (1 required)
  - ✅ Potato selected (1 required)
- System blocks submission without these

### Kitchen Timing
- Steak cooking time: 12-20 minutes (depending on doneness)
- Recommended to inform customer of wait time
- KDS shows timer for each order

### Special Handling
- "Rare" orders need special handling
- Allergies/dietary: Add in notes field
- Split checks: Track per seat/ person

---

## 🚀 Quick Reference - POS Workflow

```
1. Login → Dashboard
2. POS Tables → Select table
3. POS Order → Add items with modifiers
4. Submit Order → Goes to KDS
5. KDS → Mark Ready when done
6. Orders → Select table → Pay
7. Receipt → Table freed
```

---

## 📱 Mobile Order Flow

```
1. Open: /mobile/order.php?table_id=X
2. Browse categories (Steak Premium first)
3. Tap item → Modifier modal appears
4. Select mandatory options:
   - Sauce (required)
   - Doneness (required)
   - Potato (required)
5. Add to cart
6. Repeat for more items
7. View Cart → Edit if needed
8. Enter Name & Phone
9. Submit Order
10. Order appears on KDS
```

---

**Last Updated:** March 24, 2026
**Version:** 2.0 - Steak Restaurant Edition
