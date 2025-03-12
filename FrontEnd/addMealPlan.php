<?php
require_once('../rabbitmq/testRabbitMQClient.php');

$favoriteRequest = array (
    'type' => 'getFavorites',
    'username' => $_COOKIE['username'],
  );
  
  
$response = sendMessage($favoriteRequest);

/*$addMealPlan = array (
    'type' => ,
    'username' => ,
    'rid' => ,


    //Monday
    'MON1' => ,
    'MON2' => ,
    'MON3' => ,

    //Tuesday
    'TUE1' => ,
    'TUE2' => ,
    'TUE3' => ,

    //Wednesday
    'WED1' => ,
    'WED2' => ,
    'WED3' => ,

    //Thursday
    'THU1' => ,
    'THU2' => ,
    'THU3' => ,

    //Friday
    'FRI1' => ,
    'FRI2' => ,
    'FRI3' => ,

    //Saturday
    'SAT1' => ,
    'SAT2' => ,
    'SAT3' => ,

    //Sunday
    'SUN1' => ,
    'SUN2' => ,
    'SUN3' => ,
);*/


$response = sendMessage($addMealPlan);

header("Location: mppage.php");
die();
?>