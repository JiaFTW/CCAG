#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('../Database/mysqlconnect.php');

function doLogin($username,$password)
{
  
    $connect = new mysqlConnect('192.168.193.78','ccagUser','12345','ccagDB');

    return $connect->loginAccount($username, $password);
    
}

function doRegistration($username,$password,$email)
{
  $connect = new mysqlConnect('192.168.193.78','ccagUser','12345','ccagDB');

  return $connect->registerAccount($username, $email, $password);
}
function doValidate($token){

  $connect = new mysqlConnect('192.168.193.78','ccagUser','12345','ccagDB');

  return $connect->validateSession($token);
  
}

function doLogout($token) {
  $connect = new mysqlConnect('192.168.193.78','ccagUser','12345','ccagDB');

  return $connect->invalidateSession($token);
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
    case "logout":
      return doLogout($request['sessionId']);
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


