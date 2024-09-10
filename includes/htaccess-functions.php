<?php
// Security check: Do not allow direct access.
if (!defined('ABSPATH')) {
    exit;
}

// Function to save content to the .htaccess file with enhanced error handling
function wp_htaccess_save_file($content) {
    $htaccess_file = ABSPATH . '.htaccess';

    // Check if the file is writable
    if (!is_writable($htaccess_file)) {
        error_log('Error: The .htaccess file is not writable.');
        echo '<div class="notice notice-error is-dismissible"><p>Error: The .htaccess file is not writable. Please check file permissions.</p></div>';
        return false;
    }

    // Create a backup before saving
    if (file_exists($htaccess_file)) {
        if (!copy($htaccess_file, $htaccess_file . '.backup-' . time())) {
            error_log('Error: Unable to create a backup of the .htaccess file.');
            echo '<div class="notice notice-error is-dismissible"><p>Error: Unable to create a backup of the .htaccess file.</p></div>';
            return false;
        }
    }

    // Save the new content to the .htaccess file
    if (file_put_contents($htaccess_file, $content) === false) {
        error_log('Error: Unable to write to the .htaccess file.');
        echo '<div class="notice notice-error is-dismissible"><p>Error: Unable to save the .htaccess file. Please try again.</p></div>';
        return false;
    }

    echo '<div class="notice notice-success is-dismissible"><p>.htaccess file saved successfully!</p></div>';
    return true;
}
