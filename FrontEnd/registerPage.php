
<!DOCTYPE html>
<html>
  <head>
    <title>CCAG Register</title>
<<<<<<< HEAD:FrontEnd/registerPage.html
=======
    <link rel="stylesheet" href="./styles/styles.css">
    <?php include('header.php'); ?>
>>>>>>> yummy_cookies:FrontEnd/registerPage.php
  </head>
  
<script>
  //Script to validate password(s).
  function matchingPasswords() {
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirmPassword").value;

    if (password !== confirmPassword) {
      alert("Passwordss don't match.");
      return false; //Doesn't submit form
  }
    return true; //Submits the form
  }
</script>
  
  <body>
<<<<<<< HEAD:FrontEnd/registerPage.html
=======

>>>>>>> yummy_cookies:FrontEnd/registerPage.php
    <h2>Register</h2>
    <form action="register.php" method="POST" onsubmit="return matchingPasswords()"> <!-- Inputs the text field to the php file. And calls the matchingPasswords function.-->
      
      <label for="username">Username:</label>
      <input type="text" id=username" name="username" required> <br>

      <h5>Passwords must match and special characters allowed.</h5>
      <label for="password">Password:</label>
      <input type="password" id="password" pattern="[A-Za-z0-9]+" name="password" required> <br>

      <label for="confirmPassword">Confirm Password:</label>
      <input type="password" id="confirmPassword" pattern="[A-Za-z0-9]+" name="confirmPassword" required> <br>
      
      <input type="submit" value="Log In">

    </form>
  
  </body>
</html>
