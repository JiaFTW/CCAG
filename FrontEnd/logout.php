<?php
require_once('../rabbitmq/testRabbitMQClient.php');

if (isset($_COOKIE['session_token'])) {
    $logoutData = array(
        'type' => 'logout',
        'sessionId' => $_COOKIE['session_token'],
    );

    $response = sendMessage($logoutData);

    if ($response['status'] == 'Success') {
        setcookie('session_token', '', time()-3600,"/");
        setcookie('username','',time()-3600,"/");
    }
}

header("Location: homepage.php");
die();
?>