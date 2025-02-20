<?php 
//require_once('testRabbitMQClient.php');

$registerdata = array(
    'username' => filter_input(INPUT_POST,'username'),
    'email' => filter_input(INPUT_POST,'email'),
    'password' => filter_input(INPUT_POST,'password')
);

print_r($registerdata);
print_r($registerdata[0]);
print_r($registerdata[1]);
print_r($registerdata[2]);
print_r($registerdata[3]);
?>
