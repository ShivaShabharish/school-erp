# School ERP Startup Script
Write-Host "==========================================================" -ForegroundColor Green
Write-Host "   Starting Test Subject Higher Secondary School ERP      " -ForegroundColor Green
Write-Host "==========================================================" -ForegroundColor Green

# Ensure PHP and Git in PATH
$phpPath = "C:\Users\DELL\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe"
$gitPath = "C:\Program Files\Git\cmd"
if ($env:Path -notlike "*$phpPath*") { $env:Path = "$phpPath;$env:Path" }
if ($env:Path -notlike "*$gitPath*") { $env:Path = "$gitPath;$env:Path" }

$scriptRoot = Split-Path -Parent $MyInvocation.MyCommand.Definition
$webPath = Join-Path (Split-Path -Parent $scriptRoot) "web"
Set-Location $webPath

# Start PHP Artisan Serve in background
$serveProcess = Start-Process -FilePath "php" -ArgumentList "artisan", "serve", "--host=127.0.0.1", "--port=8000" -PassThru -WindowStyle Hidden
Write-Host "[✓] Laravel Web Server running in background (PID: $($serveProcess.Id))" -ForegroundColor Cyan

# Start Vite dev server in background
$viteProcess = Start-Process -FilePath "cmd.exe" -ArgumentList "/c", "npm run dev" -PassThru -WindowStyle Hidden
Write-Host "[✓] Vite Assets Server running in background (PID: $($viteProcess.Id))" -ForegroundColor Cyan

Start-Sleep -Seconds 2

Write-Host "`n----------------------------------------------------------" -ForegroundColor Yellow
Write-Host "  URL: http://127.0.0.1:8000" -ForegroundColor Yellow
Write-Host "  Health Check: http://127.0.0.1:8000/health" -ForegroundColor Yellow
Write-Host "----------------------------------------------------------" -ForegroundColor Yellow
Write-Host "DEMO LOGINS (Password for all: Password@123):" -ForegroundColor White
Write-Host " - Chairman:   chairman@school.test" -ForegroundColor White
Write-Host " - Principal:  principal@school.test" -ForegroundColor White
Write-Host " - Accountant: accountant@school.test" -ForegroundColor White
Write-Host " - Teacher:    teacher@school.test" -ForegroundColor White
Write-Host " - Parent:     parent@school.test" -ForegroundColor White
Write-Host "----------------------------------------------------------`n" -ForegroundColor Yellow
