<?php 
require_once('../rabbitmq/testRabbitMQClient.php');



$logindata = array (
    'type' => 'login',
    'username' => filter_input(INPUT_POST,'username'),
    'email' => filter_input(INPUT_POST,'email'),
    'password' => filter_input(INPUT_POST,'password'),
    'message' => 'Logging in user',
);

sendMessage($logindata);

//header("Location: homepage.php");
//exit();

?>
