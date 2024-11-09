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

// Enqueue CodeMirror for syntax highlighting
function wp_htaccess_enqueue_codemirror() {
    wp_enqueue_script('codemirror', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js', array(), null, true);
    wp_enqueue_script('codemirror-mode-apache', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/apache/apache.min.js', array('codemirror'), null, true);
    wp_enqueue_style('codemirror-css', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css');
}
add_action('admin_enqueue_scripts', 'wp_htaccess_enqueue_codemirror');

// Display the .htaccess editor page
function wp_htaccess_editor_page() {
    if (!current_user_can('manage_options')) {
        return; // Check if the user has permission to manage settings
    }

    // Handle form submission for saving .htaccess file
    if (isset($_POST['htaccess_content'])) {
        check_admin_referer('save_htaccess', 'htaccess_nonce'); // Nonce security check

        $htaccess_content = stripslashes($_POST['htaccess_content']); // Get the posted content

        // Save the .htaccess file
        $result = wp_htaccess_save_file($htaccess_content);
    }

    // Handle form submission for restoring from a backup
    if (isset($_POST['restore_backup'])) {
        check_admin_referer('restore_htaccess', 'restore_nonce'); // Nonce security check

        $backup_file = sanitize_text_field($_POST['backup_file']);
        wp_htaccess_restore_backup($backup_file);
    }

    // Load the .htaccess content
    $htaccess_content = wp_htaccess_get_file_content();

    // List available backups
    $backup_files = wp_htaccess_list_backups();

    ?>
    <div class="wrap">
        <h1>Edit .htaccess File</h1>
        <p><strong>Note:</strong> Be cautious while editing the .htaccess file, as incorrect configurations can break your site.</p>

        <!-- Form to edit .htaccess file -->
        <form method="post">
            <?php wp_nonce_field('save_htaccess', 'htaccess_nonce'); // Nonce for security ?>
            <textarea id="htaccess-editor" name="htaccess_content" rows="20" style="width:100%;"><?php echo esc_textarea($htaccess_content); ?></textarea>
            <br><br>
            <input type="submit" class="button-primary" value="Save Changes">
        </form>

        <?php if (!empty($backup_files)) { ?>
            <h2>Restore from Backup</h2>
            <!-- Form to restore from a backup -->
            <form method="post">
                <?php wp_nonce_field('restore_htaccess', 'restore_nonce'); // Nonce for security ?>
                <select name="backup_file">
                    <?php foreach ($backup_files as $backup) { ?>
                        <option value="<?php echo esc_attr($backup); ?>"><?php echo esc_html(basename($backup)); ?></option>
                    <?php } ?>
                </select>
                <br><br>
                <input type="submit" name="restore_backup" class="button-secondary" value="Restore Backup">
            </form>
        <?php } ?>
    </div>

    <!-- CodeMirror initialization -->
    <script>
        jQuery(document).ready(function($) {
            var editor = CodeMirror.fromTextArea(document.getElementById('htaccess-editor'), {
                lineNumbers: true,
                mode: 'apache',
                theme: 'default'
            });
        });
    </script>
    <?php
}

// Function to list available backups
function wp_htaccess_list_backups() {
    $htaccess_file = ABSPATH . '.htaccess';
    $backup_files = glob($htaccess_file . '.backup-*');
    
    if (!empty($backup_files)) {
        return $backup_files;
    } else {
        return array();
    }
}

// Function to restore a backup
function wp_htaccess_restore_backup($backup_file) {
    $htaccess_file = ABSPATH . '.htaccess';

    if (file_exists($backup_file)) {
        copy($backup_file, $htaccess_file);
        echo '<div class="notice notice-success is-dismissible"><p>.htaccess file restored from backup!</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>Error: Backup file not found.</p></div>';
    }
}

// Function to save the .htaccess file
function wp_htaccess_save_file($content) {
    $htaccess_file = ABSPATH . '.htaccess';

    if (!is_writable($htaccess_file)) {
        add_settings_error('htaccess_editor', 'htaccess_error', 'Error: The .htaccess file is not writable. Please check file permissions.', 'error');
        return false;
    }

    if (file_exists($htaccess_file)) {
        copy($htaccess_file, $htaccess_file . '.backup-' . time());
    }

    if (file_put_contents($htaccess_file, $content) === false) {
        add_settings_error('htaccess_editor', 'htaccess_error', 'Error: Unable to save the .htaccess file. Please try again.', 'error');
        return false;
    }

    add_settings_error('htaccess_editor', 'htaccess_success', '.htaccess file saved successfully!', 'updated');
    return true;
}
?>
