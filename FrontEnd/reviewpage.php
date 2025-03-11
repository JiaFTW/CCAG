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
        if (!empty($response)) {
          foreach ($response as $recipe) {
            echo '<div class="recipe-card">';
            echo '<h3>' . htmlspecialchars($recipe['name']) . '</h3>';
            echo '<img src="' . htmlspecialchars($recipe['image']) . '"alt="' . htmlspecialchars($recipe['name']) . '"class="recipe-img">';
            echo '<p><strong>Rating: </strong></p>' . htmlspecialchars($recipe['rating']);
            echo '<p><strong>Review: </strong></p>' . htmlspecialchars($recipe['description']);
            //print_r($recipe);
            echo '</div>';
          }
        } else {
          echo "<p>No reviews found.</p>";
        }
      ?>
    </div>
    </body>
</html>