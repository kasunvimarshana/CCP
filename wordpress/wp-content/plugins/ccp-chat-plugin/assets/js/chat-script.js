// jQuery(document).ready(function($) {
//     // Open the chat widget when the button is clicked
//     $('#ccp-chat-open').click(function() {
//         $('#ccp-chat-widget').fadeIn();
//     });

//     // Close the chat widget when the close button is clicked
//     $('#ccp-close-chat').click(function() {
//         $('#ccp-chat-widget').fadeOut();
//     });

//     // Send message on pressing Enter key in input box
//     $('#ccp-chat-input').keypress(function(e) {
//         if (e.which === 13) { // Enter key
//             var message = $(this).val();
//             if (message.trim() !== '') {
//                 // Append the user's message to chat
//                 $('#ccp-chat-messages').append('<div class="message sent">' + message + '</div>');
//                 $(this).val(''); // Clear input field

//                 // Send message to WordPress via AJAX
//                 $.ajax({
//                     url: ccp_ajax_obj.ajax_url,
//                     method: 'POST',
//                     data: {
//                         action: 'ccp_send_chat_message',
//                         message: message
//                     },
//                     success: function(response) {
//                         if (response.success) {
//                             console.log(response);
//                             const data = response.data?.data;
//                             const message = data?.payload?.[0];
//                             // Display response message
//                             $('#ccp-chat-messages').append('<div class="message received">' + message + '</div>');
//                         } else {
//                             $('#ccp-chat-messages').append('<div class="message error">Error: ' + response.data.message + '</div>');
//                         }
//                     },
//                     error: function() {
//                         $('#ccp-chat-messages').append('<div class="message error">Error sending message.</div>');
//                     }
//                 });
//             }
//         }
//     });
// });

jQuery(document).ready(function($) {
    // Toggle chat window
    $("#chat-toggle").click(function() {
        $("#chat-window").fadeToggle(300).toggleClass("active");
    });

    // Close chat when clicking the close button
    $("#close-chat").click(function() {
        $("#chat-window").fadeOut(300).removeClass("active");
    });

    // Close chat when clicking outside
    $(document).click(function(event) {
        if (!$(event.target).closest("#chat-window, #chat-toggle").length) {
            $("#chat-window").fadeOut(300).removeClass("active");
        }
    });

    // Send message
    $("#send-button").click(function() {
        const messageText = $("#chat-input").val().trim();
        if (messageText !== "") {
            const message = `<div class="message outgoing">${messageText}</div>`;
            $(".chat-body").append(message);
            $("#chat-input").val("");
            $(".chat-body").scrollTop($(".chat-body").get(0).scrollHeight);
            // Send message to WordPress via AJAX
            $.ajax({
                url: ccp_ajax_obj.ajax_url,
                method: 'POST',
                data: {
                    action: 'ccp_send_chat_message',
                    message: message
                },
                success: function(response) {
                    if (response.success) {
                        console.log(response);
                        const data = response.data?.data;
                        const messageText = data?.payload?.[0];
                        const message = `<div class="message incoming">${messageText}</div>`;
                        $(".chat-body").append(message);
                        $(".chat-body").scrollTop($(".chat-body").get(0).scrollHeight);
                    }
                },
                error: function() {
                    // 
                }
            });
        }
    });

    // Send message on Enter key
    $("#chat-input").keypress(function(event) {
        if (event.which === 13) {
            $("#send-button").click();
        }
    });
});
