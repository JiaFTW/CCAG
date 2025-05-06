<?php 
require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');
require_once('./logging/sendlogs.php');

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
    setcookie("session_token", $response['cookie'],time()+3600,"/");
    setcookie("username", $response['username'], time()+3600,"/");
    //$message = "has successfully logged in!";
    writelog("logged in.", $logindata['username']);
    //sendinglogs();
    header("Location: homepage.php");
    die();
} else {
    echo "<script>alert('Invalid Credentials');
    window.location.href='loginPage.php';</script>";
    die();
}

?>

