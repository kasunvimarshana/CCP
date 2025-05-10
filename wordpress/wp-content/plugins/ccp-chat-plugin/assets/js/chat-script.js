jQuery(document).ready(function($) {
    const $chatContainer = $(".ccp-chat-container");

    const CHAT_SESSION_DATA_STORAGE_KEY = "ccpChatSession";

    // Select elements
    const $chatWindow = $chatContainer.find(".ccp-chat-window");
    const $chatToggle = $chatContainer.find(".ccp-chat-toggle");
    const $chatInput = $chatContainer.find(".ccp-chat-input");
    const $chatBody = $chatContainer.find(".ccp-chat-body");
    const $sendButton = $chatContainer.find(".ccp-send-button");
    const $closeChat = $chatContainer.find(".ccp-close-chat");
    // 
    const $chatFooter = $chatContainer.find(".ccp-chat-footer");
    const $chatForm = $chatContainer.find(".ccp-chat-form");
    const $name = $chatForm.find("[name='name']");
    const $email = $chatForm.find("[name='email']");
    const $phone = $chatForm.find("[name='phone']");
    const $terms = $chatForm.find("[name='terms']");
    const $emailField = $email.closest('.form-group');
    const $phoneField = $phone.closest('.form-group');
    const $emailSendOTPContainer = $emailField.find(".send_otp_container");
    const $emailVerifyOTPContainer = $emailField.find(".verify_otp_container");
    const $emailSendOTP = $emailSendOTPContainer.find(".send_otp");
    const $emailOTP = $emailVerifyOTPContainer.find("[name='otp']");
    const $emailVerifyOTP = $emailVerifyOTPContainer.find(".verify_otp");
    const $phoneSendOTPContainer = $phoneField.find(".send_otp_container");
    const $phoneVerifyOTPContainer = $phoneField.find(".verify_otp_container");
    const $phoneSendOTP = $phoneSendOTPContainer.find(".send_otp");
    const $phoneOTP = $phoneVerifyOTPContainer.find("[name='otp']");
    const $phoneVerifyOTP = $phoneVerifyOTPContainer.find(".verify_otp");
    const $startChat = $chatForm.find(".ccp-start-chat-button");

    // Initialize session storage for chat data
    let chatSessionData = {};
    
    // Check session storage for existing chat session
    function loadChatSession() {
        const savedSession = sessionStorage.getItem(CHAT_SESSION_DATA_STORAGE_KEY);
        if (savedSession) {
            try {
                chatSessionData = JSON.parse(savedSession);
                return true;
            } catch (e) {
                console.error("Error parsing saved chat session", e);
                sessionStorage.removeItem(CHAT_SESSION_DATA_STORAGE_KEY);
            }
        }
        return false;
    }
    
    // save chat session
    function saveChatSession() {
        if (chatSessionData && Object.keys(chatSessionData).length > 0) {
            sessionStorage.setItem(CHAT_SESSION_DATA_STORAGE_KEY, JSON.stringify(chatSessionData));
        }
    }

    // clear chat session
    function clearChatSession() {
        chatSessionData = {};
        sessionStorage.removeItem(CHAT_SESSION_DATA_STORAGE_KEY);
        $chatBody.empty();
        $chatBody.hide();
        $chatFooter.hide();
        $chatForm.show();
    }
    
    // Initialize chat interface based on session existence
    function initializeChatInterface() {
        const hasExistingSession = loadChatSession();
        
        if (hasExistingSession && chatSessionData.conversationId) {
            // Show chat interface
            $chatBody.show();
            $chatFooter.show();
            $chatForm.hide();
        } else {
            // Show registration form
            $chatBody.hide();
            $chatFooter.hide();
            $chatForm.show();
        }
    }

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
            const safeText = $('<div>').text(text).html();
            const message = `<div class="ccp-message ${type}">${safeText}</div>`;
            $chatBody.append(message);
            $chatBody.scrollTop($chatBody.prop("scrollHeight"));
        }
    }

    function showError($field, message) {
        const $errorElement = $field.find('.error-message');
        if (message) {
            $errorElement.text(message).show();
        } else {
            $errorElement.hide();
        }
    }

    function validateForm() {
        let isValid = true;
        
        // Reset error messages
        $errorMessages.hide();
        
        // Validate name
        if (!$name.val().trim()) {
            showError($name.closest('.form-group'), "Please enter your name");
            isValid = false;
        }
        
        // Validate email
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test($email.val().trim())) {
            showError($emailField, "Please enter a valid email address");
            isValid = false;
        }
        
        // Validate phone
        const phonePattern = /^\d{10,15}$/;
        if (!phonePattern.test($phone.val().trim().replace(/\D/g, ''))) {
            showError($phoneField, "Please enter a valid phone number");
            isValid = false;
        }
        
        // Validate terms
        if (!$terms.prop("checked")) {
            showError($terms.closest('.form-group'), "You must agree to the Terms and Conditions");
            isValid = false;
        }
        
        return isValid;
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
                message: messageText,
                conversationId: chatSessionData?.conversationId
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

    // sendOTP
    function sendOTP(type, onSuccess=Function.prototype, onError=Function.prototype) {
        const value = type === 'email' ? $email.val().trim() : $phone.val().trim();
        const contactType = type === 'email' ? 'EMAIL' : 'MOBILE';

        if (!value) {
            return;
        }

        $.ajax({
            url: ccp_ajax_obj.ajax_url,
            method: "POST",
            data: {
                action: "ccp_send_otp",
                contactValue: value,
                contactType: contactType,
                eventId: 1
            },
            success: function(response) {
                if (response.success) {
                    onSuccess(response);
                }
            },
            error: function(error) {
                console.error("Failed to send otp.");
                onError(error);
            }
        });
    }

    function sendEmailOTP() {
        sendOTP("email", () => {
            $emailSendOTPContainer.hide();
            $emailVerifyOTPContainer.show();
        });
    }

    function sendPhoneOTP() {
        sendOTP("phone", () => {
            $phoneSendOTPContainer.hide();
            $phoneVerifyOTPContainer.show();
        });
    }

    $emailSendOTP.click(sendEmailOTP);
    $phoneSendOTP.click(sendPhoneOTP);

    // verifyOTP
    function verifyOTP(type, onSuccess=Function.prototype, onError=Function.prototype) {
        const value = type === 'email' ? $email.val().trim() : $phone.val().trim();
        const contactType = type === 'email' ? 'EMAIL' : 'MOBILE';
        const otpValue = type === 'email' ? $emailOTP.val().trim() : $phoneOTP.val().trim();

        if (!value || !otpValue) {
            return;
        }

        $.ajax({
            url: ccp_ajax_obj.ajax_url,
            method: "POST",
            data: {
                action: "ccp_verify_otp",
                contactValue: value,
                contactType: contactType,
                otp: otpValue,
                eventId: 1
            },
            success: function(response) {
                if (response.success) {
                    onSuccess(response);
                }
            },
            error: function(error) {
                console.error("Failed to verify otp.");
                onError(error);
            }
        });
    }

    function verifyEmailOTP() {
        verifyOTP("email", () => {
            $emailVerifyOTPContainer.hide();
        });
    }

    function verifyPhoneOTP() {
        verifyOTP("phone", () => {
            $phoneVerifyOTPContainer.hide();
        });
    }

    $emailVerifyOTP.click(verifyEmailOTP);
    $phoneVerifyOTP.click(verifyPhoneOTP);

    // startChat
    function startChat(onSuccess=Function.prototype, onError=Function.prototype) {
        const name = $name.val().trim();
        const email = $email.val().trim();
        const phone = $phone.val().trim();
        const termsIsChecked = $terms.prop("checked"); // $terms.is(":checked");

        if (!termsIsChecked) {
            return;
        }

        $.ajax({
            url: ccp_ajax_obj.ajax_url,
            method: "POST",
            data: {
                action: "ccp_start_chat",
                name: name,
                email: email,
                phone: phone,
                terms: termsIsChecked,
                eventId: 1
            },
            success: function(response) {
                if (response.success) {
                    onSuccess(response);
                }
            },
            error: function(error) {
                console.error("Failed to start chat.");
                onError(error);
            }
        });
    }

    function onStartChatClick() {
        startChat((response) => {
            const payload = response?.data?.data?.payload?.[0] ?? [];

            payload.forEach((v) => {
                if (v.chatRequest) appendMessage(v.chatRequest, "outgoing");
                if (v.chatResponse) appendMessage(v.chatResponse, "incoming");
                chatSessionData.conversationId = v.conversationId;
                chatSessionData.visitorId = v.visitorId;
            });

            saveChatSession();

            $chatBody.show();
            $chatFooter.show();
            $chatForm.hide();
        });
    }

    $startChat.click(onStartChatClick);

    // checkVerificationStatus
    function checkVerificationStatus(type, onSuccess=Function.prototype, onError=Function.prototype) {
        const value = type === 'email' ? $email.val().trim() : $phone.val().trim();
        const contactType = type === 'email' ? 'EMAIL' : 'MOBILE';

        if (!value) {
            return;
        }

        $.ajax({
            url: ccp_ajax_obj.ajax_url,
            method: "POST",
            data: {
                action: "ccp_check_verification_status",
                contactValue: value,
                contactType: contactType,
                eventId: 1
            },
            success: function(response) {
                if (response.success) {
                    onSuccess(response);
                }
            },
            error: function(error) {
                console.error("Failed to check verification status.");
                onError(error);
            }
        });
    }

    function checkEmailVerificationStatus() {
        // check valid email
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test($email.val().trim())) {
            return;
        }
        
        checkVerificationStatus("email", (response) => {
            const isVerified = !!response?.data?.data?.payload?.[0];
            if (isVerified) {
                $emailSendOTPContainer.hide();
                showError($emailField, "Email is already verified");
            } else {
                $emailSendOTPContainer.show();
                $emailVerifyOTPContainer.hide();
            }
        });
    }

    function checkPhoneVerificationStatus() { 
        // check valid phone
        const phonePattern = /^\d{10,15}$/;
        if (!phonePattern.test($phone.val().trim().replace(/\D/g, ''))) {
            return;
        }
        
        checkVerificationStatus("phone", (response) => {
            const isVerified = !!response?.data?.data?.payload?.[0];
            if (isVerified) {
                $phoneSendOTPContainer.hide();
                showError($phoneField, "Phone is already verified");
            } else {
                $phoneSendOTPContainer.show();
                $phoneVerifyOTPContainer.hide();
            }
        });
    }

    //
    let emailTimer;
    let phoneTimer;
    
    $email.on('input', function() {
        clearTimeout(emailTimer);
        emailTimer = setTimeout(checkEmailVerificationStatus, 500);
    });
    
    $phone.on('input', function() {
        clearTimeout(phoneTimer);
        phoneTimer = setTimeout(checkPhoneVerificationStatus, 500);
    });
    
    // Initialize chat interface on page load
    initializeChatInterface();
});
