<?php

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class MainController
{
    public function __construct()
    {
        register_activation_hook(SCF7E_PLUGIN_BASENAME, array( $this, 'activation_hook' ));
        register_uninstall_hook( SCF7E_PLUGIN_BASENAME, array( $this, 'uninstall_hook' ) );
        add_action( 'init', array( $this, 'create_post_type_on_activation' ) );
        add_action('wpcf7_before_send_mail', array($this, 'save_cf7_entry'));
        add_action('add_meta_boxes', array($this, 'add_custom_meta_box'));
        add_filter('wp_terms_checklist_args', array($this, 'exclude_default_categories'), 10, 2);
    }

    function create_post_type_on_activation() {
        $labels = array(
            'name'               => _x( 'Form Entries', 'post type general name' ),
            'singular_name'      => _x( 'Form Entry', 'post type singular name' ),
            'menu_name'          => 'Form Entries'
        );
        $args = array(
            'labels'        => $labels,
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array( 'title' ),
            'has_archive'   => true,
            'taxonomies' => array('category'),
        );
        register_post_type( 'from-entry', $args ); 

        register_taxonomy_for_object_type('category', 'from-entry');

    }


    
    // Function to save the Contact Form 7 entry
    function save_cf7_entry($cf7) {
        // Get the Contact Form 7 form ID
        $form_id = $cf7->id();
        $form = wpcf7_contact_form($form_id);
        $form_title = $form->title;

        // Get the Contact Form 7 form data
        $submission = WPCF7_Submission::get_instance();

        if ($submission) {
            // Get the form fields and values
            $form_data = $submission->get_posted_data();
            $post_type = 'from-entry';
            // $from_name = isset($form_data['your-from-name-field']) ? $form_data['your-from-name-field'] : '';

            // You can process and save the form data as per your requirements
            // For example, you can save it to the database or send it via email

            // Example: Saving the form data to the WordPress database
            $entry_id = wp_insert_post(array(
                'post_type' => $post_type,
                'post_title' => $form_title,
                'post_status' => 'publish',
                'post_category' => array(),
            ));

            // Assign categories based on category titles
            // $category_titles = array( $form_title ); // Replace with your category titles

            // foreach ($category_titles as $category_title) {
            // if ($category) {
            //     wp_set_post_categories($entry_id, array($category->term_id), true);
            // } else {
            //     $new_category = wp_insert_term($category_title, 'category');
            //     if (!is_wp_error($new_category)) {
            //         wp_set_post_categories($entry_id, array($new_category['term_id']), true);
            //     }
            // }
            

            $taxonomy = 'category'; // Replace with your desired taxonomy (e.g., custom_taxonomy)
            $args = array(
                'description' => 'Category for the custom post type',
            );
            
            register_taxonomy($taxonomy, $post_type, $args);
            
            if (!term_exists($form_title, $taxonomy)) {
                wp_insert_term($form_title, $taxonomy);
            }
            
            $category = get_term_by('name', $form_title, 'category');
            wp_set_post_categories($entry_id, array($category->term_id), true);
            // }

            // Loop through the form fields and values
            foreach ($form_data as $field_name => $field_value) {
                // Save each field and value as post meta
                update_post_meta($entry_id, $field_name, $field_value);
            }
        }
    }

    // add_action('add_meta_boxes', 'add_custom_meta_box');

    function add_custom_meta_box() {
        add_meta_box('custom_post_meta', 'Form Entry Details', array($this,'render_custom_meta_box'), 'from-entry', 'normal', 'default');
    }

    function render_custom_meta_box($post) {
        // Get all post meta data
        $post_meta = get_post_meta($post->ID);

        // Output the meta data
        $output =  '<table>';
        $output .=  '<thead>';
        $output .=  '<tr>';
        $output .=  '<th style="width:50%;text-align: left;">Field</th>';
        $output .=  '<th style="width:50%;text-align: left;">Value</th>';
        $output .=  '</tr>';
        $output .=  '</thead>';
        $output .=  '<tbody>';
        foreach ($post_meta as $key => $value) {
            $output .=  '<tr>';
            $output .=  '<td style="width:50%">'.$key.'</td>';
            $output .=  '<td style="width:50%">'.$value[0].'</td>';
            $output .=  '</tr>';
        }
        $output .= '</tbody>';
        $output .= '</table>';
        echo $output;
        // print_r($post_meta);
    }

    // Modify the category checklist for the custom post type
    function exclude_default_categories($args, $post_id) {
        $post_type = get_post_type($post_id);

        // Specify the custom post type for which to exclude default categories
        if ('from-entry' === $post_type) {
            $args['checked_ontop'] = false;
            $args['selected_cats'] = array(); // Exclude default categories
        }

        return $args;
    }


}