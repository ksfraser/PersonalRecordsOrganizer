<?php
/**
 * EPM Bank Accounts Module
 * Custom SuiteCRM module for Estate Planning Manager bank account data
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class EPM_BankAccounts extends Basic {
    
    public $new_schema = true;
    public $module_dir = 'EPM_BankAccounts';
    public $object_name = 'EPM_BankAccounts';
    public $table_name = 'epm_bankaccounts';
    public $importable = true;
    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $description;
    public $deleted;
    public $created_by_link;
    public $modified_user_link;
    public $assigned_user_id;
    public $assigned_user_name;
    public $assigned_user_link;
    public $SecurityGroups;
    
    // Custom fields for bank accounts
    public $bank_name;
    public $account_type;
    public $account_number;
    public $branch;
    public $contact_id;
    public $contact_name;
    public $contact_link;
    public $wp_client_id;
    public $wp_record_id;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function bean_implements($interface) {
        switch($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
    
    public function get_summary_text() {
        return $this->bank_name . ' - ' . $this->account_type;
    }
    
    public function build_generic_where_clause($the_query_string) {
        $where_clauses = array();
        $the_query_string = $this->db->quote($the_query_string);
        array_push($where_clauses, "epm_bankaccounts.bank_name like '$the_query_string%'");
        array_push($where_clauses, "epm_bankaccounts.account_type like '$the_query_string%'");
        
        $the_where = "";
        foreach($where_clauses as $clause) {
            if($the_where != "") $the_where .= " or ";
            $the_where .= $clause;
        }
        
        return $the_where;
    }
    
    public function get_list_view_data() {
        $temp_array = $this->get_list_view_array();
        $temp_array['ENCODED_NAME'] = $this->name;
        return $temp_array;
    }
    
    public function save_relationship_changes($is_update) {
        parent::save_relationship_changes($is_update);
    }
    
    public function mark_relationships_deleted($id) {
        $this->delete_linked($id, 'epm_bankaccounts_modified_user');
        $this->delete_linked($id, 'epm_bankaccounts_created_by');
        $this->delete_linked($id, 'epm_bankaccounts_assigned_user');
        $this->delete_linked($id, 'epm_bankaccounts_contacts');
    }
    
    public function fill_in_additional_list_fields() {
        parent::fill_in_additional_list_fields();
    }
    
    public function fill_in_additional_detail_fields() {
        parent::fill_in_additional_detail_fields();
    }
    
    public function get_linked_fields() {
        $linked_fields = parent::get_linked_fields();
        $linked_fields['Contacts'] = 'contacts';
        return $linked_fields;
    }
}
