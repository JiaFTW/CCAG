<?php
require_once('../rabbitmq/testRabbitMQClient.php');

$umpRequest = array (
  'type' => 'getRex',
  'username' => $_COOKIE['username'],
);

$response = sendMessage($umpRequest);

if (!isset($_COOKIE['session_token'])) {
  header("Location: loginPage.php");
  die();
}
?>


<html>

    <head>
    <title>CCAG Profile</title>
    <link rel="stylesheet" href="./styles/styles.css">
    </head>


    <body>
    <?php include('header.php'); ?>
    <?php include('headerprofile.php'); ?>

    <div id="results-container">
      <?php
        if (is_array($response) && count($response) > 0) {
          foreach ($response as $recipe) {
            foreach ($recipe as $rec) {
            echo '<div class="recipe-card">';
            echo '<h3>' . $rec['name'] . '</h3>';
            echo '<img src="' . $rec['image'] . '"alt="' . $rec['name'] . '"class="recipe-img">';
            echo '<p><strong>Calories: </strong>' . $rec['calories'] . 'kcal</p>';
            echo '<p><strong>Servings: </strong>' . $rec['servings'] . '</p>';
            echo '<p><strong># of Ingredients: </strong>' . $rec['num_ingredients'] . '</p>';
            echo '<p><strong>Ingredients: </strong>' . $rec['ingredients'] . '</p>';
            echo '<p><strong>Labels: </strong>' . $rec['labels_str'] . '</p>';
            echo '<p><strong>RID: </strong>' . $rec['rid'] . '</p>';

            
            echo '</div>';
            }
          }
        } else {
          echo "<p>No favorite recipes found.</p>";
        }
      ?>
    </div>


    </body>
</html>
