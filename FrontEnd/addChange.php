<?php
require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');

$reviewRequest = array (
    'type' => 'editRecipe',
    'username' => $_COOKIE['username'],
    'rid' => intval($_POST['recipe_id']),
    'name' => $_POST['newRecipeName'],
    'ingredients' => $_POST['newIngredients'],
);

if (!strcmp($_POST['newRecipeName'],$_POST['name'])) {
    $reviewRequest['name'] = $_COOKIE['username'] . "'s " . $_POST['name'];
}

$response = sendMessage($reviewRequest);
writelog("made changes to the recipe: " . $_POST['recipe_id'], $_COOKIE['username']);
header("Location: favoritepage.php")
?>