# RestoQwen POS - Premium Black & Gold Theme

## 🎨 Theme Overview

Premium black & gold theme dengan nuansa modern, elegant, dan animasi smooth untuk semua halaman RestoQwen POS.

## 📁 Files Created

### **1. Global CSS**
```
/php-native/assets/css/premium-theme.css
```
- CSS global untuk semua halaman
- Variables untuk colors
- Animations & transitions
- Responsive design
- Custom scrollbar
- Form styles
- Table styles
- Button styles
- Badge styles

### **2. Header Include**
```
/php-native/includes/premium-header.php
```
- HTML head dengan premium theme
- Sidebar navigation
- Auto-active menu detection
- Google Fonts (Playfair Display + Poppins)

### **3. Footer Include**
```
/php-native/includes/premium-footer.php
```
- Closing tags
- Bootstrap JS
- Custom JS include support

## 🚀 Cara Menggunakan

### **Option 1: Include Header/Footer (Recommended)**

```php
<?php
$page_title = "Dashboard"; // Set page title
include __DIR__ . '/../includes/premium-header.php';
?>

<!-- Your page content here -->
<div class="page-header">
    <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
</div>

<!-- Your content -->

<?php include __DIR__ . '/../includes/premium-footer.php'; ?>
```

### **Option 2: Manual CSS Link**

Tambahkan di `<head>` setiap halaman:
```html
<link href="/php-native/assets/css/premium-theme.css" rel="stylesheet">
```

## 🎨 Color Palette

```css
--gold-primary: #D4AF37
--gold-light: #F4DF89
--gold-dark: #AA8C2C
--black-primary: #0a0a0a
--black-secondary: #1a1a1a
--black-tertiary: #2a2a2a
```

## ✨ Features

### **1. Animations**
- Fade in
- Slide up
- Pulse effect
- Hover effects
- Smooth transitions

### **2. Sidebar**
- Gradient background
- Gold border
- Hover effects
- Active state highlighting
- Smooth transitions

### **3. Cards**
- Gradient backgrounds
- Gold borders
- Hover effects
- Shadow effects

### **4. Tables**
- Gold header
- Hover row highlighting
- Responsive design

### **5. Forms**
- Dark background inputs
- Gold focus states
- Smooth transitions

### **6. Buttons**
- Gold gradient
- Hover lift effect
- Shadow effects

### **7. Typography**
- Playfair Display (headings)
- Poppins (body text)

## 📋 Updated Pages

### **Completed:**
1. ✅ login.php
2. ✅ pos-tables.php
3. ✅ kds-kitchen.php
4. ✅ kds-bar.php

### **To Update:**
- dashboard.php
- orders.php
- menu.php
- customers.php
- reports.php
- users.php
- settings.php
- modifiers.php

## 🔧 Customization

### **Change Primary Color:**
Edit `premium-theme.css`:
```css
:root {
    --gold-primary: #YOUR_COLOR;
}
```

### **Change Fonts:**
Edit header include:
```html
<link href="https://fonts.googleapis.com/css2?family=YOUR_FONT" rel="stylesheet">
```

### **Add Custom Styles:**
```php
<?php
$custom_css = '/path/to/custom.css';
include __DIR__ . '/../includes/premium-header.php';
?>
```

## 📱 Responsive

Theme sudah responsive untuk:
- Desktop (>768px)
- Tablet (768px)
- Mobile (<768px)

## 🎯 Usage Example

### **Dashboard:**
```php
<?php
$page_title = "Dashboard";
include __DIR__ . '/../includes/premium-header.php';
?>

<div class="page-header">
    <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
</div>

<div class="stats-bar">
    <div class="stat-box">
        <div class="stat-value">150</div>
        <div class="stat-label">Total Orders</div>
    </div>
</div>

<?php include __DIR__ . '/../includes/premium-footer.php'; ?>
```

## ⚡ Performance

- CSS minified for production
- Animations optimized
- Lazy loading for images
- Minimal JavaScript

## 🐛 Browser Support

- Chrome (Latest)
- Firefox (Latest)
- Safari (Latest)
- Edge (Latest)
- Mobile browsers

## 📝 Notes

1. **Always include header/footer** untuk consistent theme
2. **Use utility classes** untuk quick styling
3. **Follow existing patterns** di halaman yang sudah di-update
4. **Test responsive** di berbagai device

## 🎉 Next Steps

1. Update semua halaman dengan premium theme
2. Add loading animations
3. Add page transitions
4. Add dark/light mode toggle
5. Add theme customizer

---

**Created with ❤️ for RestoQwen POS Premium Experience**
