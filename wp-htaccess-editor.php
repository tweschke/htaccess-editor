<?php
/*
Plugin Name: WP .htaccess Editor
Plugin URI: https://yourwebsite.com
Description: A simple plugin to edit the .htaccess file from the WordPress admin area.
Version: 1.0
Author: Your Name
Author URI: https://yourwebsite.com
License: GPL2
*/

// Security check: Do not allow direct access to this file.
if (!defined('ABSPATH')) {
    exit;
}

// Load additional functions (like wp_htaccess_get_file_content)
require_once(plugin_dir_path(__FILE__) . 'includes/htaccess-functions.php');

// Load the admin page
require_once(plugin_dir_path(__FILE__) . 'admin/htaccess-editor-page.php');
