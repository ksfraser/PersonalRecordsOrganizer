<?php
/**
 * Unified admin page for managing bank selectors (locations, names, manage banks)
 */
require_once dirname(__DIR__) . '/includes/epm-slugs.php';

class EPM_BankSelectorsAdmin {
    public static function render_admin_page() {
        echo '<div class="wrap"><h1>Bank Selectors</h1>';
        echo '<h2 class="nav-tab-wrapper">';
        $tabs = [
            EPM_BANK_LOCATIONS_TAB => 'Bank Locations',
            EPM_BANK_NAMES_TAB => 'Bank Names',
            EPM_MANAGE_BANKS_TAB => 'Manage Banks',
        ];
        $active = isset($_GET['tab']) ? $_GET['tab'] : EPM_BANK_LOCATIONS_TAB;
        foreach ($tabs as $tab => $label) {
            $class = ($active === $tab) ? ' nav-tab-active' : '';
            echo '<a href="?page=' . EPM_BANK_SELECTORS_SLUG . '&tab=' . esc_attr($tab) . '" class="nav-tab' . $class . '">' . esc_html($label) . '</a>';
        }
        echo '</h2>';
        echo '<div style="margin-top:30px;">';
        switch ($active) {
            case 'locations':
                if (class_exists('EPM_BankLocationTypesAdmin')) {
                    EPM_BankLocationTypesAdmin::render_admin_page();
                } else {
                    echo '<p>Bank Locations admin not found.</p>';
                }
                break;
            case 'names':
                if (class_exists('EPM_BankNamesAdmin')) {
                    EPM_BankNamesAdmin::render_admin_page();
                } else {
                    echo '<p>Bank Names admin not found.</p>';
                }
                break;
            case 'manage':
                // Placeholder for future manage banks UI
                echo '<p>Manage Banks UI coming soon.</p>';
                break;
        }
        echo '</div></div>';
    }
}
