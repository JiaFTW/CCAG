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
        if (!empty($response)) {
          foreach ($response as $recipe) {
            echo '<div class="recipe-card">';
            echo '<h3>' . htmlspecialchars($recipe['name']) . '</h3>';
            echo '<img src="' . htmlspecialchars($recipe['image']) . '"alt="' . htmlspecialchars($recipe['name']) . '"class="recipe-img">';
            echo '<form>';
            echo '<input type="submit" value="Rate & Review">';
            echo  '</form>';
            echo '<form action="removefavorite.php" method="POST">';
            echo '<input type="hidden" name="rid" value="' . htmlspecialchars($recipe['rid']) . '">';
            echo '<input type="submit" value="Remove Favorite"> ';
            echo '</form>';
            echo '</div>';
          }
        } else {
          echo "<p>No favorite recipes found.</p>";
        }
      ?>
    </div>
    </body>
</html>
