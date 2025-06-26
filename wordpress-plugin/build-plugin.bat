@echo off
REM Build script for Estate Planning Manager WordPress Plugin
REM This script creates a distributable ZIP file for WordPress installation

echo Building Estate Planning Manager Plugin...
echo.

REM Check if PHP is available
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install PHP and try again
    pause
    exit /b 1
)

REM Change to the script directory
cd /d "%~dp0"

REM Run the packaging script
echo Running packaging script...
php create-plugin-package.php

REM Check if the build was successful
if exist "dist\estate-planning-manager.zip" (
    echo.
    echo SUCCESS: Plugin package created successfully!
    echo.
    echo File location: dist\estate-planning-manager.zip
    echo.
    echo Installation Instructions:
    echo 1. Go to WordPress Admin ^> Plugins ^> Add New
    echo 2. Click "Upload Plugin"
    echo 3. Choose the estate-planning-manager.zip file
    echo 4. Click "Install Now" and then "Activate Plugin"
    echo.
) else (
    echo.
    echo ERROR: Plugin package creation failed
    echo Please check the error messages above
    echo.
)

pause
