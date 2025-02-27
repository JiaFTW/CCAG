<?php
//$_SESSION['user'] = "testuser";

// here we are configuring session cookie settings
session_set_cookie_params([
    'lifetime' => 3600, //this would be 1 hour time limit
    'path' => '/',
    'secure' => false, //(Its here when we enable HTTPS, this will only send over HTTPS)
    'httponly' => true, // this will prevent JavaScript access
    'samesite' => 'Strict' // this will prevent CSRF attacks
]);

// Check if the remember_me cookie exists
if (isset($_COOKIE['remember_me'])) {
	$remember_me_token = $_COOKIE['remember_me'];

	//this is when we can validate the user with the database, for now i will be setting a dummy user
/*	$user = validateAuthToken($auth_token);
	if ($user)
       	{
       		 $_SESSION['user'] = $user; // Automatically log the user in
        }
 */
	// we are simulating a valid token
	$_SESSION['user'] = "testuser"; // Replace with actual user data
}

// Generate a CSRF token
$csrf_token = bin2hex(random_bytes(32));

// Store the CSRF token in the session
$_SESSION['csrf_token'] = $csrf_token;

// Set cookies
setcookie('PHPSESSID', session_id(), time() + 3600, '/'); // Session ID cookie
setcookie('auth_token', bin2hex(random_bytes(32)), time() + 3600, '/'); // Authentication token
setcookie('remember_me', bin2hex(random_bytes(32)), time() + (86400 * 30), '/'); // Remember me cookie (30 days)
setcookie('csrf_token', $csrf_token, time() + 3600, '/'); // CSRF token for security


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

    <p>If you see this you are successfully logged in.</p>
  </body>
  
</html>
