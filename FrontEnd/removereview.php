<?php

require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');
require_once('./logging/sendlogs.php');

$deletereview = array(
    'type' => 'removeReview',
    'rate_id' => intval($_POST['rate_id']),
);

writelog("removed review: " . $_POST['rate_id'], $_COOKIE['username']);
sendinglogs();
$response = sendMessage($deletereview);

header("location: reviewpage.php");
die();

?>