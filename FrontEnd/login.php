<!DOCTYPE html>
<html>
  <head>
    <title>CCAG</title>
  </head>

  <body>
    <h2>Login</h2>
    <form action="authenticate.php" method="POST"> //Form action is where we can do authentication.
      
      <label>Username:</label>
      <input type="text" id=username" name="username" required> <br>

      <label>Password:</label>
      <input type="text" id=password" name="password" required> <br>

      <a href="register.php">New User? Register Here.</a>

      <input type="submit" value="Log In">
    </form>

  <?php 
    //This is where we will put our POST stuff.
  ?>
    
  </body>
</html>
