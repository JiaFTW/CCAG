<?php 
require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');

$registerdata = array(
    'type' => 'register',
    'username' => filter_input(INPUT_POST,'username'),
    'email' => filter_input(INPUT_POST,'email'),
    'password' => filter_input(INPUT_POST,'password'),
    'message' => 'Registering user',
);

echo ($registerdata['username']);
sendMessage($registerdata);

header("Location: loginPage.php");
exit();

?>
