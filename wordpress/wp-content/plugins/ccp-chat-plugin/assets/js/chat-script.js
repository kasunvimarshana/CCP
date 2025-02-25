jQuery(document).ready(function($) {
    const $chatWindow = $("#chat-window");
    const $chatToggle = $("#chat-toggle");
    const $chatInput = $("#chat-input");
    const $chatBody = $(".chat-body");
    const $sendButton = $("#send-button");
    const $closeChat = $("#close-chat");

    function toggleChat() {
        $chatWindow.fadeToggle(300).toggleClass("active");
    }

    function closeChat() {
        $chatWindow.fadeOut(300).removeClass("active");
    }

    function appendMessage(text, type) {
        if (text) {
            const message = `<div class="message ${type}">${text}</div>`;
            $chatBody.append(message);
            $chatBody.scrollTop($chatBody.prop("scrollHeight"));
        }
    }

    function sendMessage() {
        const messageText = $chatInput.val().trim();
        if (!messageText) return;

        appendMessage(messageText, "outgoing");
        $chatInput.val("");

        $.ajax({
            url: ccp_ajax_obj.ajax_url,
            method: "POST",
            data: {
                action: "ccp_send_chat_message",
                message: messageText
            },
            success: function(response) {
                if (response.success) {
                    const messageText = response.data?.data?.payload?.[0];
                    appendMessage(messageText, "incoming");
                }
            },
            error: function() {
                console.error("Failed to send message.");
            }
        });
    }

    $chatToggle.click(toggleChat);
    $closeChat.click(closeChat);
    
    $(document).click(function(event) {
        if (!$(event.target).closest("#chat-window, #chat-toggle").length) {
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
