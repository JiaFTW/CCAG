#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("conf-RabbitMQ.ini","testServer");

// Registration Request
$registrationRequest = [
    'type' => 'register',
    'username' => 'testuser',
    'email' => 'test@ccag.com',
    'password' => 'hashed_password', // Ensure passwords are hashed
];

// Login Request
$loginRequest = [
    'type' => 'login',
    'username' => 'testuser',
    'password' => 'hashed_password',
];

// Send registration request
$response = $client->send_request($registrationRequest);
echo "Registration Response: " . print_r($response, true) . PHP_EOL;

// Send login request
$response = $client->send_request($loginRequest);
echo "Login Response: " . print_r($response, true) . PHP_EOL;

/*if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

$request = array();
$request['type'] = "Login";
$request['username'] = "steve";
$request['password'] = "password";
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

 */
?>


