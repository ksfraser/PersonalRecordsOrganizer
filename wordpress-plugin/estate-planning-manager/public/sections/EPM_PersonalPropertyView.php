<?php
// View class for Personal Property section
require_once dirname(__DIR__, 2) . '/includes/tables/PersonalPropertyTable.php';
require_once dirname(__DIR__, 2) . '/includes/tables/PersonTable.php';

class EPM_PersonalPropertyView {
    public static function render($client_id, $readonly = false) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_personal_property';
        $records = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id));
        echo '<div class="epm-data-section" data-section="personal_property">';
        echo '<h3>Personal Property</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No personal property recorded.</p>';
        } else {
            echo '<div class="epm-data-grid">';
            foreach ($records as $row) {
                $owner = self::get_person_label($row->owner_person_id);
                echo '<div class="epm-data-item">';
                echo '<label>Type:</label> <span>' . esc_html($row->property_type) . '</span><br>';
                echo '<label>Item Type:</label> <span>' . esc_html($row->item_type) . '</span><br>';
                echo '<label>Vehicle Model:</label> <span>' . esc_html($row->vehicle_model) . '</span><br>';
                echo '<label>Own/Lease:</label> <span>' . esc_html($row->own_or_lease) . '</span><br>';
                echo '<label>Owner:</label> <span>' . esc_html($owner) . '</span><br>';
                echo '<label>Registration Location:</label> <span>' . esc_html($row->registration_location) . '</span><br>';
                echo '<label>Insurance Policy Location:</label> <span>' . esc_html($row->insurance_policy_location) . '</span><br>';
                echo '<label>Bill of Sale Location:</label> <span>' . esc_html($row->bill_of_sale_location) . '</span><br>';
                echo '<label>Keys Location:</label> <span>' . esc_html($row->keys_location) . '</span><br>';
                echo '<label>Contents List Location:</label> <span>' . esc_html($row->contents_list_location) . '</span><br>';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
    }
    private static function get_person_label($person_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_persons';
        $row = $wpdb->get_row($wpdb->prepare("SELECT full_name FROM $table WHERE id = %d", $person_id));
        return $row ? $row->full_name : '';
    }
    public static function render_form($client_id) {
        // Render the add/edit form for personal property
        // This should match the fields in the normalized table and use dropdowns for owner and auto model
        require_once dirname(__DIR__, 2) . '/includes/tables/AutoModelTable.php';
        require_once dirname(__DIR__, 2) . '/includes/tables/PersonTable.php';
        $auto_options = AutoModelTable::get_all_options();
        $person_options = PersonTable::get_person_options($client_id);
        echo '<form class="epm-personal-property-form" data-section="personal_property">';
        echo '<label>Property Type:</label> <input type="text" name="property_type"><br>';
        echo '<label>Item Type:</label> <input type="text" name="item_type"><br>';
        echo '<label>Vehicle Model:</label> <select name="auto_model_id">';
        echo '<option value="">Select...</option>';
        foreach ($auto_options as $id => $label) {
            echo '<option value="' . esc_attr($id) . '">' . esc_html($label) . '</option>';
        }
        echo '</select><br>';
        echo '<label>Own/Lease:</label> <select name="own_or_lease"><option value="">Select...</option><option value="Own">Own</option><option value="Lease">Lease</option></select><br>';
        echo '<label>Owner:</label> <select name="owner_person_id">';
        echo '<option value="">Select...</option>';
        foreach ($person_options as $id => $name) {
            echo '<option value="' . esc_attr($id) . '">' . esc_html($name) . '</option>';
        }
        echo '</select><br>';
        echo '<label>Registration Location:</label> <input type="text" name="registration_location"><br>';
        echo '<label>Insurance Policy Location:</label> <input type="text" name="insurance_policy_location"><br>';
        echo '<label>Bill of Sale Location:</label> <input type="text" name="bill_of_sale_location"><br>';
        echo '<label>Keys Location:</label> <input type="text" name="keys_location"><br>';
        echo '<label>Contents List Location:</label> <input type="text" name="contents_list_location"><br>';
        echo '<button type="submit" class="epm-btn epm-btn-primary">Save</button>';
        echo '</form>';
    }
}
