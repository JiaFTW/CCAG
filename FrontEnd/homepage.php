<?php
//session_start();
//$_SESSION['user'] = "testuser";

//Swaps to login page is there is no user logged in.
if(!isset($_SESSION['user'])) {
  echo ("Please <a href='loginPage.html'>Login.</a>");
  exit();
}
/*else {
setcookie("user",$_SESSION['user'],time() + (7*24*60*60),"/");
}*/

?>
<!DOCTYPE html>
<html>
  <head>
    <title>CCAG Homepage</title>
    <link rel="stylesheet" href="./styles/styles.css">
  </head>

  <body>

    <div class="navbar">
      <a href="homepage.php">Home</a>
      <a href="loginPage.html">Login</a>
    </div>

    <p>If you see this you are successfully logged in as <?php echo ($_SESSION['user']); ?></p>
  </body>
  
</html>
