<?php
require_once('../rabbitmq/testRabbitMQClient.php');

// Debugging setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get POST data with validation
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
//$code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);

if (!$email || !$code) {
    header("Location: verifyEmail.php?error=invalid_input");
    exit();
}

$verificationData = [
    'type' => 'verify_code',
    'email' => $email,
    'code' => $code
];

try {
    $response = sendMessage($verificationData);
    
    // Ensure response is an array
    if (!is_array($response)) {
        throw new Exception("Invalid server response");
    }
    
    if ($response['status'] === 'Success') {
        header("Location: loginPage.php?verified=1");
    } else {
        header("Location: verifyEmail.php?email=".urlencode($email)."&error=".urlencode($response['message'] ?? 'unknown_error'));
    }
    exit();
    
} catch (Throwable $e) {
    error_log("Verification Error: ".$e->getMessage());
    header("Location: verifyEmail.php?email=".urlencode($email)."&error=system_error");
    exit();
}
?>
