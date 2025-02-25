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

define( 'CCP_VERSION', '1.0.0' );
define( 'CCP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'CCP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'CCP_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'CCP_NAME', 'Custom Chat Plugin' );

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
        'ajax_url' => admin_url('admin-ajax.php'),
        'api_key' => get_option('ccp_api_key'),
    ));
}
add_action('wp_enqueue_scripts', 'ccp_enqueue_assets');

// Display the Chat Widget
function ccp_chat_widget() {
    include CCP_PLUGIN_DIR_PATH . 'templates/chat-box.php';
}
add_shortcode('ccp_chat_widget', 'ccp_chat_widget');
