#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

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

$request = array();
$request['type'] = $info['type'];
$request['username'] = $info['username'];
$request['password'] = $info['password'];
$request['email'] = $info['email'];
$request['message'] = $info['message'];
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;
}

?>
