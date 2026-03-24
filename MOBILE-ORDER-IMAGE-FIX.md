# 📱 MOBILE ORDER - IMAGE NOT DISPLAYING FIX

## 🐛 **PROBLEM:**
Product images not displaying in QR code / mobile order page.

---

## 🔍 **ROOT CAUSE:**

1. **Wrong image path** - Was using `/php-native/uploads/` instead of full path
2. **Empty image_url** - Menu items may not have image_url set in database
3. **Missing files** - Image files may not exist in uploads folder

---

## ✅ **FIXES APPLIED:**

### **Fix #1: Correct Image Path** ✅

**File:** `php-native/mobile/order.php`

**Before:**
```php
<img src="/php-native/uploads/<?php echo $item['image_url']; ?>">
```

**After:**
```php
<img src="<?php echo htmlspecialchars($item['image_url']); ?>">
```

**Why:** Database already stores full path like `/php-native/uploads/menu-items/steak.jpg`

---

### **Fix #2: Fallback for No Image** ✅

Added placeholder when no image:
```php
<?php if ($item['image_url']): ?>
    <img src="<?php echo $item['image_url']; ?>">
<?php else: ?>
    <div style="...">
        <i class="bi bi-camera"></i> <!-- Camera icon placeholder -->
    </div>
<?php endif; ?>
```

---

## 📋 **HOW TO ADD MENU IMAGES:**

### **Option 1: Via Menu Management Page (Easiest)**

1. **Open Menu Management:**
   ```
   http://localhost/php-native/pages/menu.php
   ```

2. **Edit Menu Item:**
   - Click Edit on menu item
   - Click "Upload Image"
   - Select image file
   - Save

3. **Image will be saved to:**
   ```
   C:\Project\restoopncode\php-native\uploads\menu-items\your-image.jpg
   ```

4. **Database updated with:**
   ```
   image_url = '/php-native/uploads/menu-items/your-image.jpg'
   ```

---

### **Option 2: Manual Upload**

1. **Prepare image** (JPG/PNG, max 500KB)

2. **Copy to folder:**
   ```
   C:\Project\restoopncode\php-native\uploads\menu-items\
   ```

3. **Update database:**
   ```sql
   USE posreato;
   
   UPDATE menu_items 
   SET image_url = '/php-native/uploads/menu-items/your-image.jpg'
   WHERE id = YOUR_MENU_ITEM_ID;
   ```

---

### **Option 3: Via API**

**Endpoint:** `POST /php-native/api/menu/upload-image.php`

**Request:**
```javascript
const formData = new FormData();
formData.append('image', fileInput.files[0]);
formData.append('menu_item_id', menuItemId);

fetch('/php-native/api/menu/upload-image.php', {
    method: 'POST',
    body: formData
});
```

**Response:**
```json
{
    "success": true,
    "url": "/php-native/uploads/menu-items/abc123.jpg"
}
```

---

## 🧪 **TEST:**

### **Test 1: Check Images Exist**

1. **Open folder:**
   ```
   C:\Project\restoopncode\php-native\uploads\menu-items\
   ```

2. **Check if images exist**
   - Should see files like: `steak.jpg`, `burger.png`, etc.

---

### **Test 2: Check Database**

1. **Open phpMyAdmin**

2. **Run SQL:**
   ```sql
   USE posreato;
   SELECT id, name, image_url FROM menu_items WHERE is_active = 1;
   ```

3. **Check results:**
   - `image_url` should be like: `/php-native/uploads/menu-items/xxx.jpg`
   - If NULL or empty → No image

---

### **Test 3: Mobile Order Page**

1. **Open:**
   ```
   http://localhost/php-native/mobile/order.php?table_id=1
   ```

2. **Check images:**
   - ✅ Images display correctly
   - ✅ Fallback icon shows if no image
   - ✅ Images are 150px height, responsive width

---

## 📊 **IMAGE REQUIREMENTS:**

| Property | Requirement |
|----------|-------------|
| **Format** | JPG, PNG, GIF |
| **Max Size** | 500KB |
| **Recommended Size** | 800x600px or 4:3 ratio |
| **Folder** | `php-native/uploads/menu-items/` |
| **URL Format** | `/php-native/uploads/menu-items/filename.jpg` |

---

## 🐛 **TROUBLESHOOTING:**

### **Problem: Images still not showing**

**Check 1: File exists?**
```
Check: C:\Project\restoopncode\php-native\uploads\menu-items\your-image.jpg
```

**Check 2: Database URL correct?**
```sql
SELECT image_url FROM menu_items WHERE id = YOUR_ID;
-- Should be: /php-native/uploads/menu-items/your-image.jpg
```

**Check 3: File permissions?**
```
Right-click folder → Properties → Security
Ensure: Read permission for web server
```

**Check 4: Browser console errors?**
```
Press F12 → Console tab
Look for: 404 errors on image URLs
```

---

### **Problem: Upload not working**

**Check 1: PHP upload limits**
```ini
; In php.ini
upload_max_filesize = 2M
post_max_size = 3M
```

**Check 2: Folder writable?**
```
chmod 777 C:\Project\restoopncode\php-native\uploads\menu-items\
```

**Check 3: File too large?**
```
Max size: 500KB recommended
Resize image if larger
```

---

## ✅ **SUMMARY:**

**Files Modified:**
- ✅ `php-native/mobile/order.php` - Fixed image path & added fallback

**Files Created:**
- ✅ `database/fix-menu-images.sql` - SQL to check images
- ✅ `MOBILE-ORDER-IMAGE-FIX.md` - This documentation

**Status:** ✅ **FIXED**
- Images now display correctly
- Fallback icon for items without images
- Responsive image sizing

---

**Next:** Upload menu item images via Menu Management page! 🎉
