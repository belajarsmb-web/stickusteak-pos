# 🔄 Backup & Restore Guide - RestoOPNCode Project

## 📦 Quick Start

### Create Backup (BEFORE making changes)
```bash
# Double-click or run:
backup.bat
```

### Restore from Backup
```bash
# Double-click or run:
restore.bat
```

## 📁 Available Scripts

| Script | Purpose | Platform |
|--------|---------|----------|
| `backup.bat` | Create new backup with timestamp | Windows (PowerShell) |
| `restore.bat` | Restore from any existing backup | Windows (PowerShell) |
| `create-backup.ps1` | PowerShell backup script | PowerShell |
| `restore-backup.ps1` | PowerShell restore script | PowerShell |

## 📍 Backup Location

All backups are stored in: 
```
C:\Project\restoopncode-backups\
```

## 📝 Backup Filename Format

```
BACKUP_YYYYMMDD-HHMMSS_restoopncode.zip
```

**Example:** `BACKUP_20260321-051920_restoopncode.zip`
- Date: 2026-03-21
- Time: 05:19:20

## ⚠️ Excluded Files (Auto)

Backup automatically excludes these files/folders:
- `*.log` - Log files
- `*.tmp` - Temporary files  
- `*.bak` - Backup files
- `*.zip` - Existing ZIP archives
- `node_modules/` - Node dependencies
- `.git/` - Git repository
- `.vscode/` - VS Code settings
- `backups/` - Backup folders
- `restoopncode-backups/` - Backup directory

## 📋 Step-by-Step: Create Backup

1. **Close unnecessary applications** (optional but recommended)
2. **Run** `backup.bat`
3. Wait for compression to complete
4. Note the backup filename shown
5. ✅ Backup created successfully!

**Example Output:**
```
============================================
Backup completed successfully!
============================================
Backup file: C:\Project\restoopncode-backups\BACKUP_20260321-051920_restoopncode.zip
File size: 373.42 MB

Recent backups (last 5):
  - BACKUP_20260321-051920_restoopncode.zip (373.42 MB)
```

## 📋 Step-by-Step: Restore from Backup

1. **Stop web server** (Apache/Nginx/Laragon)
2. **Close IDE** (VS Code, PhpStorm, etc.)
3. **Run** `restore.bat`
4. **Select backup** from the list (enter number or filename)
5. **Type YES** to confirm
6. Wait for extraction to complete
7. **Restart web server**
8. ✅ Restore completed!

**Example Output:**
```
Available backups:
============================================
  [1] BACKUP_20260321-051920_restoopncode.zip (373.42 MB)
  [2] BACKUP_20260321-051835_restoopncode.zip (369.91 MB)

Enter backup number or filename to restore:
> 1

Selected backup: BACKUP_20260321-051920_restoopncode.zip

Type YES to confirm restore: YES

Extracting backup...
============================================
Restore completed successfully!
============================================
```

## 🗓️ When to Create Backup

Create a backup **BEFORE**:

- [ ] **Database changes** (schema updates, migrations)
- [ ] **Core file modifications** (POS, orders, payments)
- [ ] **Feature implementations** (new modules, integrations)
- [ ] **Configuration changes** (database.php, settings)
- [ ] **Security updates** (patches, permission changes)
- [ ] **Plugin/extension installations**
- [ ] **Theme/template modifications**
- [ ] **API integration changes**

## 🎯 Backup Best Practices

### 1. **Before Each Work Session**
```bash
backup.bat
```

### 2. **Before Major Changes**
```bash
# Create backup with descriptive note in filename (optional)
backup.bat
# Then make your changes
```

### 3. **After Completing Features**
```bash
# Create milestone backup
backup.bat
```

## 🗑️ Backup Rotation (Cleanup Old Backups)

To save disk space, delete backups older than 30 days:

**PowerShell:**
```powershell
Get-ChildItem "C:\Project\restoopncode-backups\*.zip" | 
    Where-Object { $_.LastWriteTime -lt (Get-Date).AddDays(-30) } | 
    Remove-Item -Force
```

**Batch:**
```bash
forfiles /p "C:\Project\restoopncode-backups" /s /m *.zip /d -30 /c "cmd /c del @path"
```

## 💾 Database Backup (IMPORTANT!)

File backup doesn't include database! Create database backups separately:

### Manual Database Backup
```bash
"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe" -u root posreato > "C:\Project\restoopncode-backups\BACKUP_%DATE:~-4,4%%DATE:~3,2%%DATE:~0,2%_database.sql"
```

### PowerShell Database Backup
```powershell
& "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe" -u root posreato | Out-File "C:\Project\restoopncode-backups\BACKUP_$(Get-Date -Format 'yyyyMMdd')_database.sql"
```

## ✅ Pre-Change Checklist

Before making any significant changes:

- [ ] **1. Create file backup** (`backup.bat`)
- [ ] **2. Create database backup** (if applicable)
- [ ] **3. Document planned changes** (what, why, how)
- [ ] **4. Test current functionality** (verify working state)
- [ ] **5. Note backup filename** (for easy restore)
- [ ] **6. Inform team members** (if working in team)
- [ ] **7. Stop web server** (if doing major changes)

## 🚨 Emergency Restore

If something goes wrong:

1. **Stay calm** 🧘
2. **Stop all services** (web server, database)
3. **Run** `restore.bat`
4. **Select most recent backup** before the issue
5. **Confirm restore**
6. **Restart services**
7. **Test functionality**

## 📊 Backup Size Reference

| Backup Type | Approximate Size | Frequency |
|-------------|-----------------|-----------|
| Full project (with images) | 350-400 MB | Daily/Before changes |
| Database only | 5-50 MB | Hourly/Daily |
| Code only (no uploads) | 50-100 MB | Per feature |

## 🔧 Troubleshooting

### "Access Denied" Error
- Close all files in the project
- Stop web server (Apache/Nginx)
- Close IDE/text editor
- Run as Administrator

### "File in Use" Error
- Check for running processes accessing project files
- Use Resource Monitor to find file handles
- Restart computer if needed

### "Disk Space Full" Error
- Delete old backups (see Backup Rotation)
- Move backups to external drive
- Exclude large folders (uploads, logs)

### Backup Too Slow
- Exclude unnecessary folders
- Use SSD for faster I/O
- Close other applications

## 📞 Support

If backup/restore fails repeatedly:
1. Check available disk space (minimum 1GB free)
2. Verify PowerShell execution policy
3. Run as Administrator
4. Check Windows Event Viewer for errors
5. Try manual ZIP/restore as fallback

---

**Last Updated:** 2026-03-21  
**Project:** RestoOPNCode POS System  
**Version:** 1.0

**Remember:** Backup early, backup often! 💾✨
