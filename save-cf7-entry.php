<?php
/*
Plugin Name: Save CF7 Entery 
Plugin URI: https://contactform7.com/
Description: This is a plugin that saves contact form 7 entries into the database
Author: Noman Akram
Text Domain: save-cf7-entry
Version: 1.0.0
*/

define( 'SCF7E_VERSION', '1.0.0' );

define( 'SCF7E_PLUGIN_PATH', dirname( __FILE__ ) );

define( 'SCF7E_PLUGIN_BASENAME',  basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

define( 'SCF7E_CONTROLLER_PATH',   SCF7E_PLUGIN_PATH  . DIRECTORY_SEPARATOR . 'controller' );



require_once SCF7E_CONTROLLER_PATH .
    DIRECTORY_SEPARATOR .
    'MainController.php';

// ==========================================================================
// = All app initialization is done in Main_Controller __constructor. =
// ==========================================================================
$main_controller = new MainController();