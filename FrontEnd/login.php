<?php 
require_once('../rabbitmq/testRabbitMQClient.php');

$logindata = array (
    'type' => 'login',
    'username' => filter_input(INPUT_POST,'username'),
    'email' => filter_input(INPUT_POST,'email'),
    'password' => filter_input(INPUT_POST,'password'),
    'message' => 'Logging in user',
);

//Sends the login request
$response = sendMessage($logindata);
error_log("Login response: ".print_r($response, true)); 

if ($response['status'] == 'Success') {
    setcookie("session_token", $response['cookie'],time()+3600,"/");
    setcookie("username", $response['username'], time()+3600,"/");
    header("Location: homepage.php");
    die();
}elseif ($response['status'] === 'EmailNotVerified') {
    header("Location: verifyEmail.php?email=".urlencode($response['email']));
    die();
} else {
    echo "<script>alert('Invalid Credentials');
    window.location.href='loginPage.php';</script>";
    die();
}

?>

