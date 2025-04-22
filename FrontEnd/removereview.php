<?php

require_once('../rabbitmq/testRabbitMQClient.php');

$deletereview = array(
    'type' => 'removeReview',
    'rate_id' => intval($_POST['rate_id']),
);


$response = sendMessage($deletereview);

header("location: reviewpage.php");
die();

?>