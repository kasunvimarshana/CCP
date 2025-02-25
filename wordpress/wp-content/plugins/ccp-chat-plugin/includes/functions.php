<?php
// Ensure WordPress environment is loaded
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Append the chat widget shortcode to the content if conditions are met.
 *
 * @param string $content The existing content of the post/page.
 * @return string Modified content with the chat widget appended if applicable.
 */
function ccp_display($content) {
    $enabled_pages = get_option('ccp_enabled_pages');
    $enable_homepage = get_option('ccp_enable_homepage', false);

    // Ensure $enabled_pages is always an array
    $enabled_pages = is_array($enabled_pages) ? $enabled_pages : [];

    // Prevent multiple instances of the chat widget from being added
    if (strpos($content, '[ccp_chat_widget]') !== false) {
        return $content;
    }

    // Check if the current page is a single post, a page, or the blog index page
    if (is_single() || is_page() || is_home()) {
        // Append the chat widget if the current page ID is in the enabled list
        if (in_array(get_the_ID(), (array) $enabled_pages, true)) {
            $content .= do_shortcode('[ccp_chat_widget]');
            return $content;
        }
    }

    // Append the chat widget if the homepage setting is enabled and the current page is the homepage
    if (((is_front_page() || is_home()) && $enable_homepage)) {
        $content .= do_shortcode('[ccp_chat_widget]');
        return $content;
    }

    // Return the original content if no conditions match
    return $content;
}
add_filter('the_content', 'ccp_display');
