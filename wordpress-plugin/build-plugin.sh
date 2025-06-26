#!/bin/bash
# Build script for Estate Planning Manager WordPress Plugin
# This script creates a distributable ZIP file for WordPress installation

echo "Building Estate Planning Manager Plugin..."
echo

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "ERROR: PHP is not installed or not in PATH"
    echo "Please install PHP and try again"
    exit 1
fi

# Make sure we're in the right directory
if [ ! -f "create-plugin-package.php" ]; then
    echo "ERROR: create-plugin-package.php not found"
    echo "Please run this script from the wordpress-plugin directory"
    exit 1
fi

# Run the packaging script
echo "Running packaging script..."
php create-plugin-package.php

# Check if the build was successful
if [ -f "dist/estate-planning-manager.zip" ]; then
    echo
    echo "SUCCESS: Plugin package created successfully!"
    echo
    echo "File location: dist/estate-planning-manager.zip"
    echo
    echo "Installation Instructions:"
    echo "1. Go to WordPress Admin > Plugins > Add New"
    echo "2. Click 'Upload Plugin'"
    echo "3. Choose the estate-planning-manager.zip file"
    echo "4. Click 'Install Now' and then 'Activate Plugin'"
    echo
    
    # Show file size
    file_size=$(du -h "dist/estate-planning-manager.zip" | cut -f1)
    echo "Package size: $file_size"
    echo
else
    echo
    echo "ERROR: Plugin package creation failed"
    echo "Please check the error messages above"
    echo
    exit 1
fi
