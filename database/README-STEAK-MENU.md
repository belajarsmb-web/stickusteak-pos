# RestoQwen POS - Sample Steak Restaurant Menu

## 📋 Ringkasan

File SQL ini menambahkan **sample menu steak restaurant** lengkap dengan resep dan inventori ke database `posreato`.

## 🥩 Menu Items yang Ditambahkan (10 items)

| No | Menu Item | Harga | Deskripsi |
|----|-----------|-------|-----------|
| 1 | **Classic Sirloin Steak** | Rp 185.000 | 250g sapi sirloin premium dengan saus lada hitam, kentang tumbuk, dan sayuran musiman |
| 2 | **Tenderloin Mignon** | Rp 245.000 | 200g tenderloin sapi pilihan dengan saus mushroom, asparagus, dan baked potato |
| 3 | **Ribeye Deluxe** | Rp 225.000 | 300g ribeye marble dengan garlic butter, grilled vegetables, dan french fries |
| 4 | **Striploin Supreme** | Rp 215.000 | 280g striploin dengan rosemary jus, mashed potato, dan buncis |
| 5 | **T-Bone Special** | Rp 265.000 | 400g T-bone steak dengan saus BBQ, corn on the cob, dan salad |
| 6 | **Wagyu A5 Experience** | Rp 550.000 | 150g Wagyu A5 Jepang dengan truffle sauce, asparagus, dan potato gratin |
| 7 | **Lamb Chop** | Rp 195.000 | 6 pcs kambing chop dengan mint sauce, roasted vegetables, dan couscous |
| 8 | **Grilled Salmon** | Rp 145.000 | 200g salmon fillet dengan lemon butter sauce, nasi, dan sayuran |
| 9 | **Beef Bourguignon** | Rp 165.000 | Daging sapi slow-cooked dengan anggur merah, jamur, dan kentang |
| 10 | **Chicken Cordon Bleu** | Rp 95.000 | Dada ayam dengan ham dan keju, saus mustard, kentang, dan salad |

## 📦 Inventory Items yang Ditambahkan

### Daging & Protein (7 items)
- Daging Sapi Sirloin Premium
- Daging Sapi Tenderloin
- Daging Sapi Ribeye
- Daging Sapi Striploin
- Ikan Salmon Fillet
- Daging Kambing
- Bacon Strip

### Sayuran (1 item)
- Asparagus

### Minuman (1 item)
- Anggur Merah Dry (untuk wine sauce)

## 📝 Resep per Menu

### Classic Sirloin Steak
| Bahan | Jumlah |
|-------|--------|
| Sirloin Premium | 250g |
| Kentang | 200g |
| Wortel | 50g |
| Brokoli | 50g |
| Buncis | 30g |
| Butter | 30g |
| Lada Hitam | 10g |
| Garam | 5g |

### Tenderloin Mignon
| Bahan | Jumlah |
|-------|--------|
| Tenderloin | 200g |
| Jamur | 100g |
| Asparagus | 50g |
| Kentang | 200g |
| Butter | 30g |
| Kaldu Sapi | 100ml |
| Garam | 5g |

### Ribeye Deluxe
| Bahan | Jumlah |
|-------|--------|
| Ribeye | 300g |
| Butter | 40g |
| Bawang Putih | 20g |
| Paprika | 100g |
| Kentang | 150g |
| Garam | 5g |

### Striploin Supreme
| Bahan | Jumlah |
|-------|--------|
| Striploin | 280g |
| Kentang | 200g |
| Buncis | 50g |
| Rosemary | 20g |
| Kaldu Sapi | 100ml |
| Butter | 30g |

### T-Bone Special
| Bahan | Jumlah |
|-------|--------|
| Sirloin (dengan tulang) | 400g |
| Jagung | 1 buah |
| Selada | 100g |
| Tomat | 50g |
| Saus BBQ | 50ml |

### Wagyu A5 Experience
| Bahan | Jumlah |
|-------|--------|
| Wagyu A5 | 150g |
| Asparagus | 50g |
| Kentang | 150g |
| Cream | 30ml |
| Parmesan | 20g |

### Lamb Chop
| Bahan | Jumlah |
|-------|--------|
| Daging Kambing | 300g |
| Paprika | 100g |
| Brokoli | 50g |
| Buncis | 50g |
| Rosemary | 20g |
| Garam | 5g |

### Grilled Salmon
| Bahan | Jumlah |
|-------|--------|
| Salmon Fillet | 200g |
| Butter | 30g |
| Kaldu Ayam | 50ml |
| Beras | 150g |
| Brokoli | 50g |
| Wortel | 50g |

### Beef Bourguignon
| Bahan | Jumlah |
|-------|--------|
| Daging Sapi | 250g |
| Jamur | 100g |
| Anggur Merah | 200ml |
| Kaldu Sapi | 200ml |
| Kentang | 200g |
| Bawang Bombay | 50g |
| Bawang Putih | 10g |

### Chicken Cordon Bleu
| Bahan | Jumlah |
|-------|--------|
| Ayam Breast | 200g |
| Bacon/Ham | 50g |
| Cheddar | 50g |
| Tepung Panir | 50g |
| Telur | 2 butir |
| Kentang | 150g |
| Selada | 50g |

## 🚀 Cara Menggunakan

### Opsi 1: Via Command Line
```bash
C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root posreato < "C:\Project\restoopncode\database\sample-steak-menu-final.sql"
```

### Opsi 2: Via MySQL Workbench / phpMyAdmin
1. Buka database `posreato`
2. Import file `sample-steak-menu-final.sql`

## 📊 Verifikasi Data

```sql
-- Cek menu items yang ditambahkan
SELECT id, name, price, category_id 
FROM menu_items 
WHERE category_id = 1 
ORDER BY id;

-- Cek resep untuk setiap menu
SELECT m.name as menu, i.name as ingredient, ri.quantity, ri.unit 
FROM recipe_ingredients ri 
JOIN menu_items m ON ri.menu_item_id = m.id 
JOIN inventory_items i ON ri.inventory_item_id = i.id 
ORDER BY m.id;

-- Cek total inventory
SELECT COUNT(*) as total_inventory FROM inventory_items;

-- Cek total menu items
SELECT COUNT(*) as total_menu FROM menu_items;
```

## 📝 Catatan

- Semua harga dalam Rupiah (Rp)
- Cost price adalah perkiraan 40% dari harga jual
- Recipe quantities disesuaikan untuk 1 porsi
- Inventory stock awal sudah diset untuk operasional normal

## 🔗 Akses Aplikasi

- **Login**: http://localhost/php-native/pages/login.php
- **Menu Page**: http://localhost/php-native/pages/menu.php
- **Credentials**: `admin` / `admin123`
