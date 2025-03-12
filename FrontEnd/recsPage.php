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
            echo '<div class="recipe-card">';
            echo '<h3>' . $recipe['name'] . '</h3>';
            echo '<img src="' . $recipe['image'] . '"alt="' . $recipe['name'] . '"class="recipe-img">';
            echo '<p><strong>Calories: </strong>' . $recipe['calories'] . 'kcal</p>';
            echo '<p><strong>Servings: </strong>' . $recipe['servings'] . '</p>';
            echo '<p><strong># of Ingredients: </strong>' . $recipe['num_ingredients'] . '</p>';
            echo '<p><strong>Ingredients: </strong>' . $recipe['ingredients'] . '</p>';
            echo '<p><strong>Labels: </strong>' . $recipe['labels_str'] . '</p>';
            echo '<p><strong>RID: </strong>' . $recipe['rid'] . '</p>';
            echo '</div>';
          }
        } else {
          echo "<p>No favorite recipes found.</p>";
        }
      ?>
    </div>


    </body>
</html>
