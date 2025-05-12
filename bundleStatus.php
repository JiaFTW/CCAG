#! /usr/bin/php
<?php
    require_once('rabbitmq/path.inc');
    require_once('rabbitmq/get_host_info.inc');
    require_once('rabbitmq/rabbitMQLib.inc');

    $ipline = exec("hostname -I");
    $ipaddress = substr($ipline, strpos($ipline, " ",0) + 1);
    $ipaddress = substr($ipaddress, 0, strpos($ipaddress, " ",0));

    $client = new rabbitMQClient("testRabbitMQ.ini","DeploymentServer");
    $request = array();
    $request['type'] = "getBundleList";
    $request['ip'] = $ipaddress;

    $response = $client->send_request($request);

    echo "Current Bundles:\n";
    foreach($response as $bundle)
    {
        echo $bundle . PHP_EOL;   
    }
//data dmz frontend rabbit

    $database = $response[0][0];
    $dmz = $response[1][0];
    $frontend = $response[2][0];
    $rabbit = $response[3][0];
    echo PHP_EOL;

    echo "Which bundle do you wish to set status with?\n
        1 - FrontEnd\n
        2 - Database\n
        3 - RabbitMQ\n
        4 - DMZ" . PHP_EOL;

    $ready = false;
    $entry = readline("Enter a code: ");

    while(!$ready)
    {
        if(!is_numeric($entry))
        {
            echo "Only numbers from 1-4 are valid codes" . PHP_EOL;
            $entry = readline("Please enter a code: ");
            continue;
        }

        if($entry < 1 || $entry > 4)
        {
            echo "Only numbers from 1-4 are valid codes" . PHP_EOL;
            $entry = readline("Please enter a code: ");
            continue;
        }

        $ready = true;
    }

    $message = array();
    $message['type'] = "changeBundleStatus";

    switch ($entry)
    {
        case 1:
            $message['bundle_name'] = $frontend;
            break;
        case 2:
            $message['bundle_name'] = $database;
            break;
        case 3:
            $message['bundle_name'] = $rabbit;
            break;
        case 4:
            $message['bundle_name'] = $dmz;
            break;

    }

    echo "What status would you like to set " . $message['bundle_name'] . " to?";
?>