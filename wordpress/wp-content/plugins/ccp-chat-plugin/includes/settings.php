<?php
// Ensure WordPress environment is loaded
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// CCP_FIELDS
const CCP_FIELDS = [
    'ccp_title' => ['type' => 'text', 'default' => CCP_DEFAULT_TITLE, 'sanitize' => 'sanitize_text_field'],
    'ccp_api_key' => ['type' => 'text', 'default' => '', 'sanitize' => 'sanitize_text_field'],
    'ccp_api_endpoint' => ['type' => 'text', 'default' => CCP_DEFAULT_API_ENDPOINT, 'sanitize' => 'esc_url_raw'],
    'ccp_enabled_pages' => ['type' => 'array', 'default' => [], 'sanitize' => 'ccp_sanitize_array'],
    'ccp_enable_homepage' => ['type' => 'boolean', 'default' => false, 'sanitize' => 'sanitize_text_field'],
    'ccp_primary_color' => ['type' => 'color', 'default' => CCP_PRIMARY_COLOR, 'sanitize' => 'ccp_sanitize_hex_color'],
    'ccp_primary_hover_color' => ['type' => 'color', 'default' => CCP_PRIMARY_HOVER_COLOR, 'sanitize' => 'ccp_sanitize_hex_color'],
    'ccp_bg_color' => ['type' => 'color', 'default' => CCP_BG_COLOR, 'sanitize' => 'ccp_sanitize_hex_color'],
    'ccp_chat_bg_color' => ['type' => 'color', 'default' => CCP_CHAT_BG_COLOR, 'sanitize' => 'ccp_sanitize_hex_color'],
    'ccp_message_incoming_bg_color' => ['type' => 'color', 'default' => CCP_MESSAGE_INCOMING_BG_COLOR, 'sanitize' => 'ccp_sanitize_hex_color'],
    'ccp_message_outgoing_bg_color' => ['type' => 'color', 'default' => CCP_MESSAGE_OUTGOING_BG_COLOR, 'sanitize' => 'ccp_sanitize_hex_color'],
    'ccp_text_color' => ['type' => 'color', 'default' => CCP_TEXT_COLOR, 'sanitize' => 'ccp_sanitize_hex_color'],
    'ccp_message_incoming_text_color' => ['type' => 'color', 'default' => CCP_MESSAGE_INCOMING_TEXT_COLOR, 'sanitize' => 'ccp_sanitize_hex_color'],
    'ccp_message_outgoing_text_color' => ['type' => 'color', 'default' => CCP_MESSAGE_OUTGOING_TEXT_COLOR, 'sanitize' => 'ccp_sanitize_hex_color'],
    'ccp_message_loading_text_color' => ['type' => 'color', 'default' => CCP_MESSAGE_LOADING_TEXT_COLOR, 'sanitize' => 'ccp_sanitize_hex_color'],
];

// Hook to add settings menu
function ccp_add_settings_menu() {
    add_options_page(
        'Chat Settings', // Page title
        'Chat Settings', // Menu title
        'manage_options', // Capability
        'ccp-settings', // Menu slug
        'ccp_render_settings_page' // Callback function
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
            <?php wp_nonce_field('ccp_reset_defaults', 'ccp_reset_nonce'); ?>
            <input type="hidden" name="ccp_reset_defaults" value="1">
            <?php submit_button('Reset to Default', 'delete'); ?>
        </form>

        <?php
        if (!empty($_POST['ccp_reset_defaults']) && check_admin_referer('ccp_reset_defaults', 'ccp_reset_nonce')) {
            ccp_reset_defaults();
            echo '<div class="updated"><p>Settings reset to default values.</p></div>';
        }
        ?>
    </div>
    <?php
}

/*
function ccp_register_settings() {
    foreach (CCP_FIELDS as $key => $field) {
        register_setting('ccp_settings', $key, [
            'sanitize_callback' => $field['sanitize'],
        ]);

        add_settings_field(
            $key,
            ucwords(str_replace('_', ' ', str_replace('ccp_', '', $key))),
            'ccp_render_field_callback',
            'ccp-settings',
            'ccp_main',
            ['key' => $key, 'type' => $field['type']]
        );
    }

    add_settings_section('ccp_main', 'Main Settings', null, 'ccp-settings');
}

function ccp_render_field_callback($args) {
    $key = $args['key'];
    $type = $args['type'];
    $value = get_option($key, CCP_FIELDS[$key]['default']);

    switch ($type) {
        case 'text':
            echo "<input type='text' name='{$key}' value='" . esc_attr($value) . "' />";
            break;
        case 'color':
            echo "<input type='color' name='{$key}' value='" . esc_attr($value) . "' />";
            break;
        case 'array':
            $pages = get_pages();
            foreach ($pages as $page) {
                $checked = in_array($page->ID, (array) $value) ? 'checked' : '';
                echo "<label><input type='checkbox' name='{$key}[]' value='" . esc_attr($page->ID) . "' $checked> " . esc_html($page->post_title) . "</label><br>";
            }
            break;
        case 'boolean':
            $checked = $value ? 'checked' : '';
            echo "<input type='checkbox' name='{$key}' value='1' $checked> Enable";
            break;
    }
}
*/

function ccp_register_settings() {
    register_setting('ccp_settings', 'ccp_title', ['sanitize_callback' => 'sanitize_text_field']);
    register_setting('ccp_settings', 'ccp_api_key', ['sanitize_callback' => 'sanitize_text_field']);
    register_setting('ccp_settings', 'ccp_api_endpoint', ['sanitize_callback' => 'esc_url_raw']);
    register_setting('ccp_settings', 'ccp_enabled_pages', ['sanitize_callback' => 'ccp_sanitize_array']);
    register_setting('ccp_settings', 'ccp_enable_homepage', ['sanitize_callback' => 'sanitize_text_field']);
    register_setting('ccp_settings', 'ccp_primary_color', ['sanitize_callback' => 'ccp_sanitize_hex_color']);
    register_setting('ccp_settings', 'ccp_primary_hover_color', ['sanitize_callback' => 'ccp_sanitize_hex_color']);
    register_setting('ccp_settings', 'ccp_bg_color', ['sanitize_callback' => 'ccp_sanitize_hex_color']);
    register_setting('ccp_settings', 'ccp_chat_bg_color', ['sanitize_callback' => 'ccp_sanitize_hex_color']);
    register_setting('ccp_settings', 'ccp_message_incoming_bg_color', ['sanitize_callback' => 'ccp_sanitize_hex_color']);
    register_setting('ccp_settings', 'ccp_message_outgoing_bg_color', ['sanitize_callback' => 'ccp_sanitize_hex_color']);
    register_setting('ccp_settings', 'ccp_text_color', ['sanitize_callback' => 'ccp_sanitize_hex_color']);
    register_setting('ccp_settings', 'ccp_message_incoming_text_color', ['sanitize_callback' => 'ccp_sanitize_hex_color']);
    register_setting('ccp_settings', 'ccp_message_outgoing_text_color', ['sanitize_callback' => 'ccp_sanitize_hex_color']);
    register_setting('ccp_settings', 'ccp_message_loading_text_color', ['sanitize_callback' => 'ccp_sanitize_hex_color']);

    add_settings_section('ccp_main', 'Main Settings', null, 'ccp-settings');

    add_settings_field('ccp_title', 'Title', 'ccp_title_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_api_key', 'API Key', 'ccp_api_key_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_api_endpoint', 'API Endpoint', 'ccp_api_endpoint_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_enabled_pages', 'Enable on Pages', 'ccp_enabled_pages_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_enable_homepage', 'Enable on Homepage', 'ccp_enable_homepage_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_primary_color', 'Primary Color', 'ccp_primary_color_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_primary_hover_color', 'Primary Hover Color', 'ccp_primary_hover_color_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_bg_color', 'Background Color', 'ccp_bg_color_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_chat_bg_color', 'Chat Background Color', 'ccp_chat_bg_color_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_message_incoming_bg_color', 'Incoming Background Color', 'ccp_message_incoming_bg_color_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_message_outgoing_bg_color', 'Outgoing Background Color', 'ccp_message_outgoing_bg_color_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_text_color', 'Text Color', 'ccp_text_color_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_message_incoming_text_color', 'Incoming Text Color', 'ccp_message_incoming_text_color_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_message_outgoing_text_color', 'Outgoing Text Color', 'ccp_message_outgoing_text_color_callback', 'ccp-settings', 'ccp_main');
    add_settings_field('ccp_message_loading_text_color', 'Loading Text Color', 'ccp_message_loading_text_color_callback', 'ccp-settings', 'ccp_main');
}
add_action('admin_init', 'ccp_register_settings');

// Sanitize array input
function ccp_sanitize_array($input) {
    return array_map('intval', (array) $input);
}

// Sanitize hex color inputs
function ccp_sanitize_hex_color($color) {
    return preg_match('/^#[a-fA-F0-9]{6}$/', $color) ? $color : '#000000';
}

// Callback for Title input field
function ccp_title_callback() {
    $ccp_title = get_option('ccp_title', CCP_DEFAULT_TITLE);
    echo "<input type='text' name='ccp_title' value='" . esc_attr($ccp_title) . "' />";
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

// Callback for Enabled Pages selection
function ccp_enabled_pages_callback() {
    $enabled_pages = get_option('ccp_enabled_pages', []);
    $pages = get_pages();
    foreach ($pages as $page) {
        $checked = in_array($page->ID, (array) $enabled_pages) ? 'checked' : '';
        echo '<label><input type="checkbox" name="ccp_enabled_pages[]" value="' . esc_attr($page->ID) . '" ' . $checked . '> ' . esc_html($page->post_title) . '</label><br>';
    }
}

// Homepage Enable Toggle
function ccp_enable_homepage_callback() {
    $enable_homepage = get_option('ccp_enable_homepage', false);
    $checked = $enable_homepage ? 'checked' : '';
    echo '<input type="checkbox" name="ccp_enable_homepage" value="1" ' . $checked . '> Enable Chat on Homepage';
}

// Callback for Primary Color input field
function ccp_primary_color_callback() {
    $primary_color = get_option('ccp_primary_color', CCP_PRIMARY_COLOR);
    echo '<input type="color" name="ccp_primary_color" value="' . esc_attr($primary_color) . '" class="color-picker" />';
}

// Callback for Primary Hover Color input field
function ccp_primary_hover_color_callback() {
    $primary_hover_color = get_option('ccp_primary_hover_color', CCP_PRIMARY_HOVER_COLOR);
    echo '<input type="color" name="ccp_primary_hover_color" value="' . esc_attr($primary_hover_color) . '" class="color-picker" />';
}

// Callback for Background Color input field
function ccp_bg_color_callback() {
    $bg_color = get_option('ccp_bg_color', CCP_BG_COLOR);
    echo '<input type="color" name="ccp_bg_color" value="' . esc_attr($bg_color) . '" class="color-picker" />';
}

// Callback for Chat Background Color input field
function ccp_chat_bg_color_callback() {
    $chat_bg_color = get_option('ccp_chat_bg_color', CCP_CHAT_BG_COLOR);
    echo '<input type="color" name="ccp_chat_bg_color" value="' . esc_attr($chat_bg_color) . '" class="color-picker" />';
}

// Callback for Incoming Background Color input field
function ccp_message_incoming_bg_color_callback() {
    $message_incoming_bg_color = get_option('ccp_message_incoming_bg_color', CCP_MESSAGE_INCOMING_BG_COLOR);
    echo '<input type="color" name="ccp_message_incoming_bg_color" value="' . esc_attr($message_incoming_bg_color) . '" class="color-picker" />';
}

// Callback for Outgoing Background Color input field
function ccp_message_outgoing_bg_color_callback() {
    $message_outgoing_bg_color = get_option('ccp_message_outgoing_bg_color', CCP_MESSAGE_OUTGOING_BG_COLOR);
    echo '<input type="color" name="ccp_message_outgoing_bg_color" value="' . esc_attr($message_outgoing_bg_color) . '" class="color-picker" />';
}

// Callback for Text Color input field
function ccp_text_color_callback() {
    $text_color = get_option('ccp_text_color', CCP_TEXT_COLOR);
    echo '<input type="color" name="ccp_text_color" value="' . esc_attr($text_color) . '" class="color-picker" />';
}

// Callback for Incoming Text Color input field
function ccp_message_incoming_text_color_callback() {
    $message_incoming_text_color = get_option('ccp_message_incoming_text_color', CCP_MESSAGE_INCOMING_TEXT_COLOR);
    echo '<input type="color" name="ccp_message_incoming_text_color" value="' . esc_attr($message_incoming_text_color) . '" class="color-picker" />';
}

// Callback for Outgoing Text Color input field
function ccp_message_outgoing_text_color_callback() {
    $message_outgoing_text_color = get_option('ccp_message_outgoing_text_color', CCP_MESSAGE_OUTGOING_TEXT_COLOR);
    echo '<input type="color" name="ccp_message_outgoing_text_color" value="' . esc_attr($message_outgoing_text_color) . '" class="color-picker" />';
}

// Callback for Loading Text Color input field
function ccp_message_loading_text_color_callback() {
    $message_loading_text_color = get_option('ccp_message_loading_text_color', CCP_MESSAGE_LOADING_TEXT_COLOR);
    echo '<input type="color" name="ccp_message_loading_text_color" value="' . esc_attr($message_loading_text_color) . '" class="color-picker" />';
}

// Set default values on plugin activation
function ccp_set_default_options() {
    // add_option('ccp_api_key', CCP_DEFAULT_API_KEY);
    // add_option('ccp_api_endpoint', CCP_DEFAULT_API_ENDPOINT);
    update_option('ccp_enabled_pages', []);
    update_option('ccp_enable_homepage', false);
    update_option('ccp_primary_color', CCP_PRIMARY_COLOR);
    update_option('ccp_primary_hover_color', CCP_PRIMARY_HOVER_COLOR);
    update_option('ccp_bg_color', CCP_BG_COLOR);
    update_option('ccp_chat_bg_color', CCP_CHAT_BG_COLOR);
    update_option('ccp_message_incoming_bg_color', CCP_MESSAGE_INCOMING_BG_COLOR);
    update_option('ccp_message_outgoing_bg_color', CCP_MESSAGE_OUTGOING_BG_COLOR);
    update_option('ccp_text_color', CCP_TEXT_COLOR);
    update_option('ccp_message_incoming_text_color', CCP_MESSAGE_INCOMING_TEXT_COLOR);
    update_option('ccp_message_outgoing_text_color', CCP_MESSAGE_OUTGOING_TEXT_COLOR);
    update_option('ccp_message_loading_text_color', CCP_MESSAGE_LOADING_TEXT_COLOR);

    if (get_option('ccp_title') === false) {
        update_option('ccp_title', CCP_DEFAULT_TITLE);
    }

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
    update_option('ccp_title', CCP_DEFAULT_TITLE);
    update_option('ccp_api_key', CCP_DEFAULT_API_KEY);
    update_option('ccp_api_endpoint', CCP_DEFAULT_API_ENDPOINT);
    update_option('ccp_enabled_pages', []);
    update_option('ccp_enable_homepage', false);
    update_option('ccp_primary_color', CCP_PRIMARY_COLOR);
    update_option('ccp_primary_hover_color', CCP_PRIMARY_HOVER_COLOR);
    update_option('ccp_bg_color', CCP_BG_COLOR);
    update_option('ccp_chat_bg_color', CCP_CHAT_BG_COLOR);
    update_option('ccp_message_incoming_bg_color', CCP_MESSAGE_INCOMING_BG_COLOR);
    update_option('ccp_message_outgoing_bg_color', CCP_MESSAGE_OUTGOING_BG_COLOR);
    update_option('ccp_text_color', CCP_TEXT_COLOR);
    update_option('ccp_message_incoming_text_color', CCP_MESSAGE_INCOMING_TEXT_COLOR);
    update_option('ccp_message_outgoing_text_color', CCP_MESSAGE_OUTGOING_TEXT_COLOR);
    update_option('ccp_message_loading_text_color', CCP_MESSAGE_LOADING_TEXT_COLOR);
}

function ccp_dynamic_styles() {
    $primary_color = get_option('ccp_primary_color', CCP_PRIMARY_COLOR);
    $primary_hover_color = get_option('ccp_primary_hover_color', CCP_PRIMARY_HOVER_COLOR);
    $bg_color = get_option('ccp_bg_color', CCP_BG_COLOR);
    $chat_bg_color = get_option('ccp_chat_bg_color', CCP_CHAT_BG_COLOR);
    $message_incoming_bg_color = get_option('ccp_message_incoming_bg_color', CCP_MESSAGE_INCOMING_BG_COLOR);
    $message_outgoing_bg_color = get_option('ccp_message_outgoing_bg_color', CCP_MESSAGE_OUTGOING_BG_COLOR);
    $text_color = get_option('ccp_text_color', CCP_TEXT_COLOR);
    $message_incoming_text_color = get_option('ccp_message_incoming_text_color', CCP_MESSAGE_INCOMING_TEXT_COLOR);
    $message_outgoing_text_color = get_option('ccp_message_outgoing_text_color', CCP_MESSAGE_OUTGOING_TEXT_COLOR);
    $message_loading_text_color = get_option('ccp_message_loading_text_color', CCP_MESSAGE_LOADING_TEXT_COLOR);

    echo "<style>
        :root {
            /* Primary Colors */
            --ccp-primary: {$primary_color};
            --ccp-primary-hover: {$primary_hover_color};
            
            /* Backgrounds */
            --ccp-bg: {$bg_color};
            --ccp-chat-bg: {$chat_bg_color};
            --ccp-message-incoming-bg: {$message_incoming_bg_color};
            --ccp-message-outgoing-bg: {$message_outgoing_bg_color};

            /* Text Colors */
            --ccp-text: {$text_color};
            --ccp-message-incoming-text: {$message_incoming_text_color};
            --ccp-message-outgoing-text: {$message_outgoing_text_color};
            --ccp-message-loading-text: {$message_loading_text_color};
        }
    </style>";
}

add_action('wp_head', 'ccp_dynamic_styles');

// Helper function to adjust color brightness
function ccp_adjust_brightness($hex, $steps) {
    $steps = max(-255, min(255, $steps));
    $hex = str_replace('#', '', $hex);
    
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }
    
    $r = hexdec(substr($hex,0,2));
    $g = hexdec(substr($hex,2,2));
    $b = hexdec(substr($hex,4,2));
    
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));
    
    return '#'.dechex(($r<<16)|($g<<8)|$b);
}