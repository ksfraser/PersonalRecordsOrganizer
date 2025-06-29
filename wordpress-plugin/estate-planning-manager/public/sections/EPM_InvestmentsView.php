<?php
// EPM_InvestmentsView: Dedicated view class for the Investments section
class EPM_InvestmentsView {
    public static function render($client_id) {
        global $wpdb;
        require_once dirname(__DIR__, 2) . '/includes/tables/InvestmentsTable.php';
        require_once dirname(__DIR__, 2) . '/includes/tables/PersonTable.php';
        require_once dirname(__DIR__, 2) . '/includes/tables/OrganizationTable.php';
        $table = $wpdb->prefix . 'epm_investments';
        $person_options = PersonTable::get_person_options($client_id);
        $org_options = OrganizationTable::get_org_options($client_id);
        $records = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE client_id = %d", $client_id));
        echo '<div class="epm-data-section" data-section="investments">';
        echo '<h3>Investment Accounts</h3>';
        if (empty($records)) {
            echo '<p class="epm-no-data">No investments recorded.</p>';
        } else {
            echo '<table class="epm-data-table">';
            echo '<tr><th>Type</th><th>Company</th><th>Account #</th><th>Beneficiary</th><th>Advisor</th><th>Lender</th></tr>';
            foreach ($records as $row) {
                $beneficiary = isset($person_options[$row->beneficiary_person_id]) ? $person_options[$row->beneficiary_person_id] : '';
                $advisor = isset($person_options[$row->advisor_person_id]) ? $person_options[$row->advisor_person_id] : '';
                $lender = '';
                if ($row->lender_type === 'person' && $row->lender_person_id) {
                    $lender = isset($person_options[$row->lender_person_id]) ? $person_options[$row->lender_person_id] : '';
                } elseif ($row->lender_type === 'organization' && $row->lender_org_id) {
                    $lender = isset($org_options[$row->lender_org_id]) ? $org_options[$row->lender_org_id] : '';
                }
                echo '<tr>';
                echo '<td>' . esc_html($row->investment_type) . '</td>';
                echo '<td>' . esc_html($row->financial_company) . '</td>';
                echo '<td>' . esc_html($row->account_number) . '</td>';
                echo '<td>' . esc_html($beneficiary) . '</td>';
                echo '<td>' . esc_html($advisor) . '</td>';
                echo '<td>' . esc_html($lender) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
    }

    public static function render_form($client_id) {
        require_once dirname(__DIR__, 2) . '/includes/tables/PersonTable.php';
        require_once dirname(__DIR__, 2) . '/includes/tables/OrganizationTable.php';
        $person_options = PersonTable::get_person_options($client_id);
        $org_options = OrganizationTable::get_org_options($client_id);
        echo '<form class="epm-form" data-section="investments">';
        echo '<label>Investment Type:</label><input type="text" name="investment_type"><br>';
        echo '<label>Financial Company:</label><input type="text" name="financial_company"><br>';
        echo '<label>Account Type:</label><input type="text" name="account_type"><br>';
        echo '<label>Account Number:</label><input type="text" name="account_number"><br>';
        echo '<label>Beneficiary:</label>';
        echo '<select name="beneficiary_person_id" class="epm-person-select">';
        echo '<option value="">Select...</option>';
        foreach ($person_options as $id => $name) {
            echo '<option value="' . esc_attr($id) . '">' . esc_html($name) . '</option>';
        }
        echo '</select><br>';
        echo '<label>Advisor:</label>';
        echo '<select name="advisor_person_id" class="epm-person-select">';
        echo '<option value="">Select...</option>';
        foreach ($person_options as $id => $name) {
            echo '<option value="' . esc_attr($id) . '">' . esc_html($name) . '</option>';
        }
        echo '</select><br>';
        // Lender type selector
        echo '<label>Lender Type:</label>';
        echo '<select name="lender_type" id="epm-lender-type-select">';
        echo '<option value="">Select...</option>';
        echo '<option value="person">Person</option>';
        echo '<option value="organization">Organization</option>';
        echo '</select><br>';
        // Lender person dropdown
        echo '<div id="epm-lender-person-row" style="display:none">';
        echo '<label>Lender (Person):</label>';
        echo '<select name="lender_person_id" class="epm-person-select">';
        echo '<option value="">Select...</option>';
        foreach ($person_options as $id => $name) {
            echo '<option value="' . esc_attr($id) . '">' . esc_html($name) . '</option>';
        }
        echo '</select><br>';
        echo '</div>';
        // Lender org dropdown
        echo '<div id="epm-lender-org-row" style="display:none">';
        echo '<label>Lender (Organization):</label>';
        echo '<select name="lender_org_id" class="epm-institute-select">';
        echo '<option value="">Select...</option>';
        foreach ($org_options as $id => $name) {
            echo '<option value="' . esc_attr($id) . '">' . esc_html($name) . '</option>';
        }
        echo '</select><br>';
        echo '</div>';
        echo '<button type="submit" class="epm-btn epm-btn-primary">Save</button>';
        echo '</form>';
        // JS to toggle lender dropdowns
        echo '<script>jQuery(function($){$("#epm-lender-type-select").on("change",function(){var v=$(this).val();$("#epm-lender-person-row,#epm-lender-org-row").hide();if(v==="person"){$("#epm-lender-person-row").show();}else if(v==="organization"){$("#epm-lender-org-row").show();}});});</script>';
    }
}
