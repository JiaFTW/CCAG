<?php
require_once('../rabbitmq/testRabbitMQClient.php');

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);

if (!$email || !$code) {
    header("Location: verify2FA.php?error=invalid_input");
    exit();
}

$response = sendMessage([
    'type' => 'verify_code',
    'email' => $email,
    'code' => $code
]);

if ($response['status'] === 'Success') {
    // Set cookies from the verification response
    setcookie("session_token", $response['cookie'], time()+3600, "/");
    setcookie("username", $response['username'], time()+3600, "/");
    header("Location: homepage.php");
} else {
    header("Location: verify2FA.php?email=".urlencode($email)."&error=".urlencode($response['message'] ?? 'verification_failed'));
}
exit();
