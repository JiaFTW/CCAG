<?php 
require_once('../rabbitmq/testRabbitMQClient.php');

$recipeID = filter_input(INPUT_POST, 'recipe_id');

$favoriteData = array (
    'type' => 'addFavorite',
    'username' => $_COOKIE['username'],
    'rid' => $recipeID,
);


$response = sendMessage($favoriteData);


if ($response['status'] === 'Success') {
    echo "Success";

}
else {
    echo "Error";
}


?>