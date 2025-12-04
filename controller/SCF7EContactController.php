<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class SCF7EContactController extends SCF7EMainController {
    public static function index(){
        /**
         * Extracting passed aguments
         */
        global $wpdb;

        $start_date = (isset($_GET['from_date']) && !empty($_GET['from_date'])) ? sanitize_text_field($_GET['from_date']) : '2000-01-01';
        $end_date = (isset($_GET['to_date']) && !empty($_GET['to_date'])) ? date('Y-m-d', strtotime("+1 day", strtotime(sanitize_text_field($_GET['to_date'])))) : date('Y-m-d', strtotime("+1 day"));

        $sql = "SELECT forms.id, forms.post_title, COUNT(entries.post_id) AS total_entries 
        FROM {$wpdb->prefix}posts AS forms
        INNER JOIN {$wpdb->prefix}cf7_entries AS entries
        ON entries.post_id = forms.id
        WHERE post_type = 'wpcf7_contact_form'
        AND entries.created_at >= %s 
        AND entries.created_at <= %s
        GROUP BY forms.id
        ";

        $contact_forms =  $wpdb->get_results(
            $wpdb->prepare(
                $sql,
                $start_date,
                $end_date
            )
        );

        parent::set_query_var_custom(['data'=> $contact_forms ]);
        load_template( SCF7E_LIB_PATH. '/views/entries.php' );
    }

    public static function form_entry_details() {
        $formid = isset($_GET['form']) ? absint($_GET['form']) : 0;
        if (!$formid) {
            wp_die('Invalid form ID.', 'Access Denied', ['response' => 403]);
        }
        
        // Include the list table class
        require_once SCF7E_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'includes/class-scf7e-entries-list-table.php';
    
        $list_table = new SCF7E_Entries_List_Table($formid);
        $list_table->prepare_items();
    
        // Pass to view
        parent::set_query_var_custom([
            'list_table' => $list_table,
            'form_id'    => $formid
        ]);
    
        load_template(SCF7E_LIB_PATH . '/views/entry-details.php');
    }
}

