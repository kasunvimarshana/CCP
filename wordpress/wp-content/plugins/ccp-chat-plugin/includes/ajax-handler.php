<?php
// Ensure WordPress environment is loaded
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Register all AJAX handlers for the contact verification system
 */
function ccp_register_verification_handlers() {
    // chat
    add_action('wp_ajax_ccp_send_chat_message', 'ccp_send_chat_message');
    add_action('wp_ajax_nopriv_ccp_send_chat_message', 'ccp_send_chat_message');

    // Check if contact is verified universally
    add_action('wp_ajax_ccp_check_verification_status', 'ccp_check_verification_status');
    add_action('wp_ajax_nopriv_ccp_check_verification_status', 'ccp_check_verification_status');
    
    // Send OTP for verification
    add_action('wp_ajax_ccp_send_otp', 'ccp_send_otp');
    add_action('wp_ajax_nopriv_ccp_send_otp', 'ccp_send_otp');
    
    // Validate OTP
    add_action('wp_ajax_ccp_verify_otp', 'ccp_verify_otp');
    add_action('wp_ajax_nopriv_ccp_verify_otp', 'ccp_verify_otp');

    // Validate OTP
    add_action('wp_ajax_ccp_start_chat', 'ccp_start_chat');
    add_action('wp_ajax_nopriv_ccp_start_chat', 'ccp_start_chat');

    // Validate OTP
    add_action('wp_ajax_ccp_get_chat_score', 'ccp_get_chat_score');
    add_action('wp_ajax_nopriv_ccp_get_chat_score', 'ccp_get_chat_score');
}
add_action('init', 'ccp_register_verification_handlers');

// Handle AJAX Request to Send Chat Message
function ccp_send_chat_message() {
    // check_ajax_referer('ccp_chat_nonce', 'security');
    $user_id = get_current_user_id() ?: session_id();
    $api_base_url = get_option('ccp_api_base_url');
    $api_key = get_option('ccp_api_key');

    $message = ( isset( $_POST['message'] ) && ! empty( $_POST['message'] ) ) ? sanitize_text_field( $_POST['message'] ) : '';

    if ( empty( $message ) ) {
        wp_send_json_error( [ 'message' => 'No message received.' ] );
    }

    $conversation_id = ( isset( $_POST['conversationId'] ) && is_numeric( $_POST['conversationId'] ) ) ? intval( $_POST['conversationId'] ) : 1;

    $body = [
        'conversationId'  => $conversation_id,
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
        'timeout' => PHP_INT_MAX,
    ];

    $endpoint = trailingslashit( $api_base_url )."api/chats/send-message";

    $response = wp_remote_post( $endpoint, $options );

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        wp_send_json_error( [ 'message' => $error_message ] );
    }

    // $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode( wp_remote_retrieve_body( $response ) );
    wp_send_json_success( [ 'data' => $response_body ] );
}

// Verify Contact Status Handler
function ccp_check_verification_status() {
    // check_ajax_referer('ccp_chat_nonce', 'security');

    $api_base_url = get_option('ccp_api_base_url');
    $api_key = get_option('ccp_api_key');

    $contact_value = isset($_POST['contactValue']) ? sanitize_text_field($_POST['contactValue']) : '';
    $contact_type = isset($_POST['contactType']) ? sanitize_text_field($_POST['contactType']) : '';

    if (empty($contact_value) || empty($contact_type)) {
        wp_send_json_error(['message' => 'Missing required parameters']);
    }

    $options = [
        'headers'     => [
            'Content-Type' => 'application/json',
            // 'Authorization' => 'Bearer ' . $api_key,
        ],
        'timeout' => PHP_INT_MAX,
    ];

    $endpoint = trailingslashit( $api_base_url )."api/contact-verification/is-verified-universally/".urlencode( $contact_value )."?contactType=".$contact_type;

    $response = wp_remote_get( $endpoint, $options );

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        wp_send_json_error( [ 'message' => $error_message ] );
    }

    // $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode( wp_remote_retrieve_body( $response ) );
    wp_send_json_success( [ 'data' => $response_body ] );
}

// Send OTP Handler
function ccp_send_otp() {
    // check_ajax_referer('ccp_chat_nonce', 'security');

    $api_base_url = get_option('ccp_api_base_url');
    $api_key = get_option('ccp_api_key');

    $contact_value = isset($_POST['contactValue']) ? sanitize_text_field($_POST['contactValue']) : '';
    $contact_type = isset($_POST['contactType']) ? sanitize_text_field($_POST['contactType']) : '';
    $event_id = isset($_POST['eventId']) ? intval($_POST['eventId']) : 1;

    if (empty($contact_value) || empty($contact_type)) {
        wp_send_json_error(['message' => 'Missing required parameters']);
    }

    $body = [];

    $body = wp_json_encode( $body );

    $options = [
        'body'        => $body,
        'headers'     => [
            'Content-Type' => 'application/json',
            // 'Authorization' => 'Bearer ' . $api_key,
        ],
        'data_format' => 'body',
        'timeout' => PHP_INT_MAX,
    ];

    $endpoint = trailingslashit( $api_base_url ) ."api/contact-verification/events/".urlencode( $event_id )."/send-otp/".urlencode( $contact_value )."?contactType=".$contact_type;

    $response = wp_remote_post( $endpoint, $options );

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        wp_send_json_error( [ 'message' => $error_message ] );
    }

    // $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode( wp_remote_retrieve_body( $response ) );
    wp_send_json_success( [ 'data' => $response_body ] );
}

// Verify OTP Handler
function ccp_verify_otp() {
    // check_ajax_referer('ccp_chat_nonce', 'security');

    $api_base_url = get_option('ccp_api_base_url');
    $api_key = get_option('ccp_api_key');

    $contact_type = isset($_POST['contactType']) ? sanitize_text_field($_POST['contactType']) : '';
    $contact_value = isset($_POST['contactValue']) ? sanitize_text_field($_POST['contactValue']) : '';
    $event_id = isset($_POST['eventId']) ? intval($_POST['eventId']) : 0;
    $otp = isset($_POST['otp']) ? intval($_POST['otp']) : 0;

    if (empty($contact_value) || empty($contact_type)) {
        wp_send_json_error(['message' => 'Missing required parameters']);
    }

    $body = [
        'contactValue' => $contact_value,
        'otp' => $otp
    ];

    $body = wp_json_encode( $body );

    $options = [
        'body'        => $body,
        'headers'     => [
            'Content-Type' => 'application/json',
            // 'Authorization' => 'Bearer ' . $api_key,
        ],
        'data_format' => 'body',
        'timeout' => PHP_INT_MAX,
    ];

    $endpoint = trailingslashit( $api_base_url )."api/contact-verification/events/".urlencode( $event_id )."/validate-otp?contactType=".$contact_type;

    $response = wp_remote_post( $endpoint, $options );

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        wp_send_json_error( [ 'message' => $error_message ] );
    }

    // $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode( wp_remote_retrieve_body( $response ) );
    wp_send_json_success( [ 'data' => $response_body ] );
}

// Start Chat Handler
function ccp_start_chat() {
    // check_ajax_referer('ccp_chat_nonce', 'security');

    $api_base_url = get_option('ccp_api_base_url');
    $api_key = get_option('ccp_api_key');

    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? preg_replace('/[^0-9+]/', '', $_POST['phone'] ?? '') : '';
    $consent = isset($_POST['terms']) ? (bool)$_POST['terms'] : false;
    $event_id = isset($_POST['eventId']) ? intval($_POST['eventId']) : 0;

    $errors = [];
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    if (!is_email($email)) {
        $errors[] = 'Valid email is required';
    }
    if (!$consent) {
        $errors[] = 'Consent is required';
    }

    if (!empty($errors)) {
        wp_send_json_error(['message' => implode(', ', $errors)]);
    }

    $body = [
        'name' => $name,
        'email' => $email,
        'contactNumber' => $phone,
        'consent' => $consent,
        'eventId' => $event_id
    ];

    $body = wp_json_encode( $body );

    $options = [
        'body'        => $body,
        'headers'     => [
            'Content-Type' => 'application/json',
            // 'Authorization' => 'Bearer ' . $api_key,
        ],
        'data_format' => 'body',
        'timeout' => PHP_INT_MAX,
    ];

    $endpoint = trailingslashit( $api_base_url )."api/chats/smart-gpt/visitor-interaction";

    $response = wp_remote_post( $endpoint, $options );

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        wp_send_json_error( [ 'message' => $error_message ] );
    }

    // $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode( wp_remote_retrieve_body( $response ) );
    wp_send_json_success( [ 'data' => $response_body ] );
}

// Get Chat Score Handler
function ccp_get_chat_score() {
    // check_ajax_referer('ccp_chat_nonce', 'security');

    $api_base_url = get_option('ccp_api_base_url');
    $api_key = get_option('ccp_api_key');

    $visitor_id = isset($_POST['visitorId']) ? intval($_POST['visitorId']) : 0;
    $event_id = isset($_POST['eventId']) ? intval($_POST['eventId']) : 0;

    if (empty($visitor_id) || empty($event_id)) {
        wp_send_json_error(['message' => 'Missing required parameters']);
    }

    $options = [
        'headers'     => [
            'Content-Type' => 'application/json',
            // 'Authorization' => 'Bearer ' . $api_key,
        ],
        'timeout' => PHP_INT_MAX,
    ];

    $endpoint = trailingslashit( $api_base_url )."api/insights/chat-score/".urlencode( $event_id )."/".urlencode( $visitor_id );

    $response = wp_remote_get( $endpoint, $options );

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        wp_send_json_error( [ 'message' => $error_message ] );
    }

    // $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode( wp_remote_retrieve_body( $response ) );
    wp_send_json_success( [ 'data' => $response_body ] );
}
