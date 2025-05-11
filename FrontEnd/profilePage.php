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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  </head>
    <body>
    <?php include('header.php'); ?>
    <?php include('headerprofile.php'); ?>

    </body>
</html>
