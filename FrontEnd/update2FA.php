<?php
error_reporting(0);

if (!isset($_COOKIE['session_token'])) {
    header("Location: loginPage.php");
    exit;
}

require_once('../rabbitmq/testRabbitMQClient.php');

$tfaEnabled = isset($_POST['tfa_enabled']) ? 1 : 0;

$response = sendMessage([
    'type' => 'update_2fa',
    'username' => $_COOKIE['username'],
    'tfa_enabled' => $tfaEnabled
]);

header("Location: settings.php");
exit;
?>
