<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { die; }

// drop database tables
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cf7_entries" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cf7_entry_meta" );

?>