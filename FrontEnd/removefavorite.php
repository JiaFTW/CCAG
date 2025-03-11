
<?php
require_once('../rabbitmq/testRabbitMQClient.php');

$recipeID = intval(filter_input(INPUT_POST, 'recipe_id'));



$favoriteData = array (
    'type' => 'removeFavorite',
    'username' => $_COOKIE['username'],
    'rid' => $recipeID,
);

$response = sendMessage($favoriteData);

header("Location: favoritepage.php");
die();
?>