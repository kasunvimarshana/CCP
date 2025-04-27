jQuery(document).ready(function($) {
    // Wrap all selections inside a parent container
    const $chatContainer = $(".ccp-chat-container");

    // Select elements within the container using find()
    const $chatWindow = $chatContainer.find(".ccp-chat-window");
    const $chatToggle = $chatContainer.find(".ccp-chat-toggle");
    const $chatInput = $chatContainer.find(".ccp-chat-input");
    const $chatBody = $chatContainer.find(".ccp-chat-body");
    const $sendButton = $chatContainer.find(".ccp-send-button");
    const $closeChat = $chatContainer.find(".ccp-close-chat");

    function toggleChat() {
        $chatWindow.fadeToggle(300).toggleClass("active");
    }

    function closeChat() {
        $chatWindow.fadeOut(300).removeClass("active");
    }

    function showLoading() {
        const loading = `<div class="ccp-message loading">...</div>`;
        $chatBody.append(loading);
        $chatBody.scrollTop($chatBody.prop("scrollHeight"));
    }

    function removeLoading() {
        $chatBody.find(".ccp-message.loading").remove();
    }

    function appendMessage(text, type) {
        if (text) {
            const message = `<div class="ccp-message ${type}">${text}</div>`;
            $chatBody.append(message);
            $chatBody.scrollTop($chatBody.prop("scrollHeight"));
        }
    }

    function sendMessage() {
        const messageText = $chatInput.val().trim();
        if (!messageText) {
            return;
        }

        appendMessage(messageText, "outgoing");
        $chatInput.val("");

        showLoading();

        $.ajax({
            url: ccp_ajax_obj.ajax_url,
            method: "POST",
            data: {
                action: "ccp_send_chat_message",
                message: messageText
            },
            success: function(response) {
                removeLoading();

                if (response.success) {
                    const messageText = response.data?.data?.payload?.[0];
                    appendMessage(messageText, "incoming");
                }
            },
            error: function() {
                removeLoading();
                
                console.error("Failed to send message.");
            }
        });
    }

    // Attach events to elements using class names and find()
    $chatToggle.click(toggleChat);
    $closeChat.click(closeChat);
    
    $(document).click(function(event) {
        if (!$(event.target).closest(".ccp-chat-window, .ccp-chat-toggle").length) {
            closeChat();
        }
    });

    $sendButton.click(sendMessage);
    
    $chatInput.keypress(function(event) {
        if (event.which === 13) {
            sendMessage();
        }
    });
});
