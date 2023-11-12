<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/**
 * Plugin Name: OpenAI (ChatGPT) Chatbot
 * Description: Allows you to make FAQs, search, and use OpenAI (ChatGPT) to engage your visitors. 
 * Version: 0.9
 * Author: Donalda
 * Author URI: donalda.me
 * Hire, Commission, or Questions?
 * Author Contact: donalda@donalda.net
 */

add_action('admin_menu', 'openai_chat_menu');

function openai_chat_menu() {
    add_menu_page(
        'OpenAI Chat Settings',
        'OpenAI Chat',
        'manage_options',
        'openai_chat_settings',
        'openai_chat_settings_page'
    );
}


function openai_chat_settings_page() {
    // Check if user has submitted the form
    if (isset($_POST['openai_chat_settings_submit']) && check_admin_referer('openai_chat_settings_save', 'openai_chat_settings_nonce')) {
	// Check if the disclaimer cookie exists and is set to 'dismissed'
$disclaimerCookie = isset($_COOKIE['openai_disclaimer']) ? $_COOKIE['openai_disclaimer'] : '';

if ($disclaimerCookie !== 'dismissed') {
    echo '<div class="openai-chat-disclaimer" id="openai-chat-disclaimer">';
    echo '<p><strong>Disclaimer:</strong> By using this WordPress plugin, you agree to the following terms: This plugin is provided as-is and without any warranty or compensation of any kind.</p>';
    echo '<p><b>You acknowledge that you are using this plugin at your own risk.</b></p>';
    echo '<p>The plugin author and developer are not liable for any damages, losses, or issues that may arise from its use.</p>';
    echo '<p>It is your responsibility to ensure that the plugin is suitable for your needs and that you use it in a manner that complies with all applicable laws and regulations.</p>';
    echo '<p>If you would like to hire the plugin author, commission custom code, or discuss other services, please contact Donalda at <a href="mailto:donalda@donalda.net">donalda@donalda.net</a>.</p>';
    echo '<button id="dismiss-disclaimer">Dismiss</button>';
    echo '</div>';
}	
        // Save API key and Shortcode Settings
        update_option('openai_api_key', isset($_POST['openai_api_key']) ? sanitize_text_field($_POST['openai_api_key']) : '');
        update_option('openai_chat_shortcode_title', isset($_POST['openai_chat_shortcode_title']) ? sanitize_text_field($_POST['openai_chat_shortcode_title']) : '');
        update_option('openai_chat_welcome_message', isset($_POST['openai_chat_welcome_message']) ? sanitize_textarea_field($_POST['openai_chat_welcome_message']) : '');

        // Save the JSON entered in the textarea
        $customJson = isset($_POST['openai_custom_json']) ? wp_unslash($_POST['openai_custom_json']) : '';
        update_option('openai_custom_json', $customJson);
    }

    // Retrieve current settings
    $api_key = get_option('openai_api_key', '');
    $custom_json = get_option('openai_custom_json', '');
    $shortcode_title = get_option('openai_chat_shortcode_title', 'Chat with Us');
    $welcome_message = get_option('openai_chat_welcome_message', 'Default Welcome Message');

    // HTML and JavaScript for the settings page
    echo '<div class="wrap">';
    echo '<h2>OpenAI Chat Settings</h2>';
	echo '<h2 class="nav-tab-wrapper">';
    echo '<a href="#" class="nav-tab" data-tab="api-settings">API Settings</a>';
    echo '<a href="#" class="nav-tab" data-tab="custom-json-settings">Custom JSON Data</a>';
    echo '<a href="#" class="nav-tab" data-tab="shortcode-settings">Chat Title and Visitor Welcome</a>';
    echo '</h2>';

    echo '<form method="post" action="">';
    wp_nonce_field('openai_chat_settings_save', 'openai_chat_settings_nonce');

    // API Settings Tab
    echo '<div id="api-settings" class="tab-content">';
    echo '<table class="form-table">';
    echo '<tr valign="top"><th scope="row">OpenAI API Key:</th>';
    echo '<td><input type="text" name="openai_api_key" value="' . esc_attr($api_key) . '" /></td></tr>';
    echo '<p>If you want to use OpenAI (CHATGPT) for your chatbot you need to get an API key from <a href=/"https://platform.openai.com/api-keys/">here.</a></p>';
	echo '<p></p>';
	echo '</table>';
    echo '</div>';

    // Custom JSON Data Tab
    echo '<div id="custom-json-settings" class="tab-content" style="display:none;">';
    echo '<h3>Custom JSON Data for OpenAI API</h3>';
    echo '<p>Add FAQ entries directly by filling out the question you think your visitors will ask and the answer you want to give by clicking \'Add Question\'</p>';
	echo '<p>You can also add them directly to the box if you have a Json file you would like to use. (Or know how to edit the json and it would be faster that way for you.)</p>';
	echo '<p><b>REMEMBER TO CLICK SAVE CHANGES OR YOUR CHANGES MAY NOT BE SAVED!!</b></p>';
	echo '<p>Just wanted everyone to be aware of that.</p><p>Please remember to click the blue save changes button to ensure everything is saved. You can delete questions by removing them from the box and saving!!</p>';
    echo '<input type="text" id="faq_question_input" placeholder="Enter Question" />';
    echo '<input type="text" id="faq_answer_input" placeholder="Enter Answer" />';
    echo '<button type="button" id="append-faq-btn">Add Question</button><br>';
    echo '<textarea id="openai-api-json" name="openai_custom_json" rows="10" cols="50">' . esc_textarea($custom_json) . '</textarea>';
    echo '</div>';
	
    // Shortcode Settings Tab
    echo '<div id="shortcode-settings" class="tab-content" style="display:none;">';
    echo '<h3>Chat Title and Visitor Welcome</h3>';
    echo '<table class="form-table"><br><b>Use the shortcode [openai_chat] on the pages you want to display the chat.</b>';
    echo '<tr valign="top"><th scope="row">Chat Title:</th>';
    echo '<td><input type="text" name="openai_chat_shortcode_title" value="' . esc_attr($shortcode_title) . '" /></td></tr>';
    echo '<h3>Welcome Message Settings</h3>';
	echo '<table class="form-table">';
	echo '<p>Use this box to provide instructions. Say hello, or just introduce your site.</p>';
	echo '<p>For Example: Hello and welcome! 🌟 Need help navigating our site or finding specific information? Just type \'search\' followed by your query, and I will scour our website for you. Curious about something? Ask away, and I\'ll check if it\'s in our FAQs. And remember, if you\'re ever in doubt or need more assistance, I\'m here - your friendly AI guide, ready to help with anything you need. Let\'s chat! 💬</p>';
    echo '<tr valign="top"><th scope="row">Welcome Message:</th>';
    echo '<td><textarea name="openai_chat_welcome_message" rows="5" cols="50">' . esc_textarea($welcome_message) . '</textarea></td></tr>';
    echo '</table>';
    echo '</div>';

    echo '<p class="submit"><input type="submit" class="button-primary" name="openai_chat_settings_submit" value="Save Changes" /></p>';
    echo '</form>';
    echo '</div>';

    // JavaScript for Tabs, JSON Generation and Appending FAQs
echo '<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    var tabs = document.querySelectorAll(".nav-tab");
    var tabContents = document.querySelectorAll(".tab-content");
    var appendFaqBtn = document.getElementById("append-faq-btn");
    var questionInput = document.getElementById("faq_question_input");
    var answerInput = document.getElementById("faq_answer_input");
    var jsonTextarea = document.getElementById("openai-api-json");

    tabs.forEach(function(tab) {
        tab.addEventListener("click", function(event) {
            event.preventDefault();

            // Remove active class from all tabs
            tabs.forEach(function(item) {
                item.classList.remove("nav-tab-active");
            });
            // Add active class to clicked tab
            this.classList.add("nav-tab-active");

            // Hide all tab contents
            tabContents.forEach(function(content) {
                content.style.display = "none";
            });

            // Show the current tabs content
            var activeTab = this.getAttribute("data-tab");
            document.getElementById(activeTab).style.display = "block";
        });
    });

    appendFaqBtn.addEventListener("click", function() {
        var question = questionInput.value.trim();
        var answer = answerInput.value.trim();
        if(question && answer) {
            var existingFaqs = jsonTextarea.value ? JSON.parse(jsonTextarea.value) : [];
            existingFaqs.push({ "question": question, "answer": answer });
            jsonTextarea.value = JSON.stringify(existingFaqs, null, 2);
            questionInput.value = \'\'; // Escaped single quotes
            answerInput.value = \'\'; // Escaped single quotes
        } else {
            alert("Please enter both a question and an answer.");
        }
    });

    // Activate the first tab by default if none is active
    var activeTabs = document.querySelectorAll(".nav-tab-active");
    if (activeTabs.length === 0 && tabs.length > 0) {
        tabs[0].click();
    }
});

</script>';
}


// Shortcode Implementation
function openai_chat_shortcode($atts) {
    // Shortcode attributes and default values
    $atts = shortcode_atts(
        array(
            'title' => get_option('openai_chat_shortcode_title', 'Chat with Us'),
        ),
        $atts,
        'openai_chat'
    );
    $welcome_message = get_option('openai_chat_welcome_message', 'Welcome! Ask me anything.');

// HTML for the chatbox with instructions
    $html = '<div class="openai-chat-container">';
    $html .= '<div class="openai-chat-header">' . esc_html($atts['title']) . '</div>';
    $html .= '<div class="openai-chat-instructions">' . esc_html($welcome_message) . '</div>';
    $html .= '<div class="openai-chat-messages"></div>';
    $html .= '<input type="text" class="openai-chat-input" placeholder="Type a message..."/>';
    $html .= '<button class="openai-chat-send">Send</button>';
    $html .= '</div>';

    // JavaScript for chatbox functionality
    $html .= '<script type="text/javascript">
                jQuery(document).ready(function($) {
                    $(".openai-chat-send").click(function() {
                        var message = $(".openai-chat-input").val();
                        // AJAX call to send the message and get a response
                        // Update the .openai-chat-messages with the response
                    });
                });
              </script>';

    return $html;
}
add_shortcode('openai_chat', 'openai_chat_shortcode');


function openai_chat_ajax_handler() {
    header('Content-Type: application/json');

    if (!isset($_POST['message']) || empty($_POST['message'])) {
        echo json_encode(array('error' => 'No message received.'));
        wp_die();
    }

    $user_message = sanitize_text_field($_POST['message']);

    // Retrieve FAQs and search for an answer
    $faqs = retrieve_faqs();
    error_log('FAQ Data before search: ' . print_r($faqs, true));

    $faq_response = search_faq($user_message, $faqs);
    if ($faq_response) {
        echo json_encode(array('response' => $faq_response));
        wp_die();
    }

    // If no FAQ match found, send the message to OpenAI
    $response = send_message_to_openai($user_message);

    if (isset($response['error'])) {
        echo json_encode(array('error' => $response['error']));
    } else {
        echo json_encode(array('response' => $response['success']));
    }

    wp_die();
}
add_action('wp_ajax_openai_chat', 'openai_chat_ajax_handler');
add_action('wp_ajax_nopriv_openai_chat', 'openai_chat_ajax_handler');

function perform_wordpress_search($query) {
    $search_query = new WP_Query(array('s' => $query));
    $result_html = '';

    if ($search_query->have_posts()) {
        while ($search_query->have_posts()) {
            $search_query->the_post();
            $result_html .= 'I found the following: <a href="' . get_permalink() . '">' . get_the_title() . '</a><br>';
        }
    } else {
        $result_html = 'No results found.';
    }

    wp_reset_postdata(); // Reset global post data
    return $result_html;
}


function openai_chat_enqueue_scripts() {
    wp_enqueue_style('openai-chat-style', plugins_url('css/openai-chat.css', __FILE__));
    wp_enqueue_script('openai-chat-script', plugins_url('js/openai-chat.js', __FILE__), array('jquery'), false, true);
    wp_localize_script('openai-chat-script', 'openai_chat_params', array('ajax_url' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts', 'openai_chat_enqueue_scripts');

function search_faq($user_query, $faqs) {
    error_log('Searching FAQ for: ' . $user_query);
    error_log('FAQ Data: ' . print_r($faqs, true));

    foreach ($faqs as $faq) {
        if (strtolower(trim($user_query)) === strtolower(trim($faq['question']))) {
            return $faq['answer'];
        }
    }
    return null; // Return null if no match is found
}


function retrieve_faqs() {
    // Fetch FAQ data from the 'openai_custom_json' option
    $customJson = get_option('openai_custom_json', '');
    error_log('Retrieved FAQs from custom JSON: ' . $customJson);

    // Decode the JSON string into a PHP array
    $faqs = json_decode(wp_unslash($customJson), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Decode Error: ' . json_last_error_msg());
        return []; // Return an empty array if there's a decode error
    }

    // Return the decoded FAQs
    return $faqs;
}


function send_message_to_openai($message) {
    global $api_key;
    $api_key = get_option('openai_api_key', '');

    $api_url = 'https://api.openai.com/v1/chat/completions'; // OpenAI API URL

    $payload = json_encode(array(
        "model" => "gpt-3.5-turbo", // Specify the model here
        "messages" => array(array("role" => "user", "content" => $message))
    ));

    $response = wp_remote_post($api_url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json'
        ),
        'body' => $payload,
        'data_format' => 'body'
    ));

    if (is_wp_error($response)) {
        return array('error' => $response->get_error_message());
    } else {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['choices'][0]['message']['content'])) {
            return array('success' => $data['choices'][0]['message']['content']);
        } else {
            return array('error' => 'Unexpected API response format.');
        }
    }
}
function display_openai_disclaimer_notice() {
    // Check if the disclaimer cookie exists and is set to 'dismissed'
    $disclaimer_cookie = isset($_COOKIE['openai_disclaimer']) ? $_COOKIE['openai_disclaimer'] : '';

    if ($disclaimer_cookie !== 'dismissed') {
        echo '<div class="notice notice-info is-dismissible">';
        echo '<p><strong>Disclaimer:</strong> By using this WordPress plugin, you agree to the following terms: This plugin is provided as-is and without any warranty or compensation of any kind.</p>';
        echo '<p><b>You acknowledge that you are using this plugin at your own risk.</b></p>';
        echo '<p>The plugin author and developer are not liable for any damages, losses, or issues that may arise from its use.</p>';
        echo '<p>It is your responsibility to ensure that the plugin is suitable for your needs and that you use it in a manner that complies with all applicable laws and regulations.</p>';
        echo '<p>If you would like to hire the plugin author, commission custom code, or discuss other services, please contact Donalda at <a href="mailto:donalda@donalda.net">donalda@donalda.net</a>.</p>';
        echo '<button id="dismiss-disclaimer" class="button button-primary">Dismiss</button>';
        echo '</div>';
    }
}

add_action('admin_notices', 'display_openai_disclaimer_notice');
