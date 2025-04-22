
<?php
require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');

$recipeID = intval(filter_input(INPUT_POST, 'recipe_id'));



$favoriteData = array (
    'type' => 'removeFavorite',
    'username' => $_COOKIE['username'],
    'rid' => $recipeID,
);

$response = sendMessage($favoriteData);
writelog("unfavorited recipe: " . $recipeID, $_COOKIE['username']);
header("Location: favoritepage.php");
die();

?>