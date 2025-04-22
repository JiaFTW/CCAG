<?php
require_once('../rabbitmq/testRabbitMQClient.php');

/*$favoriteRequest = array (
  'type' => 'getUserMealPlans',
  'username' => $_COOKIE['username'],
);*/
//$response = sendMessage($favoriteRequest);

if (!isset($_COOKIE['session_token'])) {
  header("Location: loginPage.php");
  die();
}
?>


<html>
    <head>
    <title>Add Weekly Meal Plan</title>
    <link rel="stylesheet" href="./styles/styles.css">

  </head>
    <body>
    <?php include('header.php'); ?>

  <main>
    <table>
      <tr>
        <th>Day</th>
        <th>Breakfast</th>
        <th>Lunch</th>
        <th>Dinner</th>
      </tr>
        <?php
        $days = ["Monday", "Tuesday", "Wednesday","Thursday","Friday","Saturday","Sunday"];
        $meals = ["Breakfast","Lunch","Dinner"];

      
      for ($i=0; $i < 7; $i++) {
        $day = strtoupper(substr($days[$i], 0, 3));
        echo "<tr>";
        echo "<td>{$days[$i]}</td>";

        for ($j=0;$j<count($meals);$j++) {
          $mealId= $day . ($j+1);
          echo "<td>";
            echo "$mealId";
          /*foreach ($response as $recipe) {

            

          }*/

          echo "</td>";
        }
        echo "</tr>";
      }
      ?>
      </table>
        <br>
  </main>
    </body>
</html>