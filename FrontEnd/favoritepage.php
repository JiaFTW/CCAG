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
            echo '<h3>' . $recipe['name'] . '</h3>';
            echo '<img src="' . $recipe['image'] . '"alt="' . $recipe['name'] . '"class="recipe-img">';
            echo '<p><strong>Calories: </strong>' . $recipe['calories'] . 'kcal</p>';
            echo '<p><strong>Servings: </strong>' . $recipe['servings'] . '</p>';
            echo '<p><strong># of Ingredients: </strong>' . $recipe['num_ingredients'] . '</p>';
            echo '<p><strong>Ingredients: </strong>' . $recipe['ingredients'] . '</p>';
            echo '<p><strong>Labels: </strong>' . $recipe['labels_str'] . '</p>';
            echo '<p><strong>RID: </strong>' . $recipe['rid'] . '</p>';

            //rate and review 
            echo '<button type="button" class="smol-button" onclick="toggleReviewForm(' . $recipe['rid'] . ')">Rate & Review</button>';
            echo '<form id="reviewForm' . $recipe['rid'] . '" action="addReview.php" method="POST" style="display:none;">';
            echo '<input type="hidden" name="recipe_id" value="' . $recipe['rid'] . '">';
            echo '<label>Rating:</label>';
            echo '<select name ="rating" required>';
            for ($i = 1; $i <= 5; $i++) {
              echo '<option value="' . $i . '">' .$i . ' Stars</opticlass="smol-button"on>';
            }
            echo '</select><br>';
            echo '<label>Review:</label><br>';
            echo '<textarea name="review" required></textarea><br>';
            echo '<input type="submit" value="Submit">';
            echo  '</form>';


            //change recipe
            echo '<button type="button" class="smol-button" onclick="toggleChangeRecipeForm(' . $recipe['rid'] . ')">Change Ingredients</button>';
            echo '<form id="changeRecipeForm' . $recipe['rid'] . '"action="addChange.php" method="POST" style="display:none;">';
            echo '<label>' . $_COOKIE['username'] . "'s " . $recipe['name'] . '</label>';
            echo '<input type="hidden" name="recipe_id" value="' . $recipe['rid'] . '">';
            echo '<input type="hidden" name="name" value="' . $recipe['name'] . '">';
            echo '<input type="hidden" name="newRecipeName" value="' . $_COOKIE['username'] . "'s " . $recipe['name'] . '" required></input>';
            echo '<textarea name="newIngredients" required>' . $recipe['ingredients'] .'</textarea><br>';
            echo '<input type="submit" value="Save">';
            echo '</form>';


            //remove favorite button
            echo '<form action="removefavorite.php" method="POST">';
            echo '<input type="hidden" name="recipe_id" value="' . $recipe['rid'] . '">';
            echo '<input type="submit" class="smol-button" value="💔 Remove Favorite"> ';
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
