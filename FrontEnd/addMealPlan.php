<?php
require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');
require_once('./logging/sendlogs.php');

$days = ["Monday", "Tuesday", "Wednesday","Thursday","Friday","Saturday","Sunday"];
$meals = ["Breakfast","Lunch","Dinner"];

$addMealPlan = array (
  'type' => 'addMealPlan',
  'username' => $_COOKIE['username'],
);


for ($i=0; $i < 7; $i++) {
  $day = strtoupper(substr($days[$i], 0, 3));
  for ($j=0;$j<3;$j++) {
    $mealId = $day . ($j + 1);
    $addMealPlan[$mealId] = $_POST[$mealId];
  }

}

//print_r($addMealPlan);

writelog("added a meal plan", $_COOKIE['username']);
sendinglogs();
$response = sendMessage($addMealPlan);

header("Location: mppage.php");
die();


/*$addMealPlan = array (
    'type' => ,
    'username' => ,

    //Monday
    'MON1' => [RECIPE RIDs] ,
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


?>
