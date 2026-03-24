# Project Backup Creator
# Creates timestamped ZIP backup of the project

$projectDir = "C:\Project\restoopncode"
$backupDir = "C:\Project\restoopncode-backups"

# Create backup directory if not exists
if (!(Test-Path $backupDir)) {
    New-Item -ItemType Directory -Path $backupDir | Out-Null
}

# Generate timestamp
$timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
$backupFile = "$backupDir\BACKUP_$timestamp`_restoopncode.zip"

Write-Host "============================================"
Write-Host "Project Backup Creator"
Write-Host "============================================"
Write-Host ""
Write-Host "Backup timestamp: $timestamp"
Write-Host ""
Write-Host "Creating backup..."
Write-Host "Source: $projectDir"
Write-Host "Destination: $backupFile"
Write-Host ""

# Get all files excluding common temp/ignore patterns
$excludePatterns = @('*.log', '*.tmp', '*.bak', '*.zip')
$excludeFolders = @('node_modules', '.git', '.vscode', 'backups', 'restoopncode-backups')

$files = Get-ChildItem -Path $projectDir -Recurse -File | Where-Object {
    $exclude = $false
    
    # Check file extensions
    foreach ($pattern in $excludePatterns) {
        if ($_.Name -like $pattern) {
            $exclude = $true
            break
        }
    }
    
    # Check folder paths
    if (!$exclude) {
        foreach ($folder in $excludeFolders) {
            if ($_.FullName -like "*\$folder\*") {
                $exclude = $true
                break
            }
        }
    }
    
    return !$exclude
}

# Compress files
if ($files.Count -gt 0) {
    # Create temporary file list
    $tempFile = [System.IO.Path]::GetTempFileName()
    $files | Select-Object -ExpandProperty FullName | Out-File -FilePath $tempFile -Encoding UTF8
    
    # Compress using .NET compression
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    $compression = [System.IO.Compression.CompressionLevel]::Optimal
    
    try {
        [System.IO.Compression.ZipFile]::CreateFromDirectory($projectDir, $backupFile, $compression, $false)
        
        # Get file size
        $fileSize = (Get-Item $backupFile).Length / 1MB
        
        Write-Host ""
        Write-Host "============================================" -ForegroundColor Green
        Write-Host "Backup completed successfully!" -ForegroundColor Green
        Write-Host "============================================" -ForegroundColor Green
        Write-Host "Backup file: $backupFile"
        Write-Host "File size: $([math]::Round($fileSize, 2)) MB"
        Write-Host ""
        
        # List recent backups
        Write-Host "Recent backups (last 5):"
        Get-ChildItem -Path $backupDir -Filter "*.zip" | Sort-Object LastWriteTime -Descending | Select-Object -First 5 | ForEach-Object {
            $size = [math]::Round($_.Length / 1MB, 2)
            Write-Host "  - $($_.Name) ($size MB)"
        }
        Write-Host ""
    } catch {
        Write-Host ""
        Write-Host "============================================" -ForegroundColor Red
        Write-Host "ERROR: Backup failed!" -ForegroundColor Red
        Write-Host "============================================" -ForegroundColor Red
        Write-Host "Error: $($_.Exception.Message)"
        Write-Host ""
    } finally {
        # Clean up temp file
        if (Test-Path $tempFile) {
            Remove-Item $tempFile -Force
        }
    }
} else {
    Write-Host ""
    Write-Host "============================================" -ForegroundColor Yellow
    Write-Host "WARNING: No files found to backup!" -ForegroundColor Yellow
    Write-Host "============================================" -ForegroundColor Yellow
    Write-Host ""
}

Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
