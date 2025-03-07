#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('../Database/mysqlconnect.php');

function doLogin($username,$password)
{
  
    $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

    return $connect->loginAccount($username, $password);
    
}

function doRegistration($username,$password,$email)
{
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->registerAccount($username, $email, $password);
}
function doValidate($token){

  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->validateSession($token);
  
}

function doLogout($token) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->invalidateSession($token);
}

function doRecipe($keyword, /*$labels = '' */) //perform search check
{
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');
 
  $response = $connect->checkRecipe($keyword); 
  if($response == false) { //return if mysql error
    return array('status' => 'DB_Error');
  }
  if ($response === null){ //will fetch from DMZ if no results from database
    
    echo "this is  null";
    /* $client = new rabbitMQClient("testRabbitMQ.ini","DMZServer");
    $request = array();
    $request['type'] = "getRecipe";
    $request['keyword'] = $keyword; //placeholder stuff until we define the system more
    $dmz_response = $client->send_request($request);

    if($connect->populateRecipe($dmz_response) === false) {  //populate db with response
      return array('status' => 'DB_Error');
    }
    $response = $connect->checkRecipe($keyword, $lables); //perform search check agian */
  }
  
 
  return $response;
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
    case "getRecipe":
      return doRecipe($request['keyword']);
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


