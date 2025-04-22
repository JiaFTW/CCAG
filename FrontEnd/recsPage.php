<?php
require_once('../rabbitmq/testRabbitMQClient.php');

$umpRequest = array (
  'type' => 'getRex',
  'username' => $_COOKIE['username'],
);

$response = sendMessage($umpRequest);

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
    </head>

    <body>
    <?php include('header.php'); ?>
    <?php include('headerprofile.php'); ?>

    <div id="results-container" class="container mt-4">
      <?php
        if (is_array($response) && count($response) > 0) {
          foreach ($response as $recipe) {
            foreach ($recipe as $rec) {
              echo '<div class="recipe-card card mb-4 p-3">';
              echo '<h3 class="card-title">' . $rec['name'] . '</h3>';
              echo '<img src="' . $rec['image'] . '" alt="' . $rec['name'] . '" class="recipe-img card-img-top">';
              echo '<div class="card-body">';
              echo '<p><strong>Calories: </strong>' . $rec['calories'] . 'kcal</p>';
              echo '<p><strong>Servings: </strong>' . $rec['servings'] . '</p>';
              echo '<p><strong># of Ingredients: </strong>' . $rec['num_ingredients'] . '</p>';
              echo '<p><strong>Ingredients: </strong>' . $rec['ingredients'] . '</p>';
              echo '<p><strong>Labels: </strong>' . $rec['labels_str'] . '</p>';
              echo '<p><strong>RID: </strong>' . $rec['rid'] . '</p>';
              echo '</div>';
              echo '</div>';
            }
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

