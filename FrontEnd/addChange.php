<?php
require_once('../rabbitmq/testRabbitMQClient.php');

$reviewRequest = array (
    'type' => 'editRecipe',
    'username' => $_COOKIE['username'],
    'rid' => intval($_POST['recipe_id']),
    'newname' => $_POST['newRecipeName'],
    'ingredients' => $_POST['newIngredients'],
);

echo strcasecmp($_POST['newRecipeName'],$_POST['name']) . PHP_EOL;
if (!strcmp($_POST['newRecipeName'],$_POST['name'])) {
    $reviewRequest['newname'] = $_COOKIE['username'] . "'s " . $_POST['name'];
}


$response = sendMessage($reviewRequest);

header("Location: favoritepage.php")
?>