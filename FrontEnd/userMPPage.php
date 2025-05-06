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

$days = ["Monday", "Tuesday", "Wednesday","Thursday","Friday","Saturday","Sunday"];
$meals = ["Breakfast","Lunch","Dinner"];

function getMeal($array,$day,$meal)
{
  $dayString;
  $mealString;
  switch ($day)
  {
    case 0:
      $dayString = 'Monday';
      break;
    case 1:
      $dayString = 'Tuesday';
      break;
    case 2:
      $dayString = 'Wednesday';
      break;
    case 3:
      $dayString = 'Thursday';
      break;
    case 4:
      $dayString = 'Friday';
      break;
    case 5:
      $dayString = 'Saturday';
      break;
    case 6:
      $dayString = 'Sunday';
      break;
    default:
    return 'You fucked up';
  }

  switch ($meal)
  {
    case 0:
      $mealString = 'Breakfast';
      break;
    case 1:
      $mealString = 'Lunch';
      break;
    case 2:
      $mealString = 'Dinner';
      break;
    default:
    return 'You fucked up';
  }

  foreach($array as $target)
  {
    if($target['day'] == $dayString)
    {
      if($target['meal_type'] == $mealString)
        return $target['recipe_name'];
    }
  }
  return 'No Meal Selected';
}
?>

<html>
    <head>
    <title>Weekly Meal Plan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles/styles.css">
    </head>

    <body>
    <?php include('header.php'); ?>

    <main class="container mt-4">
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
            for ($i=0; $i < 7; $i++) {
              $day = strtoupper(substr($days[$i], 0, 3));
              echo "<tr>";
              echo "<td>{$days[$i]}</td>";

              for ($j=0;$j<count($meals);$j++) {
                $mealId = $day . ($j+1);
                echo "<td>";

                echo getMeal($response, $i, $j);

                echo "</td>";
              }
              echo "</tr>";
            }
          ?>
        </tbody>
      </table>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
</html>
