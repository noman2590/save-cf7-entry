<?php


class ContactController extends MainController {
    public static function index(){
        /**
         * Extracting passed aguments
         */
        global $wpdb;
        $contact_forms =  $wpdb->get_results("SELECT forms.id, forms.post_title, COUNT(entries.post_id) AS total_entries 
        FROM {$wpdb->prefix}posts AS forms
        INNER JOIN {$wpdb->prefix}cf7_entries AS entries
        ON entries.post_id = forms.id
        WHERE post_type = 'wpcf7_contact_form'
        GROUP BY forms.id
        " );
        parent::set_query_var_custom(['data'=> $contact_forms ]);
        load_template( SCF7E_LIB_PATH. '/views/entries.php' );
    }

    public static function form_entry_details () {
        global $wpdb;

        if(isset($_GET['form']) && !empty($_GET['form'])) {
            load_template( SCF7E_LIB_PATH. '/views/entry-details.php' );
        }
        else {
            return '404';
        }
    } 
}

