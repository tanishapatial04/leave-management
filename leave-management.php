<?php
/*
Plugin Name: Leave Management
Description: Simple plugin to manage employee leave requests from frontend and allow HR to view and manage them.
Version: 1.1
Author: Dev.Tanisha 
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('ELM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ELM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once ELM_PLUGIN_PATH . 'includes/database.php';
require_once ELM_PLUGIN_PATH . 'includes/shortcodes.php';
require_once ELM_PLUGIN_PATH . 'includes/admin.php';
require_once ELM_PLUGIN_PATH . 'includes/utilities.php';

// Register activation hook
register_activation_hook(__FILE__, 'elm_create_leave_table');