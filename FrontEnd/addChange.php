<?php
require_once('../rabbitmq/testRabbitMQClient.php');

$reviewRequest = array (
    'type' => 'editRecipe',
    'username' => $_COOKIE['username'],
    'rid' => intval($_POST['recipe_id']),
    'name' => $_POST['newRecipeName'],
    'ingredients' => $_POST['newIngredients'],
);

print_r($reviewRequest);
//$response = sendMessage($reviewRequest);

//header("Location: favoritepage.php")
?>