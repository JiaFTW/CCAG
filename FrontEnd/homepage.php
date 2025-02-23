<?php
session_start();
$_SESSION['username'] = "testuser";

//Swaps to login page is there is no user logged in.
if(!isset($_SESSION['username'])) {
  header("Location: loginPage.html");
  exit();
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>CCAG Homepage</title>
    <link rel="stylesheet" href="./styles/styles.css">
  </head>

  <body>
    <p>If you see this you are successfully logged in as <?php echo ($_SESSION['username']); ?></p>
  </body>
  
</html>
