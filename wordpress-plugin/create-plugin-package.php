<?php
/**
 * Script to create a distributable WordPress plugin package
 * Run this script to create a ZIP file ready for WordPress installation
 */

// Prevent direct access
if (php_sapi_name() !== 'cli') {
    die('This script must be run from the command line.');
}

class PluginPackager {
    
    private $pluginDir = 'estate-planning-manager';
    private $outputDir = 'dist';
    private $zipName = 'estate-planning-manager.zip';
    
    public function createPackage() {
        echo "Creating Estate Planning Manager plugin package...\n";
        
        try {
            $this->createOutputDirectory();
            $this->copyPluginFiles();
            $this->createZipFile();
            $this->cleanup();
            
            echo "Plugin package created successfully: {$this->outputDir}/{$this->zipName}\n";
            echo "This ZIP file can be uploaded to WordPress via Plugins > Add New > Upload Plugin\n";
            
        } catch (Exception $e) {
            echo "Error creating package: " . $e->getMessage() . "\n";
            return false;
        }
        
        return true;
    }
    
    private function createOutputDirectory() {
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }
    
    private function copyPluginFiles() {
        echo "Copying plugin files...\n";
        
        $sourceDir = $this->pluginDir;
        $targetDir = $this->outputDir . '/' . $this->pluginDir;
        
        if (!is_dir($sourceDir)) {
            throw new Exception("Plugin directory not found: $sourceDir");
        }
        
        // Remove existing target directory
        if (is_dir($targetDir)) {
            $this->removeDirectory($targetDir);
        }
        
        // Copy plugin files
        $this->copyDirectory($sourceDir, $targetDir);
        
        // Remove development files that shouldn't be in distribution
        $this->removeDevelopmentFiles($targetDir);
    }
    
    private function copyDirectory($source, $destination) {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                copy($item, $target);
            }
        }
    }
    
    private function removeDevelopmentFiles($targetDir) {
        echo "Removing development files...\n";
        
        $filesToRemove = array(
            'composer.json',
            'composer.lock',
            'phpunit.xml',
            'run-tests.php',
            'TESTING.md',
            '.gitignore',
            '.git',
            'node_modules',
            'tests',
            'vendor'
        );
        
        foreach ($filesToRemove as $file) {
            $filePath = $targetDir . '/' . $file;
            if (file_exists($filePath)) {
                if (is_dir($filePath)) {
                    $this->removeDirectory($filePath);
                } else {
                    unlink($filePath);
                }
            }
        }
    }
    
    private function removeDirectory($dir) {
        if (!is_dir($dir)) {
            return;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        
        rmdir($dir);
    }
    
    private function createZipFile() {
        echo "Creating ZIP file...\n";
        
        $zipPath = $this->outputDir . '/' . $this->zipName;
        
        // Remove existing ZIP file
        if (file_exists($zipPath)) {
            unlink($zipPath);
        }
        
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new Exception("Cannot create ZIP file: $zipPath");
        }
        
        $sourceDir = $this->outputDir . '/' . $this->pluginDir;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            $filePath = $file->getRealPath();
            $relativePath = $this->pluginDir . '/' . substr($filePath, strlen($sourceDir) + 1);
            
            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($filePath, $relativePath);
            }
        }
        
        $zip->close();
    }
    
    private function cleanup() {
        echo "Cleaning up temporary files...\n";
        
        $tempDir = $this->outputDir . '/' . $this->pluginDir;
        if (is_dir($tempDir)) {
            $this->removeDirectory($tempDir);
        }
    }
}

// Run the packager
$packager = new PluginPackager();
$packager->createPackage();
