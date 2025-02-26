<?php 
require_once('../rabbitmq/testRabbitMQClient.php');

// Validate CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_COOKIE['csrf_token'])) {
        die("CSRF token missing.");
    }
    if ($_POST['csrf_token'] !== $_COOKIE['csrf_token']) {
        die("CSRF token validation failed.");
    }
}





//TODO : make new sendMessage type for Session Validation / Check
$logindata = array (
    'type' => 'login',
    'username' => filter_input(INPUT_POST,'username'),
    'email' => filter_input(INPUT_POST,'email'),
    'password' => filter_input(INPUT_POST,'password'),
    'message' => 'Logging in user',
);

//Sends the login request
$response = sendMessage($logindata);

if ($response['status'] == 'Success') {
   
    //Set the auth_token cookie
    setcookie('auth_token', $response['cookie'], time() + 3600, '/'); // this will expires in 1 hour

    // Generate a secure remember_me token
    $remember_me_token = bin2hex(random_bytes(32));
    saveRememberMeTokenToDatabase($logindata['username'], $remember_me_token);
    setcookie('remember_me', $remember_me_token, time() + (86400 * 30), '/'); // Expires in 30 days

    header("Location: homepage.php");
    exit();
} else {
    echo "Login failed: " . $response['message'];
}


?>

