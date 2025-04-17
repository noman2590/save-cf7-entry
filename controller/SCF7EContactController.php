<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class SCF7EContactController extends SCF7EMainController {
    public static function index(){
        /**
         * Extracting passed aguments
         */
        global $wpdb;

        $start_date = (isset($_GET['from_date']) && !empty($_GET['from_date'])) ? sanitize_text_field($_GET['from_date']) : null;
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

    public static function form_entry_details () {
        global $wpdb;
        $start_date = (isset($_GET['from_date']) && !empty($_GET['from_date'])) ? sanitize_text_field($_GET['from_date']) : null;
        $end_date = (isset($_GET['to_date']) && !empty($_GET['to_date'])) ? date('Y-m-d', strtotime("+1 day", strtotime(sanitize_text_field($_GET['to_date'])))) : date('Y-m-d', strtotime("+1 day"));
        
        $formid = sanitize_text_field($_GET['form']); 
        if(isset($formid) && !empty($formid)) {

            $sql = "SELECT *
            FROM {$wpdb->prefix}cf7_entries AS entries
            LEFT JOIN {$wpdb->prefix}cf7_entry_meta AS entrymeta
            ON entries.id = entrymeta.cf7_entry_id
            WHERE entries.post_id = $formid
            AND entries.created_at >= %s 
            AND entries.created_at <= %s;
            ";

            $entries =  $wpdb->get_results(
                $wpdb->prepare(
                    $sql,
                    $start_date,
                    $end_date
                )
            );

            parent::set_query_var_custom(['data'=> $entries ]);
            load_template( SCF7E_LIB_PATH. '/views/entry-details.php' );
        }
        else {
            wp_die('Sorry, you are not allowed to access this page.', 'Access Denied', array('response' => 403));
        }
    } 
}

