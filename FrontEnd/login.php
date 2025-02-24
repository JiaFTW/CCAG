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





$logindata = array (
    'type' => 'login',
    'username' => filter_input(INPUT_POST,'username'),
    'email' => filter_input(INPUT_POST,'email'),
    'password' => filter_input(INPUT_POST,'password'),
    'message' => 'Logging in user',
);

echo($logindata['type']);
echo($logindata['username']);
sendMessage($logindata);

// Generate a secure auth token
$auth_token = bin2hex(random_bytes(32));

/* next we can store the auth token in the database (or session) associated with the user, but for that we need sinchi's help i am king of clueless here.
Example: saveAuthTokenToDatabase($logindata['username'], $auth_token);
 */


// then set the auth_token cookie
setcookie('auth_token', $auth_token, time() + 3600, '/'); // this will expires in 1 hour

// Generate a secure remember_me token
$remember_me_token = bin2hex(random_bytes(32));

/* again we have to store the remember_me token in the database (or session) associated with the user
 Example: saveRememberMeTokenToDatabase($logindata['username'], $remember_me_token);
 */

// then set the remember_me cookie
setcookie('remember_me', $remember_me_token, time() + (86400 * 30), '/'); // Expires in 30 days

// Redirect to homepage after successful login

//header("Location: homepage.php");
//exit();

?>

