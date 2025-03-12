<?php
require_once('../rabbitmq/testRabbitMQClient.php');

$favoriteRequest = array (
  'type' => 'getFavorites',
  'username' => $_COOKIE['username'],
);

$response = sendMessage($favoriteRequest);


?>


<html>
    <head>
    <title>Add Weekly Meal Plan</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <script>
      function validateForm() {
        
      }
    </script>



  </head>
    <body>
    
    <form method="POST" onsubmit="return validateForm()">
    <table>
      <tr>
        <th>Day</th>
        <th>Breakfast</th>
        <th>Lunch</th>
        <th>Dinner</th>
      </tr>
      <?php include('header.php'); ?>
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
          echo "<label for='$mealId'> {$meals[$j]}: </label>";
          echo "<select name='$mealId' id='$mealId' required>";
          
          foreach ($response as $recipe) {
            echo "<option value='{$recipe['rid']}'>{$recipe['name']}</option>";
          }

          echo "</select><br>";
          echo "</td>";
        }
        echo "</tr>";
      }
      ?>
      </table>
        <br>
      <input type="submit" class="save-button" name="submit" value="Save">
    </form>











    </body>
</html>