<?php
require_once('../rabbitmq/testRabbitMQClient.php');


$mpRequest = array (
  'type' => 'getUserMealPlans',
  'username' => $_COOKIE['username'],
);

$response = sendMessage($mpRequest);

if (!isset($_COOKIE['session_token'])) {
  header("Location: loginPage.php");
  die();
}

foreach ($response as $recipe){
  echo $recipe['day'];
}

?>


