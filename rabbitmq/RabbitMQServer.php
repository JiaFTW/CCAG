<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('../Database/mysqlconnect.php'); // included the database connection class

// function to register a new user
function doRegister($username, $email, $password)
{
	//Hash the password before storing it
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);	
    
    //connect to the database
    $db = new mysqlConnect('127.0.0.1', 'ccagUser', '12345', 'ccagDB');

    // Register the user
    $result = $db->registerAccount($username, $email, $hashedPassword);

    // Return success or failure
    return $result;//contains status and error type 
    //return ['status' => 'success', 'message' => 'User registered successfully'];
}

// function to log in a user
function doLogin($username, $password)
{
    //connect to the database
    $db = new mysqlConnect('127.0.0.1', 'ccagUser', '12345', 'ccagDB');

    //log in the user
    $result = $db->loginAccount($username, $password);

    //return the result
    return $result; // Contains status and session key

    //TODO: Validate username and password against the database
    // TODO: Generate a session key and store it in the database
    // Return session key or failure
    //return ['status' => 'success', 'session_key' => 'generated_session_key'];
}

//function to process the request
function requestProcessor($request)
{
    echo "Received request" . PHP_EOL;
    var_dump($request);

    if (!isset($request['type'])) {
        return ['status' => 'error', 'message' => 'Unsupported message type'];
    }

    switch ($request['type']) {
        case 'register':
            return doRegister($request['username'], $request['email'], $request['password']);
        case 'login':
            return doLogin($request['username'], $request['password']);
        default:
            return ['status' => 'error', 'message' => 'Unsupported request type'];
    }
}

//start the RabbitMQ server
$server = new rabbitMQServer("conf-RabbitMQ.ini", "testServer");

echo "testRabbitMQServer BEGIN" . PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END" . PHP_EOL;
exit();

/*#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($username,$password)
{
    // lookup username in databas
    // check password
    return true;
    //return false if not valid
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
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

 */
?>
