<?php
if ($_SERVER['REQUEST_METHOD'] === "POST") {

  writelog("SAYS BYEEE", "BOB"); 
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>CCAG</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <?php include('header.php'); ?>
  </head>

  <body>

    <h2>Login</h2>
    <form action="login.php" method="POST"> 
      
      <label>Username:</label>
      <input type="text" id="username" name="username" required> <br>

      <label>Password:</label>
      <input type="text" id="password" name="password" required> <br>
      <input type="submit" value="Log In">

      <h4><a href="registerPage.php">New User? Register Here.</a></h4> <br>
    </form>

  </body>
</html>
