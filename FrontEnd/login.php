<?php 
require_once('../rabbitmq/testRabbitMQClient.php');



$logindata = array (
    'type' => 'login',
    'username' => filter_input(INPUT_POST,'username'),
    'email' => filter_input(INPUT_POST,'email'),
    'password' => filter_input(INPUT_POST,'password'),
    'message' => 'Logging in user',
);

$response = sendMessage($logindata);

if ($response['success']) {
    header('Location: loginPage.html?message=Logged in.');
    exit();
} 
else {
    header('Location: loginPage.html?message=Invalid Username/Password.');
    exit();
}
//header("Location: homepage.php");
//exit();

?>

