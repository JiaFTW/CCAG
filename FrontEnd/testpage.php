<?php
require_once('../rabbitmq/testRabbitMQClient.php');


$favoriteRequest = array (
    'type' => 'getFavorites',
    'username' => $_COOKIE['username'],
  );
  
$response = sendMessage($favoriteRequest);

foreach ($response as $recipe) {
    
}



?>


