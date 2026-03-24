# Mobile Ordering with QR Code - Setup Guide

**Feature:** Customer can scan QR code on table and order from their phone  
**Status:** ✅ **READY TO USE**

---

## 📁 Files Created

| File | Purpose |
|------|---------|
| `mobile/generate-qr.php` | Generate QR codes for all tables |
| `mobile/order.php` | Mobile ordering page (customer-facing) |
| `api/mobile/submit-order.php` | API to submit mobile orders |
| `mobile/qrcodes/` | Folder to store downloaded QR codes |

---

## 🚀 How to Use

### Step 1: Generate QR Codes

1. **Open QR Generator:**
   ```
   http://localhost/php-native/mobile/generate-qr.php
   ```

2. **You'll see:**
   - All active tables with QR codes
   - Each QR code links to: `http://localhost/php-native/mobile/order.php?table_id=X`

3. **Download QR Codes:**
   - Click "📥 Download QR" button on each table
   - Save as PNG file
   - Print and place on tables

---

### Step 2: Customer Ordering Flow

```
1. Customer sits at table
2. Scans QR code with phone
3. Mobile ordering page opens
4. Customer browses menu
5. Adds items to cart
6. Clicks "Submit Order"
7. Order appears in kitchen display ✅
```

---

## 📱 Mobile Order Page Features

### Customer View:
- ✅ Table number displayed
- ✅ Category filter (All, Food, Drinks, etc.)
- ✅ Menu items with images
- ✅ Add to cart button
- ✅ Cart counter (items & total)
- ✅ Order summary modal
- ✅ Submit order button

### Order Details:
- **Service Type:** Dine In
- **Status:** sent_to_kitchen (goes to KDS immediately)
- **Tax:** Auto-calculated
- **Total:** Auto-calculated

---

## 🧪 Testing

### Test QR Generation:
```
1. Open: http://localhost/php-native/mobile/generate-qr.php
2. Verify all tables show with QR codes
3. Click download on any QR
4. Should download PNG file
```

### Test Mobile Order:
```
1. Open: http://localhost/php-native/mobile/order.php?table_id=1
2. Add items to cart
3. Click "View Cart"
4. Click "Submit Order"
5. Should show success message
6. Check KDS - order should appear
```

### Test Full Flow:
```
1. Generate QR for Table 1
2. Print QR code
3. Scan with phone
4. Order items
5. Submit
6. Check KDS display
7. Verify order appears correctly
```

---

## 🔧 Customization

### Change Base URL

**File:** `mobile/generate-qr.php` (Line 16)
```php
$baseUrl = 'http://localhost/php-native/mobile/order.php';
```

**For production:**
```php
$baseUrl = 'https://yourdomain.com/php-native/mobile/order.php';
```

### QR Code API

Currently using: `qrserver.com` (free, no API key needed)

**Alternative:** Use local QR generation library
```php
// Include phpqrcode library
include 'phpqrcode/qrlib.php';
QRcode::png($qrUrl, 'qrcodes/table-'.$tableId.'.png');
```

---

## 📊 Order Flow

```
Customer Phone
    ↓
Scan QR Code
    ↓
mobile/order.php?table_id=1
    ↓
Browse Menu & Add to Cart
    ↓
Submit Order
    ↓
api/mobile/submit-order.php
    ↓
Database (orders table)
    ↓
Status: 'sent_to_kitchen'
    ↓
KDS Display (Kitchen)
    ↓
Kitchen prepares food
    ↓
Serve to customer
```

---

## 🎨 UI/UX Features

### Mobile-First Design:
- ✅ Responsive layout
- ✅ Touch-friendly buttons
- ✅ Sticky category navigation
- ✅ Fixed cart bar at bottom
- ✅ Large images
- ✅ Clear pricing

### Visual Elements:
- Gradient header (purple theme)
- Category filter buttons
- Card-based menu items
- Floating cart bar
- Modal for cart review

---

## 🔐 Security Considerations

### Current Implementation:
- ⚠️ No authentication required
- ⚠️ No payment processing (order first, pay at counter)
- ⚠️ Relies on table QR code only

### Recommendations for Production:
1. **Add table verification:**
   - Staff confirms table before serving
   - Order shows table number for verification

2. **Payment:**
   - Customer pays at counter after receiving food
   - Or integrate payment gateway for pre-payment

3. **Rate limiting:**
   - Prevent spam orders from same IP
   - Max orders per table per hour

---

## 📝 Database Schema

### Tables Used:
```sql
-- orders
table_id, service_type, status, 
sub_total, tax_amount, total_amount,
created_by (1 = system/mobile)

-- order_items
order_id, menu_item_id, quantity, price,
notes (JSON), modifiers (JSON)

-- tables
id, table_number, name, status
```

---

## 🐛 Troubleshooting

### QR Code Not Working:
```
1. Check URL in QR code is correct
2. Verify table_id parameter exists
3. Ensure mobile/order.php is accessible
```

### Order Not Submitting:
```
1. Check browser console for errors
2. Verify database connection
3. Check API returns valid JSON
4. Ensure menu items have valid IDs
```

### Order Not Appearing in KDS:
```
1. Check order status = 'sent_to_kitchen'
2. Verify KDS filter includes mobile orders
3. Check table_id is valid
4. Ensure items have valid menu_item_id
```

---

## ✨ Features to Add (Optional)

### Future Enhancements:
1. **Order Tracking:**
   - Show order status to customer
   - "Preparing", "Ready", "Served"

2. **Call Waiter Button:**
   - Customer can request assistance

3. **Payment Integration:**
   - Pay via phone (QRIS, e-wallet)
   - Split bill feature

4. **Multi-language:**
   - English, Indonesian, Chinese
   - Auto-detect from phone

5. **Promo Codes:**
   - Enter discount code
   - Loyalty points redemption

---

## 📞 Quick Reference

### URLs:
- **QR Generator:** `/php-native/mobile/generate-qr.php`
- **Mobile Order:** `/php-native/mobile/order.php?table_id=X`
- **Submit API:** `/php-native/api/mobile/submit-order.php`
- **KDS Display:** `/php-native/pages/kds-kitchen.php`

### Files:
- **Generate QR:** `mobile/generate-qr.php`
- **Order Page:** `mobile/order.php`
- **Submit API:** `api/mobile/submit-order.php`

---

**Status:** ✅ **READY TO USE**  
**Test:** Generate QR → Scan → Order → Verify in KDS  
**Production:** Change base URL to your domain

🎉 Mobile ordering is ready to use!
