<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doRegister($username, $email, $password)
{
    // TODO: Hash the password before storing it
    // TODO: Insert user into the database
    // Return success or failure
    return ['status' => 'success', 'message' => 'User registered successfully'];
}

function doLogin($username, $password)
{
    // TODO: Validate username and password against the database
    // TODO: Generate a session key and store it in the database
    // Return session key or failure
    return ['status' => 'success', 'session_key' => 'generated_session_key'];
}

function requestProcessor($request)
{
    echo "Received request" . PHP_EOL;
    var_dump($request);

    if (!isset($request['type'])) {
        return ['status' => 'error', 'message' => 'Unsupported message type'];
    }

    switch ($request['type']) {
        case 'user.register': // Updated routing key
            return doRegister($request['username'], $request['email'], $request['password']);
        case 'user.login': // Updated routing key
            return doLogin($request['username'], $request['password']);
        default:
            return ['status' => 'error', 'message' => 'Unsupported request type'];
    }
}

$server = new rabbitMQServer("conf-RabbitMQ.ini", "testServer");

echo "testRabbitMQServer BEGIN" . PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END" . PHP_EOL;
exit();
?>
