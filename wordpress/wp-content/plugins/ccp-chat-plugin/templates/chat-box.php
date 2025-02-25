<!-- Wrapper container for chat elements -->
<div class="chat-container">
    <!-- Floating Chat Button -->
    <button class="chat-toggle chat-button">
        <svg class="chat-icon" viewBox="0 0 24 24">
            <path d="M4 2h16c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2h-4l-4 4-4-4H4c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2z" />
        </svg>
    </button>

    <!-- Chat Window -->
    <div class="chat-window">
        <div class="chat-header">
            CCP
            <span class="close-chat">✖</span>
        </div>
        <div class="chat-body">
            <!-- Messages will be appended here -->
            <!-- <div class="message incoming">Hello! How can I assist you today?</div>
            <div class="message outgoing">Hello! How can I assist you today?</div> -->
        </div>
        <div class="chat-footer">
            <input type="text" class="chat-input" placeholder="Type a message...">
            <button class="send-button">Send</button>
        </div>
    </div>
</div>
