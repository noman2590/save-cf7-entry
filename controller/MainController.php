<?php

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class MainController
{
    public function __construct()
    {
        register_activation_hook( SCF7E_PLUGIN_BASENAME, array( $this, 'activation_hook' ));
        register_uninstall_hook( SCF7E_PLUGIN_BASENAME, array( $this, 'uninstall_hook' ) );
        // add_action( 'init', array( $this, 'create_post_type_on_activation' ) );
        add_action('wpcf7_before_send_mail', array($this, 'save_cf7_entry'));
        add_action('add_meta_boxes', array($this, 'add_custom_meta_box'));
        add_filter('wp_terms_checklist_args', array($this, 'exclude_default_categories'), 10, 2);
        add_action('admin_menu', array( $this, 'admin_menu' ));
        add_action('admin_menu', array( $this, 'register_custom_admin_page' ));
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin_data_tables_on_custom_page'));
    }

    public function admin_menu() {
        add_menu_page(
            __('CF7 Entries', 'cf7-entries'),
            __('CF7 Entries', 'cf7-entries'),
            'manage_options',
            'manage-cf7-entries',
            'ContactController::index',
            'dashicons-database',
            6
        );
    }

    function register_custom_admin_page() {
        add_submenu_page(
            null, // Set parent_slug to null to create a hidden submenu
            __('Form Entries', 'form-entries'),
            __('Form Entries', 'form-entries'),
            'manage_options',
            'form-entries',
            'ContactController::form_entry_details'
        );
    }


    public static function set_query_var_custom( $args )
    {
        global $wp_query;
        $wp_query->set("data", $args);

    }

    public function activation_hook() {
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
    function save_cf7_entry($cf7) {
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

            if( entry_id ) {
                foreach ($form_data as $field_name => $field_value) {
                    $table = $wpdb->prefix . 'cf7_entry_meta';
                    $data = array( 'cf7_entry_id' => $entry_id, 'meta_key' => $field_name, 'meta_value' => $field_value );
                    $wpdb->insert($table, $data);
                }
            }
            
        }
    }

    function enqueue_admin_data_tables_on_custom_page() {
        global $pagenow;
        if ($pagenow === 'admin.php' && $_GET['page'] === 'form-entries') {
            wp_enqueue_script('jquery');
            wp_enqueue_script('data_tables', 'https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js', array('jquery'), '1.10.25', true);
            wp_enqueue_style('data_tables_style', 'https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css');
        }
        wp_enqueue_style('plugin-style', SCF7E_PLUGIN_URL . '/lib/assets/style.css');
    }
      

}