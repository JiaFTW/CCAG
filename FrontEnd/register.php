<?php 
require_once('../rabbitmq/testRabbitMQClient.php');
require_once('emailConfig.php'); 

$registerdata = array(
    'type' => 'register',
    'username' => filter_input(INPUT_POST,'username'),
    'email' => filter_input(INPUT_POST,'email'),
    'password' => filter_input(INPUT_POST,'password'),
    'message' => 'Registering user',
);

$response = sendMessage($registerdata);

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
