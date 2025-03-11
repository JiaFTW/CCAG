<?php 
require_once('../rabbitmq/testRabbitMQClient.php');

$recipedata = array (
    'type' => 'getRecs',
    'username' => $_COOKIE['username'],
);

//Sends the login request
$response = sendMessage($recipedata);
echo json_encode($response); 

?>

<html>
<div id="results-container">
      <?php
        if (is_array($response) && count($response) > 0) {
          foreach ($response as $recipe) {
            echo '<div class="recipe-card">';
            echo '<h3>' . htmlspecialchars($recipe['name']) . '</h3>';
            echo '<img src="' . htmlspecialchars($recipe['image']) . '"alt="' . htmlspecialchars($recipe['name']) . '"class="recipe-img">';

            //rate and review 
            echo '<button type="button" onclick="toggleReviewForm(' . htmlspecialchars($recipe['rid']) . ')">Rate & Review</button>';
            echo '<form id="reviewForm' . htmlspecialchars($recipe['rid']) . '" action="addReview.php" method="POST" style="display:none;">';
            echo '<input type="hidden" name="recipe_id" value="' . htmlspecialchars($recipe['rid']) . '">';
            echo '<label>Rating:</label>';
            echo '<select name ="rating" required>';
            for ($i = 1; $i <= 5; $i++) {
              echo '<option value="' . $i . '">' .$i . ' Stars</option>';
            }
            echo '</select><br>';
            echo '<label>Review:</label><br>';
            echo '<textarea name="review" required></textarea><br>';
            echo '<input type="submit" value="Submit">';
            echo  '</form>';

            //remove favorite button
            echo '<form action="removefavorite.php" method="POST">';
            echo '<input type="hidden" name="recipe_id" value="' . htmlspecialchars($recipe['rid']) . '">';
            echo '<input type="submit" value="💔 Remove Favorite"> ';
            echo '</form>';
            echo '</div>';
          }
        } else {
          echo "<p>No favorite recipes found.</p>";
        }
      ?>
    </div>
</html>