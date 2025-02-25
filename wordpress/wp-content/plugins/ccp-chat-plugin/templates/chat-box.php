<!-- <div id="ccp-chat-widget" class="ccp-chat-widget">
    <div id="ccp-chat-header" class="ccp-chat-header">Chat with Us</div>
    <div id="ccp-chat-messages" class="ccp-chat-messages"></div>
    <input id="ccp-chat-input" type="text" class="ccp-chat-input" placeholder="Type your message...">
    <button id="ccp-close-chat" class="ccp-close-chat">Close</button>
</div> -->

<!-- ================================ -->
<!-- Floating Chat Button -->
<button id="chat-toggle" class="chat-button">
    <svg class="chat-icon" viewBox="0 0 24 24">
        <path d="M4 2h16c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2h-4l-4 4-4-4H4c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2z" />
    </svg>
</button>

<!-- Chat Window -->
<div id="chat-window" class="chat-window">
    <div class="chat-header">
        CCP
        <span id="close-chat" class="close-chat">✖</span>
    </div>
    <div class="chat-body">
        <!-- <div class="message incoming">Hello! How can I assist you today?</div>
        <div class="message outgoing">Hello! How can I assist you today?</div> -->
    </div>
    <div class="chat-footer">
        <input type="text" id="chat-input" placeholder="Type a message...">
        <button id="send-button">Send</button>
    </div>
</div>