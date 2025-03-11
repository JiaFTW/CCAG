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
            echo '<h3>' . htmlspecialchars($recipe['name']) . '</h3>';
            echo '<img src="' . htmlspecialchars($recipe['image']) . '"alt="' . htmlspecialchars($recipe['name']) . '"class="recipe-img">';
            echo '<p><strong>Rating: </strong></p>' . htmlspecialchars($recipe['rating']) . '/5 Stars';
            echo '<p><strong>Review: </strong></p>' . htmlspecialchars($recipe['description']);
            //print_r($recipe);

            //remove review
            echo '<form action="removereview.php" method="POST">';
            echo '<input type="hidden" name="rate_id" value="' . htmlspecialchars($recipe['rate_id']) . '">';
            echo '<button type="submit">Remove Review</button>';
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