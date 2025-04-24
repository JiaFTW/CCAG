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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
     
    <div id="results-container" class="container mt-4">
      <?php
        if (is_array($response) && count($response) > 0) {
          foreach ($response as $recipe) {
            echo '<div class="recipe-card card mb-4 p-3">';
            echo '<h3 class="card-title">' . $recipe['name'] . '</h3>';
            echo '<img src="' . $recipe['image'] . '" alt="' . $recipe['name'] . '" class="recipe-img card-img-top">';
            echo '<div class="card-body">';
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
            echo '<select name="rating" class="form-control" required>';
            for ($i = 1; $i <= 5; $i++) {
              echo '<option value="' . $i . '">' . $i . ' Stars</option>';
            }
            echo '</select><br>';
            echo '<label>Review:</label><br>';
            echo '<textarea name="review" class="form-control" required></textarea><br>';
            echo '<input type="submit" class="smol-button btn btn-success mt-2" value="Submit">';
            echo '</form>';

            //change recipe
            echo '<button type="button" class="smol-button" onclick="toggleChangeRecipeForm(' . $recipe['rid'] . ')">Change Ingredients</button>';
            echo '<form id="changeRecipeForm' . $recipe['rid'] . '" action="addChange.php" method="POST" style="display:none;">';
            echo '<label>' . $_COOKIE['username'] . "'s " . $recipe['name'] . '</label>';
            echo '<input type="hidden" name="recipe_id" value="' . $recipe['rid'] . '">';
            echo '<input type="hidden" name="name" value="' . $recipe['name'] . '">';
            echo '<input type="hidden" name="newRecipeName" value="' . $_COOKIE['username'] . "'s " . $recipe['name'] . '" required>';
            echo '<textarea name="newIngredients" class="form-control" required>' . $recipe['ingredients'] . '</textarea><br>';
            echo '<input type="submit" class="smol-button btn btn-primary mt-2" value="Save">';
            echo '</form>';

            //remove favorite button
            echo '<form action="removefavorite.php" method="POST">';
            echo '<input type="hidden" name="recipe_id" value="' . $recipe['rid'] . '">';
            echo '<input type="submit" class="smol-button btn btn-danger mt-2" value="💔 Remove Favorite"> ';
            echo '</form>';
            echo '</div>';
            echo '</div>';
          }
        } else {
          echo "<p>No favorite recipes found.</p>";
        }
      ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
</html>

