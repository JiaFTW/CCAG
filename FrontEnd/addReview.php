<?php
require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');

$reviewRequest = array (
    'type' => 'addReview',
    'username' => $_COOKIE['username'],
    'rid' => intval($_POST['recipe_id']),
    'rating' => intval($_POST['rating']),
    'review' => $_POST['review'],
);


$response = sendMessage($reviewRequest);
writelog("added a review for recipe: " . $_POST['recipe_id'], $_COOKIE['username']);

header("Location: favoritepage.php")
?>