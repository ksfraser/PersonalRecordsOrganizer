<?php
use PHPUnit\Framework\TestCase;

class EPM_InsuranceCategoryType_Test extends TestCase {
    public function test_category_options_prefilled() {
        if (!function_exists('epm_get_insurance_category_options')) {
            require_once dirname(__DIR__) . '/estate-planning-manager/public/includes/epm-insurance-selectors.php';
        }
        $options = epm_get_insurance_category_options();
        $this->assertArrayHasKey(1, $options); // Life
        $this->assertArrayHasKey(2, $options); // Auto
        $this->assertArrayHasKey(3, $options); // House
    }
    public function test_type_options_prefilled() {
        if (!function_exists('epm_get_insurance_type_options')) {
            require_once dirname(__DIR__) . '/estate-planning-manager/public/includes/epm-insurance-selectors.php';
        }
        $lifeTypes = epm_get_insurance_type_options(1);
        $autoTypes = epm_get_insurance_type_options(2);
        $houseTypes = epm_get_insurance_type_options(3);
        $this->assertContains('Whole Life', $lifeTypes);
        $this->assertContains('Auto', $autoTypes);
        $this->assertContains('House', $houseTypes);
    }
    public function test_category_crud() {
        global $wpdb;
        $table = $wpdb->prefix . 'epm_insurance_category';
        // Create
        $wpdb->insert($table, ['name' => 'Test Category']);
        $id = $wpdb->insert_id;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
        $this->assertEquals('Test Category', $row->name);
        // Update
        $wpdb->update($table, ['name' => 'Updated Category'], ['id' => $id]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
        $this->assertEquals('Updated Category', $row->name);
        // Delete
        $wpdb->delete($table, ['id' => $id]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
        $this->assertNull($row);
    }
    public function test_type_crud() {
        global $wpdb;
        $cat_table = $wpdb->prefix . 'epm_insurance_category';
        $type_table = $wpdb->prefix . 'epm_insurance_type';
        // Create a category
        $wpdb->insert($cat_table, ['name' => 'CRUD Cat']);
        $cat_id = $wpdb->insert_id;
        // Create type
        $wpdb->insert($type_table, ['name' => 'CRUD Type', 'category_id' => $cat_id]);
        $type_id = $wpdb->insert_id;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $type_table WHERE id = %d", $type_id));
        $this->assertEquals('CRUD Type', $row->name);
        $this->assertEquals($cat_id, $row->category_id);
        // Update
        $wpdb->update($type_table, ['name' => 'Updated Type'], ['id' => $type_id]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $type_table WHERE id = %d", $type_id));
        $this->assertEquals('Updated Type', $row->name);
        // Delete
        $wpdb->delete($type_table, ['id' => $type_id]);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $type_table WHERE id = %d", $type_id));
        $this->assertNull($row);
        // Clean up category
        $wpdb->delete($cat_table, ['id' => $cat_id]);
    }
}
