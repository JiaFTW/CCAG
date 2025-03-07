<!DOCTYPE html>
<html>
  <head>
    <title>CCAG Homepage</title>
    <link rel="stylesheet" href="./styles/styles.css">
  </head>

  <body>
  <?php if (isset($_COOKIE['session_token'])) { 
    include('header.php'); 
    } else { ?>
  
  <header>
    <div class="navbar">
    
        <div class="nav-left">
            <div class="title">CCAG</div>
            <a href="homepage.php">Homepage</a>
            <a href="page1.php">Page 1</a>
            <a href="page2.php">Page 2</a>
        </div>

                    
        <div class="nav-right">    
            <a href="loginPage.php">Login</a>
            <a href="registerPage.php">Register</a>
        </div>
    </div>
  </header>
  <?php } ?>
  </body>
  
</html>
