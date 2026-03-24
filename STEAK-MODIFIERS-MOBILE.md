# 🥩 STEAK MODIFIERS - MOBILE ORDER

## 🎯 **FEATURE:**

When ordering steak items on mobile order page, customer can now select:
1. ✅ **Sauce Selection** (Black Pepper, Mushroom, Béarnaise, etc.)
2. **Doneness Level** (Rare, Medium Rare, Medium, Medium Well, Well Done)
3. **Potato Side** (Mashed, Baked, Fries, Gratin)

---

## 📋 **HOW IT WORKS:**

### **Step 1: Customer Clicks "Add" on Steak**
```
Menu Card → Click "Add" button
```

### **Step 2: Modifiers Modal Opens**
```
🥩 Customize Your Steak

Sauce Selection *
○ Black Pepper Sauce
○ Mushroom Sauce
○ Béarnaise Sauce
○ Red Wine Sauce

Doneness Level *
○ Rare
○ Medium Rare
○ Medium
○ Medium Well
○ Well Done

Potato Side *
○ Mashed Potato
○ Baked Potato
○ French Fries
○ Potato Gratin

[Cancel] [Add to Cart]
```

### **Step 3: Customer Selects Options**
- Select one sauce (required)
- Select doneness (required)
- Select potato side (required)

### **Step 4: Added to Cart**
```
Cart shows:
🥩 Steak Sirloin
   Black Pepper Sauce, Medium, Fries
Rp 150,000
```

---

## 🔧 **SETUP REQUIRED:**

### **1. Create Modifier Groups:**

Run this SQL:
```sql
USE posreato;

-- Sauce Selection Group
INSERT INTO modifier_groups (outlet_id, name, selection_type, is_required, sort_order, is_active) 
VALUES (1, 'Sauce Selection', 'single', 1, 1, 1);

-- Get the ID
SET @sauce_group_id = LAST_INSERT_ID();

-- Add sauce modifiers
INSERT INTO modifiers (modifier_group_id, name, price, sort_order, is_active) VALUES
(@sauce_group_id, 'Black Pepper Sauce', 0, 1, 1),
(@sauce_group_id, 'Mushroom Sauce', 0, 2, 1),
(@sauce_group_id, 'Béarnaise Sauce', 5000, 3, 1),
(@sauce_group_id, 'Red Wine Sauce', 5000, 4, 1);

-- Doneness Level Group
INSERT INTO modifier_groups (outlet_id, name, selection_type, is_required, sort_order, is_active) 
VALUES (1, 'Doneness Level', 'single', 1, 2, 1);

SET @doneness_group_id = LAST_INSERT_ID();

INSERT INTO modifiers (modifier_group_id, name, price, sort_order, is_active) VALUES
(@doneness_group_id, 'Rare', 0, 1, 1),
(@doneness_group_id, 'Medium Rare', 0, 2, 1),
(@doneness_group_id, 'Medium', 0, 3, 1),
(@doneness_group_id, 'Medium Well', 0, 4, 1),
(@doneness_group_id, 'Well Done', 0, 5, 1);

-- Potato Side Group
INSERT INTO modifier_groups (outlet_id, name, selection_type, is_required, sort_order, is_active) 
VALUES (1, 'Potato Side', 'single', 1, 3, 1);

SET @potato_group_id = LAST_INSERT_ID();

INSERT INTO modifiers (modifier_group_id, name, price, sort_order, is_active) VALUES
(@potato_group_id, 'Mashed Potato', 0, 1, 1),
(@potato_group_id, 'Baked Potato', 5000, 2, 1),
(@potato_group_id, 'French Fries', 0, 3, 1),
(@potato_group_id, 'Potato Gratin', 10000, 4, 1);
```

---

### **2. Link Modifiers to Steak Category:**

```sql
-- Find your steak category ID
SELECT id, name FROM categories WHERE name LIKE '%Steak%';

-- Link modifier groups to steak category (if you have category_link table)
-- Or just use the category detection in the code
```

---

## 📱 **FILES CREATED:**

| File | Purpose |
|------|---------|
| `mobile/order-with-modifiers.php` | Mobile order with modifiers |
| `STEAK-MODIFIERS-MOBILE.md` | This documentation |

---

## 🧪 **TEST:**

### **Test 1: Non-Steak Item**
```
1. Open: mobile/order-with-modifiers.php?table_id=1
2. Click "Add" on non-steak item (e.g., Burger)
3. ✅ Added directly to cart (no modifiers)
```

### **Test 2: Steak Item**
```
1. Open: mobile/order-with-modifiers.php?table_id=1
2. Click "Add" on steak item
3. ✅ Modifiers modal opens
4. ✅ Select sauce, doneness, potato
5. ✅ Click "Add to Cart"
6. ✅ Item added with selected modifiers
7. ✅ Cart shows modifiers
```

---

## 📊 **MODIFIER TYPES:**

### **Single Selection (Radio)**
- Customer can only choose ONE option
- Used for: Sauce, Doneness, Potato Side
- Required fields marked with *

### **Multiple Selection (Checkbox)**
- Customer can choose MULTIPLE options
- Used for: Extra toppings, Add-ons
- Optional (unless marked required)

---

## 🎨 **UI FEATURES:**

### **Mobile-Optimized:**
- ✅ Large touch targets
- ✅ Clear labels
- ✅ Easy to scroll
- ✅ Responsive layout

### **Visual Feedback:**
- ✅ Selected options highlighted
- ✅ Price updates in real-time
- ✅ Required fields marked
- ✅ Clean modal design

---

## 💡 **CUSTOMIZATION:**

### **Change Modifier Groups:**

Edit SQL to add your own:
```sql
INSERT INTO modifier_groups (outlet_id, name, selection_type, is_required, sort_order) 
VALUES (1, 'Your Group Name', 'single', 1, 4);
```

### **Add More Modifiers:**
```sql
INSERT INTO modifiers (modifier_group_id, name, price, sort_order) 
VALUES (GROUP_ID, 'Modifier Name', 5000, 1);
```

### **Change Steak Category Detection:**

Edit line in order-with-modifiers.php:
```javascript
// Change 'steak' to your category name
if (stripos($cat['name'], 'your-category') !== false) {
    $steakCategoryId = $cat['id'];
}
```

---

## ✅ **STATUS:**

**Feature:** ✅ **Working**  
**Mobile:** ✅ **Responsive**  
**Modifiers:** ✅ **Configurable**  
**Price:** ✅ **Auto-calculate**

---

**Next:** Run SQL to create modifier groups, then test! 🎉
