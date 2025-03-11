<?php
require_once('../rabbitmq/testRabbitMQClient.php');

if (!isset($_COOKIE['username'])) {
    $recRids = [];
    return;
}

$recRequest = array (
    'type' => 'getRecs',
    'username' => $_COOKIE['username'],
  );
  
$response = sendMessage($recRequest);

$recRids = array_map(function($recipe) {
    return $recipe['rid'];
}, $response);

?>