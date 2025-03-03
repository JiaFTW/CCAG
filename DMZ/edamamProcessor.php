<?php

require_once('../rabbitmq/path.inc');          
require_once('../rabbitmq/get_host_info.inc');
require_once('../rabbitmq/rabbitMQLib.inc');  

// Edamam API credentials
$app_id = "e87d28";               
$app_key = "5a5a8669d8e868a26407128df3f1f1d"; 

// function to fetch data from Edamam API
function fetchEdamamData($query) 
{
    global $app_id, $app_key; // 

    // create the API URL with the query and credentials
    $url = "https://api.edamam.com/search?q=" . urlencode($query) . "&app_id=" . $app_id . "&app_key=" . $app_key;

    // initialize cURL to make an HTTP request to the Edamam API
    $ch = curl_init(); // initialize cURL session
    curl_setopt($ch, CURLOPT_URL, $url); // set the URL to fetch
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the response as a string instead of outputting it
    $response = curl_exec($ch); // execute the cURL request and store the response
    curl_close($ch); // close the cURL session

    // decode the JSON response from Edamam into a PHP array
    return json_decode($response, true);
}

// example query (we can modify this later for now)
$query = "chicken"; // Ex: search for recipes with "chicken"

// fetch data from Edamam API using the query
$edamamData = fetchEdamamData($query);

// check if data was successfully fetched
if ($edamamData) {
    // initializing RabbitMQ client to send the fetched data to RabbitMQ
    $client = new rabbitMQClient("conf-RabbitMQ.ini", "testServer");

    // prepare the message to send to RabbitMQ
    $message = [
        'type' => 'edamam_data', // message type: can be used to identify the type of data
        'data' => $edamamData,   // The actual data fetched from Edamam
    ];

    // send the message to RabbitMQ using the RabbitMQ client
    $response = $client->send_request($message);

    
    echo "Data sent to RabbitMQ. Response: " . print_r($response, true) . PHP_EOL;
} else {
    
    echo "Failed to fetch data from Edamam API." . PHP_EOL;
}
?>
