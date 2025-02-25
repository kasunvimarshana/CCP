<?php
// Ensure WordPress environment is loaded
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Handle AJAX Request to Send Chat Message
function ccp_send_chat_message() {
    $user_id = get_current_user_id() ? get_current_user_id() : session_id();
    $api_endpoint = get_option('ccp_api_endpoint');
    $api_key = get_option('ccp_api_key');

    // Check if message is set
    if ( isset( $_POST['message'] ) && ! empty( $_POST['message'] ) ) {
        // Sanitize and process the message
        $message = sanitize_text_field($_POST['message']);

        $body = [
            'conversationId'  => 1,
            'chatRequest' => $message,
            'userId' => $user_id,
            'apiKey' => $api_key
        ];

        $body = wp_json_encode( $body );

        $options = [
            'body'        => $body,
            'headers'     => [
                'Content-Type' => 'application/json',
                // 'Authorization' => 'Bearer ' . $api_key,
            ],
            'data_format' => 'body',
        ];

        $response = wp_remote_post( $api_endpoint, $options );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            wp_send_json_error( array( 'message' => $error_message ) );
        } else {
            $responseBody = json_decode( wp_remote_retrieve_body( $response ) );
            wp_send_json_success( array( 'data' => $responseBody ) );
        }
    } else {
        wp_send_json_error(array('message' => 'No message received.'));
    }
}
add_action('wp_ajax_ccp_send_chat_message', 'ccp_send_chat_message');
add_action('wp_ajax_nopriv_ccp_send_chat_message', 'ccp_send_chat_message');
