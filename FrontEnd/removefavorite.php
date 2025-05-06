
<?php
require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');
require_once('./logging/sendlogs.php');

$recipeID = intval(filter_input(INPUT_POST, 'recipe_id'));



$favoriteData = array (
    'type' => 'removeFavorite',
    'username' => $_COOKIE['username'],
    'rid' => $recipeID,
);

writelog("unfavorited recipe: " . $recipeID, $_COOKIE['username']);
sendinglogs();
$response = sendMessage($favoriteData);

header("Location: favoritepage.php");
die();

?>