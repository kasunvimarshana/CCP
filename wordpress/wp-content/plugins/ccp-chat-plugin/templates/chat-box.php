<?php
    $chat_title = get_option('ccp_title', CCP_DEFAULT_TITLE);
?>

<!-- Wrapper container for chat elements -->
<div class="ccp-chat-container">
    <!-- Floating Chat Button -->
    <button class="ccp-chat-toggle ccp-chat-button" aria-label="Open chat window">
        <svg class="ccp-chat-icon" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M4 2h16c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2h-4l-4 4-4-4H4c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2z" />
        </svg>
    </button>

    <!-- Chat Window -->
    <div class="ccp-chat-window">
        <div class="ccp-chat-header">
            <?php echo esc_html($chat_title); ?>
            <span class="ccp-close-chat">✖</span>
        </div>
        <div class="ccp-chat-form">
            <div class="welcome-message">
                <h3>Let's Get Started</h3>
                <p>Please provide your information to continue.</p>
            </div>
            <form class="chat-registration-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text"
                            name="name" 
                            placeholder="Enter your name" 
                            class="form-input"
                            aria-required="true"
                            required />
                    <div class="error-message">Please enter your name</div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email"
                            name="email" 
                            placeholder="Enter your email" 
                            class="form-input"
                            aria-required="true"
                            required />
                    <div class="error-message">Please enter a valid email address</div>
                    <div class="send_otp_container">
                        <button type="button" class="send_otp">Send OTP</button>
                    </div>
                    <div class="verify_otp_container" style="display: none;">
                        <input type="text" name="otp"/>
                        <button type="button" class="verify_otp">Verify OTP</button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel"
                            name="phone" 
                            placeholder="Enter your phone number" 
                            class="form-input"
                            aria-required="true"
                            required />
                    <div class="error-message">Please enter a valid phone number</div>
                    <div class="send_otp_container">
                        <button type="button" class="send_otp">Send OTP</button>
                    </div>
                    <div class="verify_otp_container" style="display: none;">
                        <input type="text" name="otp"/>
                        <button type="button" class="verify_otp">Verify OTP</button>
                    </div>
                </div>

                <div class="form-group" data-field="terms">
                    <label for="terms">Terms and Conditions</label>
                    <div class="checkbox-container">
                        <input type="checkbox" 
                            name="terms"
                            aria-required="true"
                            required />
                        <label for="terms">I agree to the Terms and Conditions</label>
                    </div>
                    <div class="error-message">You must agree to the Terms and Conditions</div>
                </div>
                
                <button type="button" class="ccp-start-chat-button">Start Chat</button>
            </form>
        </div>
        <div class="ccp-chat-body" style="display: none;">
            <!-- Messages will be appended here -->
            <!-- <div class="message incoming">Hello! How can I assist you today?</div>
            <div class="message outgoing">Hello! How can I assist you today?</div> -->
        </div>
        <div class="ccp-chat-footer" style="display: none;">
            <input type="text" class="ccp-chat-input" placeholder="Type a message...">
            <button class="ccp-send-button">Send</button>
        </div>
    </div>
</div>

