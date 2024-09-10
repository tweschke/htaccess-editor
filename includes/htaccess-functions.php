<?php
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
        return '';
    }
}

// Function to save content to the .htaccess file
function wp_htaccess_save_file($content) {
    $htaccess_file = ABSPATH . '.htaccess';

    // Check if the file is writable
    if (!is_writable($htaccess_file)) {
        echo '<div class="notice notice-error is-dismissible"><p>Error: The .htaccess file is not writable. Please check file permissions.</p></div>';
        return false;
    }

    // Create a backup before saving
    if (file_exists($htaccess_file)) {
        copy($htaccess_file, $htaccess_file . '.backup-' . time());
    }

    // Save the new content
    if (file_put_contents($htaccess_file, $content) === false) {
        echo '<div class="notice notice-error is-dismissible"><p>Error: Unable to save the .htaccess file. Please try again.</p></div>';
        return false;
    }

    echo '<div class="notice notice-success is-dismissible"><p>.htaccess file saved successfully!</p></div>';
    return true;
}
