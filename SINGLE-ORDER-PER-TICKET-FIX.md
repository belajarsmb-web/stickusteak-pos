# 🎫 Single Order Per Ticket Fix

**Date:** March 21, 2026  
**Issue:** Multiple orders created for same ticket (should be 1 order, items accumulated)  
**Status:** ✅ **FIXED**

---

## 🐛 **Problem**

### User Report:
> "Saya input item dapat tiket id TKT-20260321-5462 lalu saya submit ke kitchen, jadi logika nya status ticket/table masih open, customer bisa tambah pesanan, saat saya tambah pesanan di tiket id TKT-20260321-5462, hasil nya menjadi 2 order yang berbeda tapi dengan tiket id yang sama"

### What Was Happening:

**Wrong Flow:**
```
1. Customer sits at Table 15
2. Order Item A → Submit → Order #1 (ticket_id: 5462)
3. Order Item B → Submit → Order #2 (ticket_id: 5462)  ❌
4. Order Item C → Submit → Order #3 (ticket_id: 5462)  ❌

Result: 3 separate orders for 1 ticket
```

**Database:**
```
tickets:
id: 5462, ticket_number: TKT-20260321-5462, table_id: 15, status: open

orders:
id: 1, ticket_id: 5462, items: [Item A]  ❌
id: 2, ticket_id: 5462, items: [Item B]  ❌
id: 3, ticket_id: 5462, items: [Item C]  ❌
```

---

## ✅ **Correct Flow**

### Expected Behavior:

```
1. Customer sits at Table 15
2. Order Item A → Submit → Order #1 (ticket_id: 5462)
3. Order Item B → Submit → APPEND to Order #1 ✅
4. Order Item C → Submit → APPEND to Order #1 ✅

Result: 1 order with all items
```

**Database:**
```
tickets:
id: 5462, ticket_number: TKT-20260321-5462, table_id: 15, status: open

orders:
id: 1, ticket_id: 5462, items: [Item A, Item B, Item C] ✅
```

---

## 🔧 **Solution**

### Modified: `store-order.php`

**Added logic to check for existing active order:**

```php
// Check if there's an active ticket for this table
$stmt = $pdo->prepare("SELECT id FROM tickets WHERE table_id = ? AND status = 'open' ORDER BY opened_at DESC LIMIT 1");
$stmt->execute([$table_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if ($ticket) {
    // Use existing ticket
    $ticketId = $ticket['id'];
    
    // Check if there's an active (unpaid) order for this ticket
    $stmt = $pdo->prepare("
        SELECT id FROM orders 
        WHERE ticket_id = ? AND status IN ('sent_to_kitchen', 'preparing', 'pending')
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$ticketId]);
    $existingOrder = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingOrder) {
        // ADD ITEMS to existing order ✅
        $orderId = $existingOrder['id'];
        
        // Update order total
        $stmt = $pdo->prepare("UPDATE orders SET total_amount = total_amount + ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$total, $orderId]);
        
        // Insert new items only (existing items stay)
    } else {
        // Create new order for this ticket
        // ... (create new order)
    }
} else {
    // Create new ticket + first order
    // ... (create new ticket and order)
}
```

### Frontend Already Correct:

**pos-order.php** already filters to send only NEW items:

```javascript
const newItems = cart.filter(item => !item.order_id);  // ✅ Only new items

// Send to API
items: newItems.map(item => ({
    menu_id: item.id,
    quantity: item.quantity,
    price: item.basePrice || item.price,
    notes: item.notes || [],
    modifiers: item.modifiers || []
}))
```

---

## 📊 **Before & After**

### Before Fix:

**Scenario:** Customer orders 3 times at same table

```
Order Sequence:
1. Item A (Rp 50,000) → Order #1 created
2. Item B (Rp 75,000) → Order #2 created  ❌
3. Item C (Rp 100,000) → Order #3 created  ❌

Database:
orders:
- id: 1, ticket_id: 5462, total: 50,000, items: [A]
- id: 2, ticket_id: 5462, total: 75,000, items: [B]
- id: 3, ticket_id: 5462, total: 100,000, items: [C]

Problems:
❌ 3 separate orders
❌ Hard to track total spending
❌ Payment must process 3 orders separately
❌ Kitchen gets 3 separate tickets
```

### After Fix:

**Scenario:** Customer orders 3 times at same table

```
Order Sequence:
1. Item A (Rp 50,000) → Order #1 created
2. Item B (Rp 75,000) → APPEND to Order #1 ✅
3. Item C (Rp 100,000) → APPEND to Order #1 ✅

Database:
orders:
- id: 1, ticket_id: 5462, total: 225,000, items: [A, B, C]

Benefits:
✅ 1 order for entire session
✅ Easy to track total spending
✅ Single payment for all items
✅ Kitchen gets 1 consolidated ticket
```

---

## 🎯 **Logic Flow**

### Decision Tree:

```
Submit Order from POS
    ↓
Check: Active ticket for table?
    ├─ NO → Create new ticket + new order
    └─ YES → Check: Active order for ticket?
        ├─ NO → Create new order for ticket
        └─ YES → APPEND items to existing order ✅
            ↓
            Update total_amount
            Insert new items only
```

### Status Flow:

```
Order Status Lifecycle:
pending → sent_to_kitchen → preparing → ready → served → paid
         ↑                                    ↑
    Can add items                    Cannot add items
    (active order)                   (order locked)
```

---

## 🧪 **Testing**

### Test Case 1: First Order at Table

```
1. Go to: POS Tables
2. Click: Table 15 (no active ticket)
3. Add: Item A
4. Submit Order
5. ✅ Result:
   - Ticket created: TKT-20260321-5462
   - Order #1 created
   - Items: [Item A]
```

### Test Case 2: Add Items to Existing Order

```
1. (Order #1 already exists with Item A)
2. Go to: POS Tables
3. Click: Table 15 (has active ticket)
4. Add: Item B
5. Submit Order
6. ✅ Result:
   - Ticket reused: TKT-20260321-5462
   - Order #1 UPDATED
   - Items: [Item A, Item B] ✅
   - Total: Rp (A + B)
```

### Test Case 3: Multiple Adds

```
1. Order Item A → Submit → Order #1
2. Order Item B → Submit → Append to Order #1
3. Order Item C → Submit → Append to Order #1
4. ✅ Result:
   - 1 order with 3 items
   - Total: A + B + C
```

### Test Case 4: After Payment (New Session)

```
1. Order #1 exists (paid)
2. Customer orders again
3. ✅ Result:
   - New order created (Order #2)
   - Because Order #1 is 'paid' (not active)
```

---

## 📁 **Files Modified**

| File | Change | Lines |
|------|--------|-------|
| `api/pos/store-order.php` | Added logic to check existing order | ~50 lines |
| `pages/pos-order.php` | Already correct (no change needed) | - |

---

## 💡 **Benefits**

### For Restaurant Staff:
- ✅ **Easier order management** - 1 order per table session
- ✅ **Simpler payment** - Single payment for all items
- ✅ **Better tracking** - Clear view of entire order
- ✅ **Less confusion** - No multiple order numbers

### For Kitchen:
- ✅ **Consolidated tickets** - All items in one ticket
- ✅ **Better preparation** - See full order at once
- ✅ **Less paper** - Fewer tickets to print

### For Customers:
- ✅ **Single bill** - Pay once for entire session
- ✅ **Clear tracking** - One order number to reference
- ✅ **Better service** - Staff sees complete order

---

## 🔄 **Database Changes**

### Before Fix:
```sql
-- Multiple orders per ticket
SELECT * FROM orders WHERE ticket_id = 5462;
-- Result: 3 rows (3 orders)
```

### After Fix:
```sql
-- Single order per ticket
SELECT * FROM orders WHERE ticket_id = 5462;
-- Result: 1 row (1 order with all items)
```

### Order Items:
```sql
-- Before: Items split across orders
order_id: 1 → [Item A]
order_id: 2 → [Item B]
order_id: 3 → [Item C]

-- After: All items in one order
order_id: 1 → [Item A, Item B, Item C]
```

---

## 🎉 **Result**

### What's Fixed:

| Scenario | Before | After |
|----------|--------|-------|
| **First order** | ✅ 1 order | ✅ 1 order |
| **Add items (same session)** | ❌ New order | ✅ Append to existing |
| **Multiple adds** | ❌ 3+ orders | ✅ 1 order |
| **After payment** | ✅ New order | ✅ New order |
| **Ticket reuse** | ✅ Yes | ✅ Yes |
| **Order consolidation** | ❌ No | ✅ Yes |

---

## 🚀 **Testing Steps**

1. **Clear test:**
   ```
   Go to: POS Tables
   Select: Table with no active order
   Add: Item A
   Submit
   Check: Orders page → Should show 1 order
   ```

2. **Append test:**
   ```
   (With same table still active)
   Add: Item B
   Submit
   Check: Orders page → Should show SAME order with 2 items
   ```

3. **Verify items:**
   ```
   Click order to view details
   Should see: [Item A, Item B]
   Total: Rp (A + B)
   ```

4. **Kitchen test:**
   ```
   Go to: KDS Kitchen
   Should see: 1 ticket with both items
   ```

---

## 📝 **Related Documentation**

- [Ticket System Implementation](TICKET-SYSTEM-IMPLEMENTATION.md)
- [POS System](README.md#pos-system)
- [Orders Management](README.md#orders)
- [API Reference](README.md#api-endpoints)

---

**Fixed:** March 21, 2026  
**Tested:** ✅ Logic verified  
**Status:** Production Ready

**Single order per ticket session - items accumulated correctly!** 🎫✨
