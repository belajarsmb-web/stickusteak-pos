# Project Restore from Backup
# Restores project from a selected backup ZIP file

$projectDir = "C:\Project\restoopncode"
$backupDir = "C:\Project\restoopncode-backups"

Write-Host "============================================"
Write-Host "Project Restore from Backup" -ForegroundColor Yellow
Write-Host "============================================"
Write-Host ""
Write-Host "WARNING: This will restore the project from a backup!" -ForegroundColor Red
Write-Host "All current changes will be overwritten." -ForegroundColor Red
Write-Host ""
Write-Host "Press any key to continue or Ctrl+C to cancel..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
Write-Host ""

# Get list of backups
if (!(Test-Path $backupDir)) {
    Write-Host "ERROR: Backup directory not found: $backupDir" -ForegroundColor Red
    Write-Host "Please create a backup first."
    pause
    exit 1
}

$backups = Get-ChildItem -Path $backupDir -Filter "*.zip" | Sort-Object LastWriteTime -Descending

if ($backups.Count -eq 0) {
    Write-Host "ERROR: No backup files found in $backupDir" -ForegroundColor Red
    pause
    exit 1
}

Write-Host "Available backups:"
Write-Host "============================================"
for ($i = 0; $i -lt $backups.Count; $i++) {
    $size = [math]::Round($backups[$i].Length / 1MB, 2)
    $color = if ($i -eq 0) { "Green" } else { "White" }
    Write-Host "  [$($i + 1)] " -NoNewline
    Write-Host "$($backups[$i].Name) ($size MB)" -ForegroundColor $color
}
Write-Host ""

# Prompt for selection
Write-Host "Enter backup number or filename to restore:"
$selection = Read-Host "> "

# Determine selected backup
$selectedBackup = $null
if ([int]::TryParse($selection, [ref]$null)) {
    $index = [int]$selection - 1
    if ($index -ge 0 -and $index -lt $backups.Count) {
        $selectedBackup = $backups[$index]
    }
} else {
    $selectedBackup = Get-ChildItem -Path $backupDir -Filter "*$selection*" | Select-Object -First 1
}

if ($null -eq $selectedBackup) {
    Write-Host ""
    Write-Host "ERROR: Invalid selection or backup not found!" -ForegroundColor Red
    pause
    exit 1
}

Write-Host ""
Write-Host "Selected backup: $($selectedBackup.Name)" -ForegroundColor Green
Write-Host ""
Write-Host "Restoring to: $projectDir" -ForegroundColor Yellow
Write-Host ""
Write-Host "Type YES to confirm restore: " -NoNewline
$confirm = Read-Host

if ($confirm -ne "YES") {
    Write-Host ""
    Write-Host "Restore cancelled." -ForegroundColor Yellow
    pause
    exit 0
}

Write-Host ""
Write-Host "Extracting backup..." -ForegroundColor Cyan

try {
    # Extract backup
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    
    # Create project directory if not exists
    if (!(Test-Path $projectDir)) {
        New-Item -ItemType Directory -Path $projectDir | Out-Null
    }
    
    # Extract files
    [System.IO.Compression.ZipFile]::ExtractToDirectory($selectedBackup.FullName, $projectDir, $true)
    
    Write-Host ""
    Write-Host "============================================" -ForegroundColor Green
    Write-Host "Restore completed successfully!" -ForegroundColor Green
    Write-Host "============================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Please restart your web server if running." -ForegroundColor Yellow
    Write-Host ""
} catch {
    Write-Host ""
    Write-Host "============================================" -ForegroundColor Red
    Write-Host "ERROR: Restore failed!" -ForegroundColor Red
    Write-Host "============================================" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please check:" -ForegroundColor Yellow
    Write-Host "  1. Close any open files in the project" -ForegroundColor Yellow
    Write-Host "  2. Stop web server (Apache/Nginx)" -ForegroundColor Yellow
    Write-Host "  3. Close IDE/text editor" -ForegroundColor Yellow
    Write-Host "  4. Run as Administrator if needed" -ForegroundColor Yellow
    Write-Host ""
}

Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
