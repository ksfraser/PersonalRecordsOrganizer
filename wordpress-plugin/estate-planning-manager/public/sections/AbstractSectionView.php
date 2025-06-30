<?php
/**
 * AbstractSectionView
 *
 * Provides generic render and render_form logic for EPM section views.
 */

namespace EstatePlanningManager\Sections;

use EPM_Shortcodes;

interface SectionViewInterface
{
    public static function get_section_key();
    public static function get_fields(
        /*EPM_Shortcodes*/ $shortcodes
    );
}

abstract class AbstractSectionView implements SectionViewInterface
{
    /**
     * @var EPM_Shortcodes|null
     */
    protected static $shortcodes;

    public static function setShortcodes($shortcodes)
    {
        static::$shortcodes = $shortcodes;
    }

    /**
     * Get the section key (e.g., 'personal', 'banking', etc.)
     * @return string
     */
    abstract public static function get_section_key();

    /**
     * Get the fields array for this section
     * @return array
     */
    abstract public static function get_fields($shortcodes);

    /**
     * Render the section data (read-only or editable)
     * @param int $user_id
     * @param bool $readonly
     */
    public static function render($user_id, $readonly = false) {
        if (!static::$shortcodes) {
            static::$shortcodes = \EPM_Shortcodes::instance();
        }
        $fields = static::get_fields(static::$shortcodes);
        $section = static::get_section_key();
        $data = static::$shortcodes->get_client_data($section, $user_id);
        echo '<div class="epm-section-data">';
        echo '<table class="epm-section-table" style="width:100%;">';
        foreach ($fields as $field) {
            $label = esc_html($field['label']);
            $value = isset($data->{$field['name']}) ? esc_html($data->{$field['name']}) : '';
            echo '<tr><th style="text-align:left;width:30%;">' . $label . '</th><td>' . $value . '</td></tr>';
        }
        echo '</table>';
        echo '</div>';
    }

    /**
     * Render the section form
     * @param int $user_id
     */
    public static function render_form($user_id) {
        if (!static::$shortcodes) {
            static::$shortcodes = \EPM_Shortcodes::instance();
        }
        $fields = static::get_fields(static::$shortcodes);
        $section = static::get_section_key();
        echo '<form method="post" class="epm-section-form">';
        echo '<input type="hidden" name="section" value="' . esc_attr($section) . '">';
        foreach ($fields as $field) {
            static::$shortcodes->render_form_field($field, $user_id);
        }
        echo '<button type="submit" class="epm-btn epm-btn-primary">Save</button>';
        echo '</form>';
    }

    /**
     * Child classes must provide the model instance.
     */
    abstract protected function getModel();
    /**
     * Child classes must provide the section name/slug.
     */
    abstract protected function getSection();
    /**
     * Render the section view: summary table, add/edit/delete for owner, read-only for shared users.
     * @param int $client_id
     * @param bool $readonly
     */
    public function renderSectionView($client_id, $readonly = false) {
        $model = $this->getModel();
        $section = $this->getSection();
        // Add H3 header for each section
        $section_titles = [
            'personal' => 'Personal Information',
            'banking' => 'Bank Accounts',
            'investments' => 'Investments',
            'real_estate' => 'Real Estate',
            'insurance' => 'Insurance Policies',
            'scheduled_payments' => 'Scheduled Payments',
            'personal_property' => 'Personal Property',
            'auto_property' => 'Automobiles',
            'emergency_contacts' => 'Emergency Contacts',
        ];
        $title = isset($section_titles[$section]) ? $section_titles[$section] : ucfirst(str_replace('_', ' ', $section));
        echo '<h3 class="epm-section-title">' . esc_html($title) . '</h3>';
        $records = $model->getAllRecordsForClient($client_id);
        // Display summary table
        $this->renderSummaryTable($records, !$readonly, $model);
        // Only allow add/edit/delete if not readonly
        if (!$readonly) {
            $this->renderAddEditDeleteUI($records, $model);
        } else {
            echo '<div class="epm-readonly-notice">This section is shared with you. You have read-only access.</div>';
        }
    }
    protected function renderSummaryTable($records, $is_owner, $model) {
        // Determine columns: use all keys from the first record, or model's getFormFields()
        $columns = [];
        if (!empty($records)) {
            $columns = array_keys($records[0]);
        } elseif (method_exists($model, 'getFormFields')) {
            foreach ($model->getFormFields() as $field) {
                $columns[] = $field['name'];
            }
        }
        // Exclude 'id' and 'client_id' fields
        $exclude = ['id', 'client_id'];
        $columns = array_filter($columns, function($col) use ($exclude) { return !in_array($col, $exclude); });
        echo '<table class="epm-summary-table"><thead><tr>';
        foreach ($columns as $field) {
            echo '<th>' . esc_html(ucwords(str_replace('_', ' ', $field))) . '</th>';
        }
        if ($is_owner) {
            echo '<th>Actions</th>';
        }
        echo '</tr></thead><tbody>';
        foreach ($records as $record) {
            echo '<tr>';
            foreach ($columns as $field) {
                echo '<td>' . esc_html(isset($record[$field]) ? $record[$field] : '') . '</td>';
            }
            if ($is_owner) {
                echo '<td>';
                echo '<a href="#" class="epm-edit-record" data-id="' . esc_attr($record['id']) . '">Edit</a> | ';
                echo '<a href="#" class="epm-delete-record" data-id="' . esc_attr($record['id']) . '">Delete</a>';
                echo '</td>';
            }
            echo '</tr>';
        }
        echo '</tbody></table>';
    }
    protected function renderAddEditDeleteUI($records, $model) {
        echo '<button class="epm-add-new">Add New</button>';
        // Modal for Add/Edit form
        echo '<div id="epm-modal" class="epm-modal" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);">';
        echo '<div class="epm-modal-content" style="background:#fff;margin:5% auto;padding:20px;max-width:500px;position:relative;">';
        echo '<span class="epm-modal-close" style="position:absolute;top:10px;right:15px;font-size:24px;cursor:pointer;">&times;</span>';
        echo '<h4>Add New Record</h4>';
        // Render the form fields (empty for new record)
        echo '<form class="epm-modal-form">';
        foreach ($model->getFormFields() as $field) {
            echo '<div class="epm-form-group">';
            echo '<label>' . esc_html($field['label']) . '</label>';
            echo '<input type="text" name="' . esc_attr($field['name']) . '" value="" class="epm-form-control">';
            echo '</div>';
        }
        echo '<button type="submit" class="epm-btn epm-btn-primary">Save</button>';
        echo '</form>';
        echo '</div></div>';
        // Inline JS for modal logic
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var modal = document.getElementById("epm-modal");
            var btn = document.querySelector(".epm-add-new");
            var close = document.querySelector(".epm-modal-close");
            if(btn && modal && close) {
                btn.onclick = function() { modal.style.display = "block"; };
                close.onclick = function() { modal.style.display = "none"; };
                window.onclick = function(event) { if(event.target == modal) { modal.style.display = "none"; } };
            }
        });
        </script>';
    }
}
