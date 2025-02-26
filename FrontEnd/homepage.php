<?php
session_start();
//$_SESSION['user'] = "testuser";

// here we are configuring session cookie settings
session_set_cookie_params([
    'lifetime' => 3600, //this would be 1 hour time limit
    'path' => '/',
    'secure' => false, //(Its here when we enable HTTPS, this will only send over HTTPS)
    'httponly' => true, // this will prevent JavaScript access
    'samesite' => 'Strict' // this will prevent CSRF attacks
]);


// check if the remember_me cookie exists
if (isset($_COOKIE['remember_me'])) {
    // automatically log the user in using the remember_me token

    $_SESSION['user'] = "testuser"; 
}

// check if the auth_token and session_id cookies exist
if (isset($_COOKIE['auth_token']) && isset($_COOKIE['session_id'])) {
       

// validate the tokens against the database
    require_once('../Database/mysqlconnect.php'); // Include the database connection class

    // connect to the database
    $db = new mysqlConnect('127.0.0.1', 'ccagUser', '12345', 'ccagDB');

    // validate the session
    $validationResult = $db->validateSession($_COOKIE['auth_token'], $_COOKIE['session_id']);

    if ($validationResult['status'] === 'Success') {
        // session is valid
        // fetch user data from the database
        $userData = $db->getUserByToken($_COOKIE['auth_token']); // Added: Fetch user data

	if ($userData) {
            // store user data in the session
            $_SESSION['user'] = $userData; // Added: Store actual user data
	}
       	else
       	{
            // user data not found
            echo "User data not found. Please <a href='loginPage.html'>log in</a>.";
            exit();
        }
   
    }
   
    else 
    {
        // session is invalid or expired
        echo "Session expired or invalid. Please <a href='loginPage.html'>log in</a>.";
        exit();
    }
} else {
    // cookies are missing
    echo "Please <a href='loginPage.html'>log in</a>.";
    exit();
}

// generate a CSRF token
$csrf_token = bin2hex(random_bytes(32));

// store the CSRF token in the session
$_SESSION['csrf_token'] = $csrf_token;

// set cookies
setcookie('PHPSESSID', session_id(), time() + 3600, '/'); // Session ID cookie
setcookie('csrf_token', $csrf_token, time() + 3600, '/'); // CSRF token for security

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
