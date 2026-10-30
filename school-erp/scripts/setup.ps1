# School ERP Automated Setup Script
$ErrorActionPreference = "Stop"

Write-Host "=== Setting up School ERP ===" -ForegroundColor Green

# Ensure PHP and Git in PATH
$phpPath = "C:\Users\DELL\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe"
$gitPath = "C:\Program Files\Git\cmd"
if ($env:Path -notlike "*$phpPath*") { $env:Path = "$phpPath;$env:Path" }
if ($env:Path -notlike "*$gitPath*") { $env:Path = "$gitPath;$env:Path" }

$scriptRoot = Split-Path -Parent $MyInvocation.MyCommand.Definition
$webPath = Join-Path (Split-Path -Parent $scriptRoot) "web"
Set-Location $webPath

Write-Host "1. Installing Composer packages..." -ForegroundColor Cyan
composer install --no-interaction

Write-Host "2. Installing NPM dependencies..." -ForegroundColor Cyan
npm install

Write-Host "3. Compiling production frontend assets..." -ForegroundColor Cyan
npm run build

Write-Host "4. Executing database migrations and seeding realistic data..." -ForegroundColor Cyan
php artisan migrate:fresh --seed --force

Write-Host "=== School ERP Setup Completed Successfully! ===" -ForegroundColor Green
Write-Host "Run .\scripts\start.ps1 to start the application server." -ForegroundColor Yellow
