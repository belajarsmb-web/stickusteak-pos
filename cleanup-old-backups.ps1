# Backup Cleanup Script
# Deletes backups older than specified days

$backupDir = "C:\Project\restoopncode-backups"
$daysToKeep = 30  # Change this to keep backups for different duration

Write-Host "============================================"
Write-Host "Backup Cleanup Script" -ForegroundColor Cyan
Write-Host "============================================"
Write-Host ""
Write-Host "Backup directory: $backupDir"
Write-Host "Keeping backups for: $daysToKeep days"
Write-Host ""

if (!(Test-Path $backupDir)) {
    Write-Host "ERROR: Backup directory not found!" -ForegroundColor Red
    pause
    exit 1
}

# Get old backups
$cutoffDate = (Get-Date).AddDays(-$daysToKeep)
$oldBackups = Get-ChildItem -Path $backupDir -Filter "*.zip" | 
    Where-Object { $_.LastWriteTime -lt $cutoffDate }

if ($oldBackups.Count -eq 0) {
    Write-Host "No old backups found to delete." -ForegroundColor Green
    Write-Host "All backups are newer than $daysToKeep days."
} else {
    Write-Host "Found $($oldBackups.Count) backup(s) older than $daysToKeep days:" -ForegroundColor Yellow
    Write-Host ""
    
    $totalSize = 0
    foreach ($backup in $oldBackups) {
        $size = $backup.Length / 1MB
        $totalSize += $size
        Write-Host "  - $($backup.Name) ([math]::Round($size, 2) MB) - $($backup.LastWriteTime)"
    }
    
    Write-Host ""
    Write-Host "Total space to be freed: $([math]::Round($totalSize, 2)) MB" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Type YES to delete these old backups: " -NoNewline
    $confirm = Read-Host
    
    if ($confirm -eq "YES") {
        Write-Host ""
        Write-Host "Deleting old backups..." -ForegroundColor Cyan
        
        foreach ($backup in $oldBackups) {
            Remove-Item $backup.FullName -Force
            Write-Host "  Deleted: $($backup.Name)" -ForegroundColor Green
        }
        
        Write-Host ""
        Write-Host "============================================" -ForegroundColor Green
        Write-Host "Cleanup completed successfully!" -ForegroundColor Green
        Write-Host "============================================" -ForegroundColor Green
        Write-Host "Freed: $([math]::Round($totalSize, 2)) MB" -ForegroundColor Green
        Write-Host ""
    } else {
        Write-Host ""
        Write-Host "Cleanup cancelled." -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "Remaining backups:"
Get-ChildItem -Path $backupDir -Filter "*.zip" | 
    Sort-Object LastWriteTime -Descending | 
    ForEach-Object {
        $size = [math]::Round($_.Length / 1MB, 2)
        Write-Host "  - $($_.Name) ($size MB) - $($_.LastWriteTime)"
    }

Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
