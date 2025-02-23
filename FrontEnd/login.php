<?php 
require_once('../rabbitmq/testRabbitMQClient.php');



$logindata = array (
    'type' => 'login',
    'username' => filter_input(INPUT_POST,'username'),
    'email' => filter_input(INPUT_POST,'email'),
    'password' => filter_input(INPUT_POST,'password'),
    'message' => 'Logging in user',
);

echo($logindata['type']);
echo($logindata['username']);
sendMessage($logindata);

/* if doLogin($logindata['username'], $logindata['password']) {
echo ("User successfully logged in!");
session_start();
$_SESSION['user'] = $logindata['username'];
setcookie("user", $logindata['username'], time() + (7*24*60*60), "/");
}
else {
echo ("Login was not successful");
}
*/

?>

