<?php
// Security check: Do not allow direct access.
if (!defined('ABSPATH')) {
    exit;
}

// Add the .htaccess editor page to the admin menu
function wp_htaccess_editor_menu() {
    add_menu_page(
        '.htaccess Editor',   // Page title
        '.htaccess Editor',   // Menu title
        'manage_options',     // Capability
        'htaccess-editor',    // Menu slug
        'wp_htaccess_editor_page', // Function to display content
        'dashicons-editor-code',   // Icon
        100                      // Position
    );
}
add_action('admin_menu', 'wp_htaccess_editor_menu');

// Display the .htaccess editor page
function wp_htaccess_editor_page() {
    if (!current_user_can('manage_options')) {
        return; // Check if the user has permission to manage settings
    }

    // Handle form submission
    if (isset($_POST['htaccess_content'])) {
        check_admin_referer('save_htaccess', 'htaccess_nonce'); // Nonce security check

        $htaccess_content = stripslashes($_POST['htaccess_content']); // Get the posted content

        // Save the .htaccess file
        wp_htaccess_save_file($htaccess_content);
        echo '<div class="notice notice-success is-dismissible"><p>.htaccess file saved successfully!</p></div>';
    }

    // Load the .htaccess content
    $htaccess_content = wp_htaccess_get_file_content();

    ?>
    <div class="wrap">
        <h1>Edit .htaccess File</h1>
        <form method="post">
            <?php wp_nonce_field('save_htaccess', 'htaccess_nonce'); // Nonce for security ?>
            <textarea name="htaccess_content" rows="20" style="width:100%;"><?php echo esc_textarea($htaccess_content); ?></textarea>
            <br><br>
            <input type="submit" class="button-primary" value="Save Changes">
        </form>
    </div>
    <?php
}
