<?php 
require_once('getDatabase.php');
$db = getDB();

$username = filter_input(INPUT_POST, 'username');
$email = filter_input(INPUT_POST, 'email');
$password = filter_input(INPUT_POST, 'password');
$hash = password_hash($password, PASSWORD_DEFAULT); //hashes the password

$query = 'INSERT INTO accounts
(username,email,password)
VALUES
(:username, :email, :password)';

$statement = $db->prepare($query);
$statement->bindValue(':username', $email);
$statement->bindValue(':email', $email);
$statement->bindValue(':password', $hash);

if ($statement->execute()) {
    echo "User Successfully Register!";
}
else {
    echo "Could not register User" . $statement->error;
}

$statement->close();

?>
