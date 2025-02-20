#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("conf-RabbitMQ.ini", "testServer");

// Registration Request
$registrationRequest = [
    'type' => 'user.register', // Updated routing key
    'username' => 'testuser',
    'email' => 'test@ccag.com',
    'password' => password_hash('testpassword', PASSWORD_BCRYPT), // Hash the password
];

// Login Request
$loginRequest = [
    'type' => 'user.login', // Updated routing key
    'username' => 'testuser',
    'password' => 'hashed_password',
];

// Send registration request
$response = $client->send_request($registrationRequest);
echo "Registration Response: " . print_r($response, true) . PHP_EOL;

// Send login request
$response = $client->send_request($loginRequest);
echo "Login Response: " . print_r($response, true) . PHP_EOL;

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

?>
