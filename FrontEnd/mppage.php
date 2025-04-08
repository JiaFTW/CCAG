<?php
require_once('../rabbitmq/testRabbitMQClient.php');

$favoriteRequest = array (
  'type' => 'getFavorites',
  'username' => $_COOKIE['username'],
);

$response = sendMessage($favoriteRequest);

if (!isset($_COOKIE['session_token'])) {
  header("Location: loginPage.php");
  die();
}
?>

<html>
    <head>
    <title>Add Weekly Meal Plan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles/styles.css">
    </head>
    <body>
    <?php include('header.php'); ?>

    <main class="container mt-4">
      <form method="POST" class="mealPlanForm" action="addMealPlan.php">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Day</th>
              <th>Breakfast</th>
              <th>Lunch</th>
              <th>Dinner</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $days = ["Monday", "Tuesday", "Wednesday","Thursday","Friday","Saturday","Sunday"];
            $meals = ["Breakfast","Lunch","Dinner"];

            for ($i=0; $i < 7; $i++) {
              $day = strtoupper(substr($days[$i], 0, 3));
              echo "<tr>";
              echo "<td>{$days[$i]}</td>";

              for ($j=0;$j<count($meals);$j++) {
                $mealId = $day . ($j+1);
                echo "<td>";
                echo "<select name='$mealId' id='$mealId' class='form-control' required>";
                echo "<option value='' disabled selected>Select a recipe...</option>";

                foreach ($response as $recipe) {
                  echo "<option value='{$recipe['rid']}'>{$recipe['name']}</option>";
                }

                echo "</select><br>";
                echo "</td>";
              }
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
        <br>
        <input type="submit" class="smol-button btn btn-primary" name="submit" value="Save">
      </form>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
</html>


