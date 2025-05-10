<?php
/**
* CCP
*
* @package CCP
* @author Kasun Vimarshana
* @license gplv2-or-later
* @version 1.0.0
*
* @wordpress-plugin
* Plugin Name: Custom Chat Plugin
* Plugin URI: https://github.com/kasunvimarshana/
* Description: A live chat plugin.
* Version: 1.0.1
* Author: Kasun Vimarshana
* Author URI: https://github.com/kasunvimarshana/
* License: GPLv2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
*
*/

// Ensure WordPress environment is loaded
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define default values
define( 'CCP_VERSION', '1.0.0' );
define( 'CCP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'CCP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'CCP_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'CCP_NAME', 'Custom Chat Plugin' );
define( 'CCP_DEFAULT_API_BASE_URL', 'http://triagezone.com' );
define( 'CCP_DEFAULT_API_KEY', '' );
define( 'CCP_PLUGIN_VERSION', '1.0' );
define( 'CCP_DEFAULT_TITLE', 'CCP' );
// theme
define( 'CCP_PRIMARY_COLOR', '#007bff' );
define( 'CCP_PRIMARY_HOVER_COLOR', '#0056b3' );
define( 'CCP_BG_COLOR', '#ffffff' );
define( 'CCP_CHAT_BG_COLOR', '#f9f9f9' );
define( 'CCP_MESSAGE_INCOMING_BG_COLOR', '#e0e0e0' );
define( 'CCP_MESSAGE_OUTGOING_BG_COLOR', '#e0e0e0' );
define( 'CCP_TEXT_COLOR', '#ffffff' );
define( 'CCP_MESSAGE_INCOMING_TEXT_COLOR', '#000000' );
define( 'CCP_MESSAGE_OUTGOING_TEXT_COLOR', '#ffffff' );
define( 'CCP_MESSAGE_LOADING_TEXT_COLOR', '#000000' );

require_once CCP_PLUGIN_DIR_PATH . 'includes/settings.php';
require_once CCP_PLUGIN_DIR_PATH . 'includes/ajax-handler.php';
require_once CCP_PLUGIN_DIR_PATH . 'includes/functions.php';

// Enqueue Scripts and Styles
function ccp_enqueue_assets() {
    // Ensure jQuery is loaded
    if (!wp_script_is('jquery', 'enqueued')) {
        wp_enqueue_script('jquery'); // Enqueue WordPress default jQuery
    }

    // load a newer version of jQuery
    // wp_enqueue_script('jquery-core', 'https://code.jquery.com/jquery-3.6.0.min.js', array(), '3.6.0', true);

    // Enqueue CSS for the chat widget
    wp_enqueue_style('ccp-chat-style', CCP_PLUGIN_DIR_URL . 'assets/css/chat-style.css');

    // Enqueue JavaScript for the chat functionality
    wp_enqueue_script('ccp-chat-script', CCP_PLUGIN_DIR_URL . 'assets/js/chat-script.js', array('jquery'), null, true);

    // Localize script to pass AJAX URL to JavaScript
    wp_localize_script('ccp-chat-script', 'ccp_ajax_obj', array(
        'nonce' => wp_create_nonce('ccp_chat_nonce'),
        'ajax_url' => admin_url('admin-ajax.php'),
        'api_key' => get_option('ccp_api_key'),
        'event_id' => get_option('ccp_default_event_id', 1)
    ));
}
add_action('wp_enqueue_scripts', 'ccp_enqueue_assets');

// Display the Chat Widget
function ccp_chat_widget() {
    // ob_start();
    include CCP_PLUGIN_DIR_PATH . 'templates/chat-box.php';
    // $output = ob_get_clean();
    // return $output;
}
add_shortcode('ccp_chat_widget', 'ccp_chat_widget');

// Plugin activation hook
function ccp_chat_activate() {
    // Activation logic
}
register_activation_hook(__FILE__, 'ccp_chat_activate');

// Plugin deactivation hook
function ccp_chat_deactivate() {
    // Deactivation logic
}
register_deactivation_hook(__FILE__, 'ccp_chat_deactivate');