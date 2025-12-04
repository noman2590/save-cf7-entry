<?php

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class SCF7EMainController
{
    public function __construct()
    {
        register_activation_hook( SCF7E_PLUGIN_BASENAME, array( $this, 'scf7e_activation_hook' ));
        add_action('wpcf7_before_send_mail', array($this, 'scf7e_save_cf7_entry'));
        add_action('admin_menu', array( $this, 'scf7e_admin_menu' ));
        add_action('admin_menu', array( $this, 'scf7e_register_custom_admin_page' ));
        add_action('admin_init', array( $this, 'scf7e_check_cf7_installed'));
        add_action('admin_post_scf7e_delete_entry', array( $this, 'scf7e_handle_delete_entry'));
        add_action('admin_notices', array( $this, 'scf7e_display_admin_notice'));
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
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
            $sql = "CREATE TABLE  $table_name (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, 
                `post_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";
            dbDelta($sql);
        }

        $table_name = $wpdb->prefix . 'cf7_entry_meta';
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
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

    // show notice if contact form plugin isn't active
    public function scf7e_check_cf7_installed() {
        if (!class_exists('WPCF7')) {
            add_action('admin_notices', array( $this, 'scf7e_cf7_admin_notice'));
        }
    }
    
    // Function to display the admin notice
    public function scf7e_cf7_admin_notice() {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('Contact Form 7 is not installed or activated. Please install and activate Contact Form 7 to use this plugin.', 'save-cf7-entry'); ?></p>
        </div>
        <?php
    }

    // format form field names 
    public static function scf7e_convert_to_title_case($string) {
        $string_with_spaces = str_replace('-', ' ', $string);
        $title_case_string = ucwords($string_with_spaces);
        return $title_case_string;
    }

    // function to delete entries
    public function scf7e_handle_delete_entry() {
        check_admin_referer( 'scf7e_delete_entry', 'scf7e_delete_nonce' );
    
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }
    
        $entry_id = isset( $_POST['scf7e_delete'] ) ? absint( $_POST['scf7e_delete'] ) : 0;
        if ( ! $entry_id ) {
            wp_die( 'Invalid entry' );
        }
    
        global $wpdb;
        $wpdb->delete( $wpdb->prefix . 'cf7_entries',     [ 'id' => $entry_id ], [ '%d' ] );
        $wpdb->delete( $wpdb->prefix . 'cf7_entry_meta', [ 'cf7_entry_id' => $entry_id ], [ '%d' ] );
    
        // SET SUCCESS MESSAGE — 100% guaranteed to show
        set_transient( 'scf7e_entry_deleted', true, 30 );
    
        // Clean redirect — no deleted=1 needed
        $redirect_url = admin_url( 'admin.php' );
        $args = [
            'page' => 'form-entries',
            'form' => absint( $_POST['form'] ?? 0 ),
        ];
        if ( ! empty( $_POST['from_date'] ) ) $args['from_date'] = sanitize_text_field( $_POST['from_date'] );
        if ( ! empty( $_POST['to_date'] ) )   $args['to_date']   = sanitize_text_field( $_POST['to_date'] );
        if ( ! empty( $_POST['paged'] ) )     $args['paged']     = absint( $_POST['paged'] );
    
        $redirect_url = add_query_arg( $args, $redirect_url );
    
        wp_safe_redirect( $redirect_url );
        exit;
    }

    // display notices on entries page
    public function scf7e_display_admin_notice() {

        if ($notice = get_transient('scf7e_admin_success')) {      
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($notice) . '</p></div>';
            delete_transient('scf7e_admin_success');
        }

        if ($notice = get_transient('scf7e_admin_failed')) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($notice) . '</p></div>';
            delete_transient('scf7e_admin_failed');
        }
    }

    public static function scf7e_get_entries_url( $page_slug, $form_id = null ) {

        // Optional parameters
        $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : null;
        $to_date = isset($_GET['to_date']) ? $_GET['to_date'] : null;

        // Build the base URL
        $url = admin_url('admin.php');

        // Create an array of query parameters
        $query_args = array('page' => $page_slug);

        
        // Add optional parameters if they are set
        if($form_id) {
            $query_args['form'] = $form_id;
        }
        if (!empty($from_date)) {
            $query_args['from_date'] = $from_date;
        }
        if (!empty($to_date)) {
            $query_args['to_date'] = $to_date;
        }

        // Generate the final URL with the query parameters
        $final_url = add_query_arg($query_args, $url);

        return $final_url;
    }
}