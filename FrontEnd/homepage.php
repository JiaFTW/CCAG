<!DOCTYPE html>
<html>
  <head>
    <title>CCAG Homepage</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  </head>

  <body>
  <?php if (isset($_COOKIE['session_token'])) { 
    include('header.php'); ?>
    <h2>WELCOME TO THE THE CCAG RECIPE DATABASE</h2> <br>
                <p>Find your new favorite recipe here!</p> <br>
                <p>Login to use all relevant features!</p> <br>
                <p>Once you see a recipe you like, favorite it!</p> <br>
                <p>Go to your profile page to set your diet restrictions, rate & review your favorite recipes, manage your reviews, and even make changes to your favorite recipes!</p> <br>
                <p>Future Features: Add weekly mealplan and view it under your profile page.</p> <br> 
   <?php } else { ?>
  
  <header>
    <div class="navbar">
    
        <div class="nav-left">
            <div class="title">CCAG</div>
            <a href="homepage.php">Homepage</a>
            <a href="searchPage.php">Recipes</a>
        </div>          
        <div class="nav-right">    
            <a href="loginPage.php">Login</a>
            <a href="registerPage.php">Register</a>
        </div>
    </div>
  </header>

            <h2>WELCOME TO THE THE CCAG RECIPE DATABASE</h2> <br>
                <p>Find your new favorite recipe here!</p> <br>
                <p>Login to use all relevant features!</p> <br>
                <p>Once you see a recipe you like, favorite it!</p> <br>
                <p>Go to your profile page to set your diet restrictions, rate & review your favorite recipes, manage your reviews, and even make changes to your favorite recipes!</p> <br>
                <p>Future Features: Add weekly mealplan and view it under your profile page.</p> <br> 
  <?php } ?>
  </body>
  
</html>

  
</html>
