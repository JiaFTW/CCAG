<?php
require_once('../rabbitmq/testRabbitMQClient.php');


$favoriteRequest = array (
    'type' => 'getFavorites',
    'username' => $_POST['rid'],
  );
  
  $response = sendMessage($favoriteRequest);

  print_r($response);

?>


