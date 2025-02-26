<?php
require_once('../rabbitmq/testRabbitMQClient.php');

// we are validating the CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_COOKIE['csrf_token'])) {
        die("CSRF token missing.");
    }
    if ($_POST['csrf_token'] !== $_COOKIE['csrf_token']) {
        die("CSRF token validation failed.");
    }
}

// login data
$logindata = array(
    'type' => 'login',
    'username' => filter_input(INPUT_POST, 'username'),
    'email' => filter_input(INPUT_POST, 'email'), 
    'password' => filter_input(INPUT_POST, 'password'),
    'message' => 'Logging in user',
);

// send login data to RabbitMQ
$response = sendMessage($logindata);

// handle the response from RabbitMQ
if ($response['status'] === 'Success') {
	// login is successful

	// set auth_token, session_id, and remember me cookies
    setcookie('auth_token', $response['auth_token'], time() + 3600, '/');
    setcookie('session_id', $response['session_id'], time() + 3600, '/'); 
    setcookie('remember_me', bin2hex(random_bytes(32)), time() + (86400 * 30), '/'); 

    // redirect to homepage
    header("Location: homepage.php"); 
    exit();
}
else {
	    // login failed
	echo "Login failed. Please check your username and password.";
	
    // we could also redirect back to the login page, but it's a bit redundent what do you guys think.
    // header("Location: loginPage.html"); 
    // exit();
}

?>
