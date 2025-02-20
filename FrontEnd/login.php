<?php 
require_once('getDatabase.php');

//Initializes $_SESSION so we can store variables to be used across multiple pages, will mainly used for keeping the user logged in when switching pages when we get basic authentication up and working.
//session_start();

$db = getDB();

$username = filter_input(INPUT_POST, 'username');
$password = filter_input(INPUT_POST, 'password');

$query = 'SELECT password FROM accounts WHERE username = :username';

$statement = $db->prepare($query);
$statement->bindValue(':username', $username);
$statement->execute();
$row = $statement->fetch();
$statement->close();  

if ($row === false) {
  return false;
}
else {
  $hash = $row['password']; 
  if (password_verify($password, $hash)) { // https://www.php.net/manual/en/function.password-verify.php
    echo 'Password is Valid';
  }
  else {
    echo 'Invalid credentials.';
  }
}

?>
