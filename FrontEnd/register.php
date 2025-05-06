<?php 
require_once('../rabbitmq/testRabbitMQClient.php');
require_once('emailConfig.php'); 
require_once('./logging/writelog.php');
require_once('./logging/sendlogs.php');

$registerdata = array(
    'type' => 'register',
    'username' => filter_input(INPUT_POST,'username'),
    'email' => filter_input(INPUT_POST,'email'),
    'password' => filter_input(INPUT_POST,'password'),
    'message' => 'Registering user',
);

$response = sendMessage($registerdata);

writelog("successfully registered.", $logindata['username']);
sendinglogs();
//sendMessage($registerdata);


//header("Location: loginPage.php");
//exit();

if ($response['status'] === 'Success') {
    // Registration successful but not verified
    header("Location: verifyEmail.php?email=".urlencode($registerdata['email']));
    exit();
} else {
    echo "<script>alert('Registration error: ".$response['invalid_type']."');
    window.location.href='registerPage.php';</script>";
    die();
}
?>
