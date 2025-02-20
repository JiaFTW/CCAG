<?php 
//require_once('testRabbitMQClient.php');

$registerdata = array(
    'username' => filter_input(INPUT_POST,'username'),
    'email' => filter_input(INPUT_POST,'email'),
    'password' => filter_input(INPUT_POST,'password')
);

//sendMessage($registerdata);
print_r($registerdata['username']);
print_r($registerdata['email']); 
print_r($registerdata['password']); 
?>
