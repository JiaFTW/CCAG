<?php
//require_once('sessionValidate.php')
if (!isset($_COOKIE['session_token'])) {
  header("Location: loginPage.php");
  die();
}

?>


<html>
    <head>
    <title>CCAG Profile</title>
    <link rel="stylesheet" href="./styles/styles.css">
  </head>
    <body>
    <?php include('header.php'); ?>
    <?php include('headerprofile.php'); ?>
        
    </body>
</html>
