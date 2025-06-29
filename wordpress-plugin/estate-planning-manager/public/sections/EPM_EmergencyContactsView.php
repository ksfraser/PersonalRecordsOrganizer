<?php
// View class for Emergency Contacts section
require_once dirname(__DIR__, 2) . '/includes/tables/PersonTable.php';

class EPM_EmergencyContactsView {
    public static function render($client_id, $readonly = false) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_emergency_contacts';
        $records = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id));
        echo '<div class="epm-data-section" data-section="emergency_contacts">';
        echo '<h3>Emergency Contacts</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No emergency contacts recorded.</p>';
        } else {
            echo '<div class="epm-data-grid">';
            foreach ($records as $row) {
                echo '<div class="epm-data-item">';
                echo '<label>Name:</label> <span>' . esc_html($row->contact_name) . '</span><br>';
                echo '<label>Relationship:</label> <span>' . esc_html($row->relationship) . '</span><br>';
                echo '<label>Phone:</label> <span>' . esc_html($row->phone) . '</span><br>';
                echo '<label>Email:</label> <span>' . esc_html($row->email) . '</span><br>';
                echo '<label>Address:</label> <span>' . esc_html($row->address) . '</span><br>';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
    }
}
