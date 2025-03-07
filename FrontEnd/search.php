<?php 
require_once('../rabbitmq/testRabbitMQClient.php');

$recipedata = array (
    'type' => 'getRecipe',
    'keyword' => filter_input(INPUT_POST,'search'),
);

//Sends the login request
$response = sendMessage($recipedata);

?>