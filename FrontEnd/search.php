<?php 
require_once('../rabbitmq/testRabbitMQClient.php');

$recipedata = array (
    'type' => 'getRecipe',
    'username' => $_COOKIE['username'],
    'keyword' => filter_input(INPUT_POST,'search'),
);

//Sends the login request
$response = sendMessage($recipedata);


?>