<?php 
require_once('getDatabase.php');

//Initializes $_SESSION so we can store variables to be used across multiple pages, will mainly used for keeping the user logged in when switching pages when we get basic authentication up and working.
//session_start();

$db = getDB();

$query = 'SELECT password FROM accounts WHERE email = :email';
$statement = $db->prepare($query);
$statement->bindValue(':email', $email);
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
