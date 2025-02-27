<?php 
require_once('../rabbitmq/testRabbitMQClient.php');

/* Validate CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_COOKIE['csrf_token'])) {
        die("CSRF token missing.");
    }
    if ($_POST['csrf_token'] !== $_COOKIE['csrf_token']) {
        die("CSRF token validation failed.");
    }
}
*/

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
    
    
    $sessionId = array (
        'type' => 'validate_session',
        'sessionId' => $response['cookie'],
    );
    
    $sessioncheck = sendMessage($sessionId);

    //print_r($sessioncheck);
    //TEST CODE FOR $_SESSION ON CLIENT SIDE//
    //session_start();
    //$_SESSION['is_valid_user'] = true;
    //$_SESSION['user'] = $logindata['username']; 
    //TEST CODE FOR $_SESSION ON CLIENT SIDE//

    //header("Location: homepage.php");
    //die();
} else {
    echo "<script>alert('Invalid Credentials');
    window.location.href='loginPage.php';</script>";
    die();
}


?>

