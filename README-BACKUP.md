# ️ Auto-Backup System

## Quick Reference

### Before Making Changes
```bash
backup.bat
```

### After Making Changes
```bash
backup.bat  # Create another backup to compare
```

### Restore if Needed
```bash
restore.bat
```

## Backup Files Created

| File | Description |
|------|-------------|
| `backup.bat` | Main backup script (double-click this) |
| `restore.bat` | Main restore script (double-click this) |
| `create-backup.ps1` | PowerShell backup logic |
| `restore-backup.ps1` | PowerShell restore logic |

## Backup Location

```
📁 C:\Project\restoopncode-backups\
    ├── 📄 BACKUP_20260321-051920_restoopncode.zip
    ├── 📄 BACKUP_20260321-051835_restoopncode.zip
    └── 📄 BACKUP_20260321-051752_restoopncode.zip
```

## Usage Examples

### Example 1: Before Adding New Feature
```bash
# 1. Create backup
backup.bat

# 2. Note the backup filename
# BACKUP_20260321-051920_restoopncode.zip

# 3. Implement feature...
# 4. Test...
# 5. If something breaks, restore:
restore.bat
# Enter: 1 (or filename)
```

### Example 2: Before Database Migration
```bash
# 1. Create file backup
backup.bat

# 2. Create database backup
"C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe" -u root posreato > "C:\Project\restoopncode-backups\pre-migration.sql"

# 3. Run migration...

# 4. If migration fails, restore both:
restore.bat  # Restore files
# Import database backup in phpMyAdmin or via CLI
```

### Example 3: Daily Backup Routine
```bash
# Morning (before work)
backup.bat

# After lunch (before afternoon work)
backup.bat

# Evening (end of day)
backup.bat
```

## Tips

💡 **Name your backups**: Add notes in a text file if needed
💡 **Multiple backups**: Create before/after major changes
💡 **Test restores**: Periodically test that backups work
💡 **Off-site copy**: Copy important backups to cloud/external drive

---

**Created:** 2026-03-21  
**For:** RestoOPNCode POS System
