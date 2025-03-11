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
    <script>
      function toggleReviewForm(recipeId) {
        let form = document.getElementById("reviewForm" + recipeId);
        form.style.display = form.style.display === "none" ? "block" : "none";
      }

      function toggleChangeRecipeForm(recipeId) {
        let form = document.getElementById("changeRecipeForm" + recipeId);
        form.style.display = form.style.display === "none" ? "block" : "none";
      }
    </script>
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


            //change recipe
            echo '<button type="button" onclick="toggleChangeRecipeForm(' . htmlspecialchars($recipe['rid']) . ')">Change Ingredients</button>';
            echo '<form id="changeRecipeForm' . htmlspecialchars($recipe['rid']) . '"action="addChange.php" method="POST" style="display:none;">';
            echo '<label>New Name</label>';
            echo '<input type="hidden" name="recipe_id" value="' . htmlspecialchars($recipe['rid']) . '">';
            echo '<input type="hidden" name="name" value="' . htmlspecialchars($recipe['name']) . '">';
            echo '<input type="text" name="newRecipeName" value="' . $_COOKIE['username'] . "'s " . htmlspecialchars($recipe['name']) . '" required></input>';
            echo '<textarea name="newIngredients" required>' .htmlspecialchars($recipe['ingredients']) .'</textarea><br>';
            echo '<input type="submit" value="Save">';
            echo '</form>';


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
    </body>
</html>
