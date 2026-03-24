# 🚀 Quick Start Guide - RestoOPNCode POS

**Get up and running in 5 minutes!**

---

## ⚡ Fastest Setup (Laragon - Windows)

### Step 1: Install Laragon (2 minutes)
```
1. Download: https://laragon.org/download/
2. Install to: C:\laragon
3. Launch Laragon
4. Click "Start All"
```

### Step 2: Copy Project (30 seconds)
```bash
# Copy or clone project to:
C:\laragon\www\restoopncode
```

### Step 3: Setup Database (2 minutes)
```
1. Open: http://localhost/phpmyadmin
2. Click "Import" tab
3. Choose file: C:\laragon\www\restoopncode\database\schema.sql
4. Click "Go"
5. Wait for success message
```

### Step 4: Import Sample Data (1 minute)
```
1. Still in phpMyAdmin, select database "posreato"
2. Click "Import" tab
3. Choose file: C:\laragon\www\restoopncode\database\seed.sql
4. Click "Go"
```

### Step 5: Login! (30 seconds)
```
URL: http://localhost/php-native/

Username: admin
Password: admin123
```

✅ **Done! You're ready to use the POS system!**

---

## 🎯 First Steps After Login

### 1. Create Your First Order
```
1. Go to: POS Tables
2. Click on any table (e.g., "Table 1")
3. Click menu items to add to cart
4. Click "Submit Order"
5. Done! Order is sent to kitchen
```

### 2. View Order in Kitchen
```
1. Go to: KDS Kitchen
2. You'll see the order appear
3. Click "Mark Ready" when done
```

### 3. Process Payment
```
1. Go to: Orders
2. Find your order
3. Click "Pay"
4. Select payment method
5. Complete payment
6. Receipt will print
```

### 4. Test Mobile Order
```
1. Go to: Mobile Order
2. Select table
3. Add items
4. Enter customer name & phone
5. Submit order
```

---

## 🔧 Common Issues & Quick Fixes

### Issue: "Database connection failed"
```
Fix:
1. Open: C:\laragon\www\restoopncode\php-native\config\database.php
2. Verify:
   - DB_NAME = posreato
   - DB_USER = root
   - DB_PASS = (empty)
```

### Issue: "Page not found"
```
Fix:
1. Make sure Laragon is running (green status)
2. Access: http://localhost/php-native/ (not http://localhost/)
```

### Issue: "Login doesn't work"
```
Fix:
1. Import seed.sql again
2. Clear browser cache (Ctrl+Shift+Delete)
3. Try username: admin, password: admin123
```

---

## 📱 Test All Features

### ✅ POS Flow
- [ ] Login
- [ ] Select table
- [ ] Add items to cart
- [ ] Add modifiers
- [ ] Add notes
- [ ] Submit order
- [ ] View in KDS
- [ ] Process payment
- [ ] Print receipt

### ✅ Mobile Order Flow
- [ ] Open mobile order
- [ ] Select items
- [ ] Add notes
- [ ] Enter customer info
- [ ] Submit order
- [ ] View in KDS

### ✅ Kitchen Display
- [ ] View orders
- [ ] Mark as ready
- [ ] Filter by status

### ✅ Management
- [ ] View orders list
- [ ] View reports
- [ ] Manage menu
- [ ] Manage inventory
- [ ] Open/close shift

---

## 💾 Backup Before Making Changes

```bash
# Double-click this file:
backup.bat

# Creates backup in:
C:\Project\restoopncode-backups\
```

---

## 📚 Learn More

- **Full Documentation:** See [README.md](README.md)
- **API Reference:** See API Endpoints section
- **Backup Guide:** See [BACKUP-GUIDE.md](BACKUP-GUIDE.md)
- **Project Status:** See [PROJECT-STATUS-REPORT.md](PROJECT-STATUS-REPORT.md)

---

## 🆘 Need Help?

### Check These First:
1. Is Laragon running? (green status)
2. Is database imported? (check phpMyAdmin)
3. Are you using correct URL? (http://localhost/php-native/)
4. Are credentials correct? (admin/admin123)

### Still Stuck?
1. Check browser console (F12) for errors
2. Check error logs in Laragon
3. Review troubleshooting section in README.md
4. Create an issue in repository

---

## 🎉 You're All Set!

**Your Restaurant POS is ready to use!**

Explore all features:
- 📊 Dashboard
- 🍽️ POS Orders
- 📱 Mobile Orders
- 👨‍🍳 Kitchen Display
- 📜 Reports
- ⚙️ Settings

**Enjoy!** 🚀

---

**Last Updated:** March 21, 2026  
**Version:** 1.0.0  
**Setup Time:** ~5 minutes
