<?php
/*
Plugin Name: Save CF7 Entry
Description: A simple plugin to saves contact form 7 entries into the database.
Author: Noman Akram
Author URI: https://profiles.wordpress.org/nomanakram2590/
Text Domain: save-cf7-entry
Requires Plugins: contact-form-7
Version: 1.0.2
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SCF7E_VERSION', '1.0.2' );
define( 'SCF7E_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'SCF7E_PLUGIN_BASENAME',  basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'SCF7E_PLUGIN_URL', plugins_url( '', SCF7E_PLUGIN_BASENAME ) );
define( 'SCF7E_CONTROLLER_PATH',   SCF7E_PLUGIN_PATH  . DIRECTORY_SEPARATOR . 'controller' );
define( 'SCF7E_LIB_PATH', SCF7E_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'lib' );

require_once SCF7E_CONTROLLER_PATH . DIRECTORY_SEPARATOR . 'SCF7EMainController.php';
require_once SCF7E_CONTROLLER_PATH . DIRECTORY_SEPARATOR . 'SCF7EContactController.php';

$main_controller = new SCF7EMainController();