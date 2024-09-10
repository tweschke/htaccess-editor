<?php

error_log('htaccess-functions.php loaded successfully');


// Security check: Do not allow direct access.
if (!defined('ABSPATH')) {
    exit;
}

// Function to get the content of the .htaccess file
function wp_htaccess_get_file_content() {
    $htaccess_file = ABSPATH . '.htaccess';

    // Check if the file exists
    if (file_exists($htaccess_file)) {
        return file_get_contents($htaccess_file);
    } else {
        return ''; // Return an empty string if the file doesn't exist
    }
}
