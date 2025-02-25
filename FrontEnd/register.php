<?php 
require_once('../rabbitmq/testRabbitMQClient.php');

$registerdata = array(
    'type' => 'register',
    'username' => filter_input(INPUT_POST,'username'),
    'email' => filter_input(INPUT_POST,'email'),
    'password' => filter_input(INPUT_POST,'password'),
    'message' => 'Registering user',
);

$response = sendMessage($registerdata);

if ($response['success']) {
    header('Location: loginPage.html?message=Successfully Registered.');
    exit();
} 
else {
    header('Location: registerPage.html?message=Regristation failed.');
    exit();
}
?>
