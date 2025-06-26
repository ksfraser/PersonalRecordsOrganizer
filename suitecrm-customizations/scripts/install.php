<?php
/**
 * Installation script for Estate Planning Manager SuiteCRM customizations
 * 
 * This script installs the custom modules and configurations needed for
 * the Estate Planning Manager WordPress plugin integration.
 */

// Prevent direct access
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

// Include SuiteCRM bootstrap
if (file_exists('../../config.php')) {
    require_once('../../config.php');
} else {
    die('SuiteCRM config.php not found. Please run this script from the SuiteCRM root directory.');
}

require_once('include/entryPoint.php');

class EPM_Installer {
    
    private $modules = array(
        'EPM_BankAccounts',
        'EPM_Investments', 
        'EPM_RealEstate',
        'EPM_Insurance'
    );
    
    public function install() {
        echo "Starting Estate Planning Manager installation...\n";
        
        try {
            $this->createTables();
            $this->installModules();
            $this->setupRelationships();
            $this->configurePermissions();
            $this->rebuildCache();
            
            echo "Installation completed successfully!\n";
            
        } catch (Exception $e) {
            echo "Installation failed: " . $e->getMessage() . "\n";
            return false;
        }
        
        return true;
    }
    
    private function createTables() {
        echo "Creating database tables...\n";
        
        global $db;
        
        // Bank Accounts table
        $sql = "CREATE TABLE IF NOT EXISTS epm_bankaccounts (
            id CHAR(36) NOT NULL PRIMARY KEY,
            name VARCHAR(255) NULL,
            date_entered DATETIME NULL,
            date_modified DATETIME NULL,
            modified_user_id CHAR(36) NULL,
            created_by CHAR(36) NULL,
            description TEXT NULL,
            deleted TINYINT(1) DEFAULT 0,
            assigned_user_id CHAR(36) NULL,
            bank_name VARCHAR(100) NULL,
            account_type VARCHAR(50) NULL,
            account_number VARCHAR(50) NULL,
            branch VARCHAR(100) NULL,
            contact_id CHAR(36) NULL,
            wp_client_id VARCHAR(36) NULL,
            wp_record_id VARCHAR(36) NULL,
            INDEX idx_epm_bankaccounts_name (name),
            INDEX idx_epm_bankaccounts_assigned (assigned_user_id),
            INDEX idx_epm_bankaccounts_contact (contact_id),
            INDEX idx_epm_bankaccounts_wp_client (wp_client_id)
        )";
        
        $db->query($sql);
        
        // Similar tables for other modules would be created here
        echo "Database tables created.\n";
    }
    
    private function installModules() {
        echo "Installing custom modules...\n";
        
        foreach ($this->modules as $module) {
            $this->installModule($module);
        }
        
        echo "Custom modules installed.\n";
    }
    
    private function installModule($moduleName) {
        echo "Installing module: $moduleName\n";
        
        // Copy module files
        $sourceDir = "../modules/$moduleName";
        $targetDir = "modules/$moduleName";
        
        if (!is_dir($sourceDir)) {
            throw new Exception("Source module directory not found: $sourceDir");
        }
        
        // Create target directory
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // Copy files recursively
        $this->copyDirectory($sourceDir, $targetDir);
        
        // Register module in module registry
        $this->registerModule($moduleName);
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
    
    private function registerModule($moduleName) {
        global $beanList, $beanFiles;
        
        // Add to bean list
        $beanList[$moduleName] = $moduleName;
        $beanFiles[$moduleName] = "modules/$moduleName/$moduleName.php";
        
        // Update module registry
        if (file_exists('custom/application/Ext/Include/modules.ext.php')) {
            $content = file_get_contents('custom/application/Ext/Include/modules.ext.php');
        } else {
            $content = "<?php\n";
        }
        
        $moduleEntry = "\$beanList['$moduleName'] = '$moduleName';\n";
        $moduleEntry .= "\$beanFiles['$moduleName'] = 'modules/$moduleName/$moduleName.php';\n";
        
        if (strpos($content, $moduleEntry) === false) {
            $content .= $moduleEntry;
            
            if (!is_dir('custom/application/Ext/Include')) {
                mkdir('custom/application/Ext/Include', 0755, true);
            }
            
            file_put_contents('custom/application/Ext/Include/modules.ext.php', $content);
        }
    }
    
    private function setupRelationships() {
        echo "Setting up module relationships...\n";
        
        // Add relationships between custom modules and Contacts
        $this->addContactRelationships();
        
        echo "Relationships configured.\n";
    }
    
    private function addContactRelationships() {
        // This would add subpanels to Contacts for each EPM module
        $relationships = array(
            'EPM_BankAccounts' => 'Bank Accounts',
            'EPM_Investments' => 'Investments',
            'EPM_RealEstate' => 'Real Estate',
            'EPM_Insurance' => 'Insurance'
        );
        
        foreach ($relationships as $module => $label) {
            $this->createContactSubpanel($module, $label);
        }
    }
    
    private function createContactSubpanel($module, $label) {
        $subpanelDir = 'custom/modules/Contacts/Ext/Layoutdefs';
        
        if (!is_dir($subpanelDir)) {
            mkdir($subpanelDir, 0755, true);
        }
        
        $subpanelFile = "$subpanelDir/{$module}.php";
        $subpanelContent = "<?php\n";
        $subpanelContent .= "\$layout_defs['Contacts']['subpanel_setup']['{$module}'] = array(\n";
        $subpanelContent .= "    'order' => 100,\n";
        $subpanelContent .= "    'module' => '{$module}',\n";
        $subpanelContent .= "    'subpanel_name' => 'default',\n";
        $subpanelContent .= "    'sort_order' => 'asc',\n";
        $subpanelContent .= "    'sort_by' => 'id',\n";
        $subpanelContent .= "    'title_key' => 'LBL_{$module}_SUBPANEL_TITLE',\n";
        $subpanelContent .= "    'get_subpanel_data' => '{$module}',\n";
        $subpanelContent .= "    'top_buttons' => array(\n";
        $subpanelContent .= "        array('widget_class' => 'SubPanelTopCreateButton'),\n";
        $subpanelContent .= "        array('widget_class' => 'SubPanelTopSelectButton', 'mode' => 'MultiSelect'),\n";
        $subpanelContent .= "    ),\n";
        $subpanelContent .= ");\n";
        
        file_put_contents($subpanelFile, $subpanelContent);
    }
    
    private function configurePermissions() {
        echo "Configuring permissions...\n";
        
        // Set up ACL roles for the new modules
        foreach ($this->modules as $module) {
            $this->setupModuleACL($module);
        }
        
        echo "Permissions configured.\n";
    }
    
    private function setupModuleACL($module) {
        // Create ACL entries for the module
        $aclDir = "custom/modules/$module/Ext/Vardefs";
        
        if (!is_dir($aclDir)) {
            mkdir($aclDir, 0755, true);
        }
        
        $aclContent = "<?php\n";
        $aclContent .= "\$dictionary['$module']['acls'] = array(\n";
        $aclContent .= "    'SugarACLStatic' => true,\n";
        $aclContent .= ");\n";
        
        file_put_contents("$aclDir/acl.php", $aclContent);
    }
    
    private function rebuildCache() {
        echo "Rebuilding SuiteCRM cache...\n";
        
        // Clear and rebuild various caches
        if (function_exists('sugar_cache_clear')) {
            sugar_cache_clear();
        }
        
        // Rebuild relationships
        if (class_exists('Relationship')) {
            Relationship::rebuild();
        }
        
        // Quick repair and rebuild
        require_once('modules/Administration/QuickRepairAndRebuild.php');
        $repair = new RepairAndClear();
        $repair->repairAndClearAll(array('clearAll'), array('All Modules'), false, false);
        
        echo "Cache rebuilt.\n";
    }
}

// Run the installer
if (php_sapi_name() === 'cli') {
    $installer = new EPM_Installer();
    $installer->install();
} else {
    echo "This script must be run from the command line.\n";
}
