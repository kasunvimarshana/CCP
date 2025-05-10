<?php
    $chat_title = get_option('ccp_title', CCP_DEFAULT_TITLE);
?>

<!-- Wrapper container for chat elements -->
<div class="ccp-chat-container">
    <!-- Floating Chat Button -->
    <button class="ccp-chat-toggle ccp-chat-button">
        <svg class="ccp-chat-icon" viewBox="0 0 24 24">
            <path d="M4 2h16c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2h-4l-4 4-4-4H4c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2z" />
        </svg>
    </button>

    <!-- Chat Window -->
    <div class="ccp-chat-window">
        <div class="ccp-chat-header">
            <?php echo esc_html($chat_title); ?>
            <span class="ccp-close-chat">✖</span>
        </div>
        <div class="ccp-chat-body">
            <!-- Messages will be appended here -->
            <!-- <div class="message incoming">Hello! How can I assist you today?</div>
            <div class="message outgoing">Hello! How can I assist you today?</div> -->
        </div>
        <div class="ccp-chat-footer">
            <input type="text" class="ccp-chat-input" placeholder="Type a message...">
            <button class="ccp-send-button">Send</button>
        </div>
    </div>
</div>
