<?php
require_once('../rabbitmq/testRabbitMQClient.php');

if (!isset($_COOKIE['username'])) {
    $favoriteRids = [];
    return;
}

$favoriteRequest = array (
    'type' => 'getFavorites', //change this to getRecs soon.
    'username' => $_COOKIE['username'],
  );
  
$response = sendMessage($favoriteRequest);

$favoriteRids = array_map(function($recipe) {
    return $recipe['rid'];
}, $response);

?>