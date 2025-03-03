#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('../Database/mysqlconnect.php');

function getRecipe($info)
{
  //DMZ stuff here
  echo "getting here".PHP_EOL;
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
    case "getRecipe":
      return getRecipe($request);
    default:
      return "type fail".PHP_EOL;
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","DMZServer");

echo "DMZServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "DMZServer END".PHP_EOL;
exit();
?>


