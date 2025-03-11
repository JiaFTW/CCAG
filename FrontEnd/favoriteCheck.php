<?php
require_once('../rabbitmq/testRabbitMQClient.php');


$favoriteRequest = array (
    'type' => 'getFavorites',
    'username' => $_COOKIE['username'],
  );
  
$response = sendMessage($favoriteRequest);

$favoriteRids = array_map(function($recipe) {
    return $recipe['rid'];
}, $response);

echo json_encode($favoriteRids);



?>