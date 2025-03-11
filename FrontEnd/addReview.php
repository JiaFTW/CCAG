<?php
require_once('../rabbitmq/testRabbitMQClient.php');

$reviewRequest = array (
    'type' => 'addReview',
    'username' => $_COOKIE['username'],
    'rid' => intval($_POST['recipe_id']),
    'rating' => intval($_POST['rating']),
    'review' => $_POST['review'],
);


$response = sendMessage($reviewRequest);

header("Location: favoritepage.php")
?>