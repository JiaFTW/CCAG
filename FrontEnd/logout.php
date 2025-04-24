<?php
require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');
require_once('./logging/sendlogs.php');

if (isset($_COOKIE['session_token'])) {
    $logoutData = array(
        'type' => 'logout',
        'sessionId' => $_COOKIE['session_token'],
    );

    $response = sendMessage($logoutData);
    sendinglogs();

    if ($response['status'] == 'Success') {
        writelog("logged out.", $_COOKIE['username']);
        setcookie('session_token', '', time()-3600,"/");
        setcookie('username','',time()-3600,"/");
    }
}

header("Location: homepage.php");
die();
?>