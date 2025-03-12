<?php
require_once('../rabbitmq/testRabbitMQClient.php');

$getReview = array (
  'type' => 'getUserReviews',
  'username' => $_COOKIE['username'],
);

$response = sendMessage($getReview);

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
            echo '<p><strong>Rating: </strong></p>' . $recipe['rating'] . '/5 ⭐';
            echo '<p><strong>Review: </strong></p>' . $recipe['description'];

            echo '<h3>' . $recipe['name'] . '</h3>';
            echo '<p><strong>Calories: </strong>' . $recipe['calories'] . 'kcal</p>';
            echo '<p><strong>Servings: </strong>' . $recipe['servings'] . '</p>';
            echo '<p><strong># of Ingredients: </strong>' . $recipe['num_ingredients'] . '</p>';
            echo '<p><strong>Ingredients: </strong>' . $recipe['ingredients'] . '</p>';
            echo '<p><strong>Labels: </strong>' . $recipe['labels_str'] . '</p>';
            echo '<p><strong>RID: </strong>' . $recipe['rid'] . '</p>';
            //print_r($recipe);

            //remove review
            echo '<form action="removereview.php" method="POST">';
            echo '<input type="hidden" name="rate_id" value="' . $recipe['rate_id'] . '">';
            echo '<button type="submit" class="smol-button">Remove Review</button>';
            echo '</form>';
            echo '</div>';
          }
        } else {
          echo "<p>No reviews found.</p>";
        }
      ?>
    </div>
    </body>
</html>