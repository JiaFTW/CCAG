
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');



if(isset($argv[1]))
{
  $type = $argv[1];
}
else
{
  $type = "login";
}

if (isset($argv[2]))
{
  $msg = $argv[2];
}
else
{
  $msg = "test message";
}

function sendMessage($info){
$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
$request = array();
$request['type'] = $info['type'];
switch ($request['type'])
  {
    case "login":
      echo 'logging in';
      $request['username'] = $info['username'];
      $request['password'] = $info['password'];
      break;
    case "validate_session":
      $request['sessionId'] = $info['sessionId'];
      break;
    case "logout":
      $request['sessionId'] = $info['sessionId'];
      break;
    case "register":
      $request['username'] = $info['username'];
      $request['password'] = $info['password'];
      $request['email'] = $info['email'];
      break;
    case "getRecipe":
      //put whatever data we need to search the database or dmz here
    default:
      return $request['type'].PHP_EOL;
  }
$request['message'] = $info['message'];
$response = $client->send_request($request);
//$response = $client->publish($request);
return $response;
echo "client received response: ".PHP_EOL;
//print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;
}

?>
