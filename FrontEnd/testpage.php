<?php
require_once('../rabbitmq/testRabbitMQClient.php');


$favoriteRequest = array (
    'type' => 'getFavorites',
    'username' => $_COOKIE['username'],
  );
  
  $response = sendMessage($favoriteRequest);

  print_r($response);

?>


