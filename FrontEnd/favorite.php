<?php 
require_once('../rabbitmq/testRabbitMQClient.php');

$recipeID = intval(filter_input(INPUT_POST, 'recipe_id'));



$favoriteData = array (
    'type' => 'addFavorite',
    'username' => $_COOKIE['username'],
    'rid' => $recipeID,
);

print_r($favoriteData);
$response = sendMessage($favoriteData);
?>