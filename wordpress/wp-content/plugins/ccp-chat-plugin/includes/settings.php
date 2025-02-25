<?php
// Ensure WordPress environment is loaded
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define default values
define('CCP_DEFAULT_API_ENDPOINT', 'http://triagezone.com/api/chats/send-message');
define('CCP_DEFAULT_API_KEY', '');

function ccp_add_settings_menu() {
    add_menu_page(
        'Chat Settings', 
        'Chat Settings', 
        'manage_options', 
        'ccp-settings', 
        'ccp_render_settings_page'
    );
}
add_action('admin_menu', 'ccp_add_settings_menu');

function ccp_render_settings_page() {
    ?>
    <div class="wrap">
        <h2>Chat Plugin Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('ccp_settings');
            do_settings_sections('ccp-settings');
            submit_button();
            ?>
        </form>

        <form method="post">
            <input type="hidden" name="ccp_reset_defaults" value="1">
            <?php submit_button('Reset to Default', 'delete'); ?>
        </form>

        <?php
        if (isset($_POST['ccp_reset_defaults'])) {
            ccp_reset_defaults();
            echo '<div class="updated"><p>Settings reset to default values.</p></div>';
        }
        ?>
    </div>
    <?php
}

function ccp_register_settings() {
    register_setting('ccp_settings', 'ccp_api_key', ['sanitize_callback' => 'sanitize_text_field']);
    register_setting('ccp_settings', 'ccp_api_endpoint', ['sanitize_callback' => 'esc_url_raw']);
    register_setting('ccp_settings', 'ccp_enabled_pages', ['sanitize_callback' => 'ccp_sanitize_array']);

    add_settings_section('ccp_main', 'Main Settings', null, 'ccp-settings');

    add_settings_field('ccp_api_key', 'API Key', 'ccp_api_key_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_api_endpoint', 'API Endpoint', 'ccp_api_endpoint_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_enabled_pages', 'Enable on Pages', 'ccp_enabled_pages_callback', 'ccp-settings', 'ccp_main');
}
add_action('admin_init', 'ccp_register_settings');

// Sanitize array input
function ccp_sanitize_array($input) {
    return array_map('intval', (array) $input);
}

// Callback for API key input field
function ccp_api_key_callback() {
    $api_key = get_option('ccp_api_key', CCP_DEFAULT_API_KEY);
    echo "<input type='text' name='ccp_api_key' value='" . esc_attr($api_key) . "' />";
}

// Callback for API endpoint input field
function ccp_api_endpoint_callback() {
    $api_endpoint = get_option('ccp_api_endpoint', CCP_DEFAULT_API_ENDPOINT);
    echo "<input type='text' name='ccp_api_endpoint' value='" . esc_attr($api_endpoint) . "' />";
}

// Callback for API enabled pages field
// Callback for Enabled Pages selection
function ccp_enabled_pages_callback() {
    $enabled_pages = get_option('ccp_enabled_pages', []);
    $pages = get_pages();
    foreach ($pages as $page) {
        $checked = in_array($page->ID, (array) $enabled_pages) ? 'checked' : '';
        echo '<label><input type="checkbox" name="ccp_enabled_pages[]" value="' . esc_attr($page->ID) . '" ' . $checked . '> ' . esc_html($page->post_title) . '</label><br>';
    }
}

// Set default values on plugin activation
function ccp_set_default_options() {
    // add_option('ccp_api_key', CCP_DEFAULT_API_KEY);
    // add_option('ccp_api_endpoint', CCP_DEFAULT_API_ENDPOINT);
    add_option('ccp_enabled_pages', []);

    if (get_option('ccp_api_key') === false) {
        update_option('ccp_api_key', CCP_DEFAULT_API_KEY);
    }

    if (get_option('ccp_api_endpoint') === false) {
        update_option('ccp_api_endpoint', CCP_DEFAULT_API_ENDPOINT);
    }
}
register_activation_hook(__FILE__, 'ccp_set_default_options');

// Reset settings to default
function ccp_reset_defaults() {
    update_option('ccp_api_key', CCP_DEFAULT_API_KEY);
    update_option('ccp_api_endpoint', CCP_DEFAULT_API_ENDPOINT);
    update_option('ccp_enabled_pages', []);
}