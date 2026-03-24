# 🧪 KDS Testing Guide - RestoQwen POS

## ✅ Step-by-Step Test Order to KDS

### **PENTING: Jangan Langsung Bayar!**

Order hanya muncul di KDS saat status: `pending`, `preparing`, `ready`, `in_progress`
Setelah `paid`, order hilang dari KDS (ini correct!)

---

## 📝 **Test Steps**

### **Step 1: Buat Order Baru (JANGAN BAYAR!)**

1. Buka: `http://localhost/php-native/pages/pos-tables.php`
2. Klik meja yang **Available** (hijau)
3. Klik menu item (e.g., "Black Angus Tenderloin")
4. Pilih modifiers (jika ada)
5. Klik **"Add to Order"**
6. Klik **"Submit Order"** (bukan "Bayar / Pay"!)
7. ✅ Order dibuat dengan status `pending`
8. ✅ Order ID akan muncul di alert

**CATATAN PENTING:**
- ❌ **JANGAN** klik "Bayar / Pay" dulu!
- ✅ Order harus status `pending` dulu

---

### **Step 2: Cek KDS Kitchen**

1. Buka tab baru: `http://localhost/php-native/pages/kds-kitchen.php`
2. **Refresh** halaman (F5)
3. ✅ Order baru akan muncul dengan:
   - Order # di kiri atas
   - Table name
   - Items list
   - Badge **[SUBMITTED]** (jika sudah di-print)

**Expected Result:**
```
┌─────────────────────────────────┐
│ Order #54        Table 1        │
│ 17/03/2026 15:30   2 min ago   │
├─────────────────────────────────┤
│ 1x Black Angus Tenderloin       │
│    [Medium Rare]                │
│    [Black Pepper Sauce]         │
├─────────────────────────────────┤
│ [02:30]  [SUBMITTED]            │
└─────────────────────────────────┘
```

---

### **Step 3: Submit to Kitchen (Optional)**

1. Di halaman **POS Order** (bukan KDS)
2. Klik **"Submit to Kitchen"** pada item
3. ✅ Item marked as printed
4. ✅ Print count = 1

---

### **Step 4: Bayar (Setelah Makanan Siap)**

1. Setelah kitchen selesai prepare
2. Kembali ke **POS Order**
3. Klik **"Bayar / Pay"**
4. Pilih payment method
5. Process payment
6. ✅ Status jadi `paid`
7. ✅ Order **hilang dari KDS** (ini correct!)

---

## 🔍 **Troubleshooting**

### **Order Tidak Muncul di KDS?**

**Check 1: Status Order**
```sql
SELECT id, table_id, status, created_at 
FROM orders 
WHERE status IN ('pending', 'preparing', 'ready', 'in_progress')
ORDER BY created_at DESC;
```

Jika semua order status `paid`, berarti langsung bayar tanpa submit order dulu!

**Check 2: Order Items**
```sql
SELECT oi.id, oi.order_id, oi.menu_item_id, m.name as item_name, 
       c.name as category, oi.is_printed, oi.print_count
FROM order_items oi
JOIN menu_items m ON oi.menu_item_id = m.id
LEFT JOIN categories c ON m.category_id = c.id
WHERE oi.order_id = [ORDER_ID];
```

**Check 3: KDS API Response**
```
http://localhost/php-native/api/kds/kitchen-orders.php
```

Harus return JSON dengan orders array tidak kosong.

---

## ✅ **Expected Flow**

```
POS Tables → Select Table
    ↓
POS Order → Add Items
    ↓
Submit Order (NOT Pay!)
    ↓
Order Status: pending ✅
    ↓
KDS Kitchen Shows Order ✅
    ↓
Submit to Kitchen (Optional)
    ↓
Kitchen Prepares Food
    ↓
Customer Ready to Pay
    ↓
POS Order → Bayar / Pay
    ↓
Order Status: paid
    ↓
Order Removed from KDS ✅
```

---

## 🎯 **Quick Test**

**Dari Browser Console** (F12):

```javascript
// Create test order
fetch('/php-native/api/pos/store-order.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        table_id: 14,
        table_name: 'Table 1',
        items: [{
            menu_id: 2,
            quantity: 1,
            price: 285000,
            notes: [],
            modifiers: ['Medium Rare']
        }]
    })
})
.then(r => r.json())
.then(d => console.log('Order created:', d));

// Check KDS
fetch('/php-native/api/kds/kitchen-orders.php')
.then(r => r.json())
.then(d => console.log('KDS Orders:', d));
```

---

## 📊 **Database Check**

```sql
-- Check recent orders
SELECT id, table_id, status, total_amount, created_at 
FROM orders 
ORDER BY created_at DESC 
LIMIT 10;

-- Check KDS visible orders only
SELECT id, table_id, status, total_amount, created_at 
FROM orders 
WHERE status IN ('pending', 'preparing', 'ready', 'in_progress')
ORDER BY created_at DESC;

-- Check order items
SELECT oi.*, m.name as item_name, c.name as category
FROM order_items oi
JOIN menu_items m ON oi.menu_item_id = m.id
LEFT JOIN categories c ON m.category_id = c.id
WHERE oi.order_id IN (
    SELECT id FROM orders 
    WHERE status IN ('pending', 'preparing', 'ready', 'in_progress')
);
```

---

## ✅ **Success Criteria**

- [ ] Order dibuat dengan status `pending`
- [ ] Order muncul di KDS Kitchen
- [ ] Item category = "Premium Steaks" / "Burgers & Sandwiches" / "Side Dishes" / "Desserts"
- [ ] Item NOT category = "Beverages" (ini untuk Bar Display)
- [ ] Submit to Kitchen berfungsi
- [ ] Print count bertambah
- [ ] Payment mengubah status ke `paid`
- [ ] Order hilang dari KDS setelah paid

---

**Last Updated:** 2026-03-18
**Version:** 1.0
