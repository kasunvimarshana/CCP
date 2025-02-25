<?php
// Ensure WordPress environment is loaded
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function ccp_display($content) {
    if ((is_single() || is_page()) && get_option('ccp_enabled_pages')) {
        $enabled_pages = get_option('ccp_enabled_pages');
        if (in_array(get_the_ID(), (array)$enabled_pages)) {
            $content .= do_shortcode('[ccp_chat_widget]');
        }
    }
    return $content;
}
add_filter('the_content', 'ccp_display');
