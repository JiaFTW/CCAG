<?php
require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');
require_once('./logging/sendlogs.php');

$reviewRequest = array (
    'type' => 'addReview',
    'username' => $_COOKIE['username'],
    'rid' => intval($_POST['recipe_id']),
    'rating' => intval($_POST['rating']),
    'review' => $_POST['review'],
);

writelog("added a review for recipe: " . $_POST['recipe_id'], $_COOKIE['username']);
sendinglogs();
$response = sendMessage($reviewRequest);

header("Location: favoritepage.php")
?>