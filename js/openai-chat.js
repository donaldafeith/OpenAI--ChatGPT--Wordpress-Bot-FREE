jQuery(document).ready(function($) {
    // Function to handle sending the message
    function sendMessage() {
        var message = $(".openai-chat-input").val();
        
        if (message.toLowerCase().startsWith("search ")) {
            // Prefix with 'search:' for the server to recognize it as a search query
            message = "search:" + message.substring(7);
        }

        $.ajax({
            url: openai_chat_params.ajax_url,
            type: 'POST',
            data: {
                action: 'openai_chat',
                message: message
            },
            success: function(response) {
                $(".openai-chat-messages").append('<div>' + response.response + '</div>');
                $(".openai-chat-input").val('');
            },
            error: function(error) {
                console.error('Error: ', error);
            }
        });
    }

    // Event listener for the Send button
    $(".openai-chat-send").click(function() {
        sendMessage();
    });

    // Event listener for pressing Enter key in the input field
    $(".openai-chat-input").keypress(function(event) {
        if (event.which === 13) { // Enter key has keyCode = 13
            event.preventDefault(); // Prevent the default form submit
            sendMessage();
        }
    });

    // Toggle chat container on header click and prevent propagation on inner elements
    $('.openai-chat-header').click(function() {
        $('.openai-chat-container').toggleClass('expanded');
    });

    // Prevent closing when clicking inside the chatbox
    $('.openai-chat-messages, .openai-chat-input, .openai-chat-send').click(function(e) {
        e.stopPropagation();
    });
});