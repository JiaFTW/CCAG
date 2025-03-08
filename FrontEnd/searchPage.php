<?php
require_once('sessionValidate.php');
?>


<html>
    <head>
    <title>CCAG Search Page</title>
    <link rel="stylesheet" href="./styles/styles.css">
  </head>
    <body>
    <?php include('header.php'); ?>

        <div class="search-container">
            <form action="search.php" method="POST">
                <input type="text" name="search" class="search-box" placeholder="Search recipes..." required>
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>

<!--PLEASE WORKKKKKKKKKKKKKKKKKKKKKKKKKK  -->

        <div class="results-container">
            
        </div>
    </body>
</html>