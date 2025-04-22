<?php 
require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');

$recipeID = intval(filter_input(INPUT_POST, 'recipe_id'));



$favoriteData = array (
    'type' => 'addFavorite',
    'username' => $_COOKIE['username'],
    'rid' => $recipeID,
);

$response = sendMessage($favoriteData);
writelog("favorited recipe: " . $recipeID, $_COOKIE['username']);
?>