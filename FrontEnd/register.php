<?php 
require_once('../rabbitmq/testRabbitMQClient.php');

// Start the session, for example if we are directly going to the registration page.
session_start();

// Validate CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_COOKIE['csrf_token'])) {
        die("CSRF token missing.");
    }
    if ($_POST['csrf_token'] !== $_COOKIE['csrf_token']) {
        die("CSRF token validation failed.");
    }
}

$registerdata = array(
    'type' => 'register',
    'username' => filter_input(INPUT_POST,'username'),
    'email' => filter_input(INPUT_POST,'email'),
    'password' => filter_input(INPUT_POST,'password'),
    'message' => 'Registering user',
);

//send registration data to RabbitMQ
$response = sendMessage($registerdata);

// handle the response from RabbitMQ
if ($response['status'] === 'Success') {
    // registration successful
    // set auth_token and session_id cookies using values returned by RabbitMQ
    setcookie('auth_token', $response['auth_token'], time() + 3600, '/'); 
    setcookie('session_id', $response['session_id'], time() + 3600, '/'); 

    // redirect to homepage
    header("Location: homepage.php"); 
    exit();
}
else {
    // Registration failed
    echo "Registration failed. Reason: " . $response['invalid_type']; // Added: Display error message
    // we could also redirect back to the registration page
    // header("Location: registerPage.html"); 
    // exit(); 
}


/*echo ($registerdata['username']);
sendMessage($registerdata);

// So we could go two ways here option 1: automatically log the user in after registration
// Generate a secure auth token
$auth_token = bin2hex(random_bytes(32));

// Store the auth token in the database (or session) associated with the user, again i didn't touch the database section yet so no clue.
// Example: saveAuthTokenToDatabase($registerdata['username'], $auth_token);

// Set the auth_token cookie
setcookie('auth_token', $auth_token, time() + 3600, '/'); // Expires in 1 hour

// Generate a secure remember_me token
$remember_me_token = bin2hex(random_bytes(32));

// Store the remember_me token in the database (or session) associated with the user
// Example: saveRememberMeTokenToDatabase($registerdata['username'], $remember_me_token);

// Set the remember_me cookie
setcookie('remember_me', $remember_me_token, time() + (86400 * 30), '/'); // Expires in 30 days

// This will then redirect to homepage after successful registration
header("Location: homepage.php");
exit();

// For option 2:we can redirect to login page after registration,whatever option you guys think is better.
// header("Location: loginPage.html");
// exit();
 */
?>
