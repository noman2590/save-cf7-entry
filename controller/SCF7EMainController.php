<?php

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class SCF7EMainController
{
    public function __construct()
    {
        register_activation_hook( SCF7E_PLUGIN_BASENAME, array( $this, 'scf7e_activation_hook' ));
        add_action('wpcf7_before_send_mail', array($this, 'scf7e_save_cf7_entry'));
        add_action('admin_menu', array( $this, 'scf7e_admin_menu' ));
        add_action('admin_menu', array( $this, 'scf7e_register_custom_admin_page' ));
        add_action('admin_enqueue_scripts', array( $this, 'scf7e_enqueue_admin_scripts'));
    }

    public function scf7e_admin_menu() {
        add_menu_page(
            __('CF7 Entries', 'cf7-entries'),
            __('CF7 Entries', 'cf7-entries'),
            'manage_options',
            'manage-cf7-entries',
            'SCF7EContactController::index',
            'dashicons-database',
            6
        );
    }

    function scf7e_register_custom_admin_page() {
        add_submenu_page(
            'cf7-entries', // hidden submenu
            __('Form Entries Listing', 'form-entries'),
            __('Form Entries Listing', 'form-entries'),
            'manage_options',
            'form-entries',
            'SCF7EContactController::form_entry_details'
        );
    }

    public static function set_query_var_custom( $args )
    {
        global $wp_query;
        $wp_query->set("data", $args);

    }

    public function scf7e_activation_hook() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'cf7_entries';
        $sql = "CREATE TABLE  $table_name (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `post_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        $table_name = $wpdb->prefix . 'cf7_entry_meta';
        $sql = "CREATE TABLE  $table_name (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `cf7_entry_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `meta_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    // Function to save the Contact Form 7 entry
    function scf7e_save_cf7_entry($cf7) {
        $form_id = $cf7->id();
        $form = wpcf7_contact_form($form_id);
        $form_title = $form->title;

        // Get the Contact Form 7 form data
        $submission = WPCF7_Submission::get_instance();

        if ($submission) {
            $form_data = $submission->get_posted_data();

            global $wpdb;

            $table = $wpdb->prefix . 'cf7_entries';
            $data = array( 'post_id' => $form_id );
            $wpdb->insert($table, $data);
            $entry_id = $wpdb->insert_id;

            if( $entry_id ) {
                foreach ($form_data as $field_name => $field_value) {
                    $table = $wpdb->prefix . 'cf7_entry_meta';
                    $data = array( 'cf7_entry_id' => $entry_id, 'meta_key' => $field_name, 'meta_value' => $field_value );
                    $wpdb->insert($table, $data);
                }
            }
            
        }
    }

    function scf7e_enqueue_admin_scripts () {
        global $pagenow;
        if ($pagenow === 'admin.php' && $_GET['page'] === 'form-entries') {
            wp_enqueue_script('data_tables', 'https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js', array('jquery'), '1.10.25', true);
            wp_enqueue_style('data_tables_style', 'https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css');
        }
        wp_enqueue_style('plugin-style', SCF7E_PLUGIN_URL . '/lib/assets/style.css');
    }
      

}