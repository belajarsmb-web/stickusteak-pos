# 📝 CARA UBAH RECEIPT HEADER DARI SETTINGS PAGE

## ✅ **SOLUSI TERMUDAH - Via Web Interface!**

Sekarang Anda bisa mengubah receipt header langsung dari halaman Settings tanpa perlu SQL atau batch file!

---

## 📍 **CARA MENGUBAH:**

### **Step 1: Buka Halaman Settings**
```
http://localhost/php-native/pages/settings-receipt-template.php
```

### **Step 2: Isi Data Receipt Header**

Di bagian **"Receipt Header Information"**, isi field berikut:

1. **Restaurant Name** (Wajib)
   - Contoh: `Stickusteak`
   - Ini akan muncul di paling atas struk

2. **Address**
   - Contoh: `SouthCity, Jakarta`
   - Alamat restaurant

3. **Phone Number**
   - Contoh: `08123456789`
   - Nomor telepon

### **Step 3: Isi Footer (Optional)**

Di bagian **"Receipt Footer"**:

1. **Footer Text**
   - Contoh: `Thank you for your visit!`

2. **Website**
   - Contoh: `www.stickusteak.com`

3. **Social Media**
   - Contoh: `@stickusteak`

### **Step 4: Save**

Klik tombol **"Save Template"**

---

## 🎯 **HASIL DI STRUK:**

Setelah save, struk akan menampilkan:

```
Stickusteak          ← Restaurant Name
SouthCity, Jakarta   ← Address
Telp: 08123456789    ← Phone

Order #123
Table: 14
...

Thank you for your visit!  ← Footer
www.stickusteak.com        ← Website
@stickusteak               ← Social Media
```

---

## 📋 **FIELD YANG TERSEDIA:**

### **Header:**
- ✅ Restaurant Name (wajib)
- ✅ Address
- ✅ Phone Number

### **Footer:**
- ✅ Footer Text
- ✅ Website
- ✅ Social Media

### **Other Settings:**
- ✅ Logo upload
- ✅ Font size (Small/Medium/Large)
- ✅ Paper size (58mm/80mm)
- ✅ Show/hide tax breakdown
- ✅ Show/hide service charge
- ✅ Show/hide QR code

---

## 🔄 **ALTERNATIF LAIN:**

### **Option 1: Via Settings Page (TERMUDAH)** ✅
```
http://localhost/php-native/pages/settings-receipt-template.php
→ Isi form
→ Save
```

### **Option 2: Via Batch File**
```cmd
update-receipt-header.bat
→ Masukkan data
→ Enter
```

### **Option 3: Via SQL**
```sql
UPDATE receipt_templates SET
    header_text = 'Stickusteak',
    address = 'SouthCity, Jakarta',
    phone = '08123456789'
WHERE is_default = 1;
```

---

## ✅ **KEUNTUNGAN VIA SETTINGS PAGE:**

1. ✅ **No SQL needed**
2. ✅ **No command line**
3. ✅ **Visual form**
4. ✅ **Easy to edit**
5. ✅ **Preview available**
6. ✅ **Can save multiple templates**

---

**Status:** ✅ **RECEIPT HEADER CAN BE EDITED VIA SETTINGS**  
**Location:** `http://localhost/php-native/pages/settings-receipt-template.php`  
**Fields:** Restaurant Name, Address, Phone, Footer, Website, Social Media

Silakan buka halaman settings dan ubah receipt header dengan mudah! 🎉
