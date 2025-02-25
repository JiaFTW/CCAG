#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

/*function getDB() {
    $conn = new mysqli('127.0.0.1','ccagUser','12345','ccagDB');
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}*/

function doLogin($username,$password)
{
    /*$conn = getDB();
    $stmt = $conn->prepare("SELECT password FROM accounts WHERE username = $username");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hashedPassword);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashedPassword)) {
            return ['success' => true, 'message' => 'Login successful'];
        }
    }
    return ['success' => false, 'message' => 'Invalid username or password'];*/
    return true;
}

function doRegistration($username,$password,$email)
{
     /*$conn = getDB();

    // Check if the username or email already exists
    $stmt = $conn->prepare("SELECT * FROM accounts WHERE username = $username OR email = $email");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return ['success' => false, 'message' => 'Username or email already taken'];
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO accounts (username, email, password) VALUES ($username, $password, $email)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    $stmt->execute();

    return ['success' => true, 'message' => 'User registered successfully'];*/
    return true;
}
function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
    case "register":
      return doRegistration($request['username'],$request['password'],$request['email']);
    default:
      return "type fail".PHP_EOL;
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

