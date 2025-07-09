<?php
// Admin screen for managing phone line types
if (!defined('ABSPATH')) exit;

class EPM_Admin_Phone_Line_Types {
    public static function render_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epm_phone_line_types';
        $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY sort_order, label");
        ?>
        <div class="wrap">
            <h1>Phone Line Types</h1>
            <table class="widefat">
                <thead>
                    <tr><th>ID</th><th>Value</th><th>Label</th><th>Active</th><th>Sort Order</th><th>Created</th><th>Last Updated</th></tr>
                </thead>
                <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo esc_html($row->id); ?></td>
                        <td><?php echo esc_html($row->value); ?></td>
                        <td><?php echo esc_html($row->label); ?></td>
                        <td><?php echo $row->is_active ? 'Yes' : 'No'; ?></td>
                        <td><?php echo esc_html($row->sort_order); ?></td>
                        <td><?php echo esc_html($row->created); ?></td>
                        <td><?php echo esc_html($row->lastupdated); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
