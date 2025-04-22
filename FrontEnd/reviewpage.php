<?php
require_once('../rabbitmq/testRabbitMQClient.php');

if (!isset($_COOKIE['session_token'])) {
  header("Location: loginPage.php");
  die();
}

$getReview = array (
  'type' => 'getUserReviews',
  'username' => $_COOKIE['username'],
);

$response = sendMessage($getReview);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CCAG Profile</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="./styles/styles.css">
</head>
<body>

  <?php include('header.php'); ?>
  <?php include('headerprofile.php'); ?>

  <div class="container mt-5">
    <div class="row">
      <?php
        if (is_array($response) && count($response) > 0) {
          foreach ($response as $recipe) {
            echo '<div class="col-md-4 mb-4">';
            echo '<div class="recipe-card">';
            echo '<img src="' . $recipe['image'] . '" class="recipe-img" alt="' . $recipe['name'] . '">';
            echo '<div class="card-body">';
            echo '<h3>' . $recipe['name'] . '</h3>';
            echo '<p><strong>Rating:</strong> ' . $recipe['rating'] . '/5 ⭐</p>';
            echo '<p><strong>Review:</strong> ' . $recipe['description'] . '</p>';
            echo '<p><strong>Calories:</strong> ' . $recipe['calories'] . ' kcal</p>';
            echo '<p><strong>Servings:</strong> ' . $recipe['servings'] . '</p>';
            echo '<p><strong># of Ingredients:</strong> ' . $recipe['num_ingredients'] . '</p>';
            echo '<p><strong>Ingredients:</strong> ' . $recipe['ingredients'] . '</p>';
            echo '<p><strong>Labels:</strong> ' . $recipe['labels_str'] . '</p>';
            echo '<p><strong>RID:</strong> ' . $recipe['rid'] . '</p>';
            echo '<form action="removereview.php" method="POST">';
            echo '<input type="hidden" name="rate_id" value="' . $recipe['rate_id'] . '">';
            echo '<button type="submit" class="smol-button">Remove Review</button>';
            echo '</form>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
          }
        } else {
          echo "<p>No reviews found.</p>";
        }
      ?>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

