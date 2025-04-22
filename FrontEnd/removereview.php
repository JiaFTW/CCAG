<?php

require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');

$deletereview = array(
    'type' => 'removeReview',
    'rate_id' => intval($_POST['rate_id']),
);


$response = sendMessage($deletereview);
writelog("removed review: " . $_POST['rate_id'], $_COOKIE['username']);
header("location: reviewpage.php");
die();

?>