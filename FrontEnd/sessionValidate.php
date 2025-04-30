<?php
require_once('../rabbitmq/testRabbitMQClient.php');

if (!isset($_COOKIE['session_token']) || !isset($_COOKIE['username'])) {
    header("Location: loginPage.php");
    die();
}

$sessionId = [
    'type' => 'validate_session',
    'sessionId' => $_COOKIE['session_token']
];

$sessioncheck = sendMessage($sessionId);

if ($sessioncheck['status'] !== 'Success' || $sessioncheck['username'] !== $_COOKIE['username']) {
    // Destroy cookie securely
    setcookie("session_token", "", [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => true,     // Only send over HTTPS
        'httponly' => true,   // Prevent JavaScript access
        'samesite' => 'Strict'
    ]);
    header("Location: loginPage.php");
    die();
}


/*require_once('../rabbitmq/testRabbitMQClient.php');

if (!isset($_COOKIE['session_token'])  || !isset($_COOKIE['username'])) {
    header("Location: loginPage.php");
    die();
}

$sessionId = array (
    'type' => 'validate_session',
    'sessionId' => $_COOKIE['session_token'],
);

$sessioncheck = sendMessage($sessionId);

if ($sessioncheck['status'] !== 'Success') {
    setcookie("session_token", "", time()-3600, "/");
    header("Location: loginPage.php");
    die();
}*/
?>
