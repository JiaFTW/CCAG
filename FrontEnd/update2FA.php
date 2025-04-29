<?php
// Remove all output buffering and error display
error_reporting(0);

// Immediate redirect if no session
if (!isset($_COOKIE['session_token'])) {
    header("Location: loginPage.php");
    exit;
}

// Direct includes without buffering
require_once('../rabbitmq/testRabbitMQClient.php');

// Process checkbox state
$tfaEnabled = isset($_POST['tfa_enabled']) ? 1 : 0;

// Send update request
$response = sendMessage([
    'type' => 'update_2fa',
    'username' => $_COOKIE['username'],
    'tfa_enabled' => $tfaEnabled
]);

// Force immediate redirect
header("Location: settings.php");
exit;
?>
