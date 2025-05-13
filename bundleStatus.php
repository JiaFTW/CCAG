#! /usr/bin/php
<?php
    require_once('rabbitmq/path.inc');
    require_once('rabbitmq/get_host_info.inc');
    require_once('rabbitmq/rabbitMQLib.inc');
    function status($database, $dmz, $frontend, $rabbit)
    {
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

        echo "What status would you like to set " . $message['bundle_name'] . " to?\n
        1 - Approved\n
        2 - Disapproved\n";

        $ready = false;
        $entry = readline("Please enter a code: ");

        while(!$ready)
        {
            if(!is_numeric($entry))
            {
                echo "Only numbers 1 and 2 are valid codes" . PHP_EOL;
                $entry = readline("Please enter a code: ");
                continue;
            }

            if($entry < 1 || $entry > 2)
            {
                echo "Only numbers 1 and 2 are valid codes" . PHP_EOL;
                $entry = readline("Please enter a code: ");
                continue;
            }
            $ready = true;
        }

        if($entry == 1)
        {
            $message['bundle_status'] = "Approved";
        }
        else
        {
            $message['bundle_status'] = "Disapproved";
        }
        $client = new rabbitMQClient("testRabbitMQ.ini","DeploymentServer");
        $response = $client->send_request($message);

        if($response)
            echo "Status updated!" . PHP_EOL;
        else
            echo "Update failed...\n"; 
    }

    function rollback($ip)
    {
        $request = array();
        $request['type'] = "rollback";
        $request['ip'] = $ipaddress;

        echo "Which bundle do you wish to rollback?\n
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

        switch ($entry)
        {
            case 1:
                $request['bundle_type'] = "FrontEnd";
                break;
            case 2:
                $request['bundle_type'] = "Database";
                break;
            case 3:
                $request['bundle_type'] = "rabbitmq";
                break;
            case 4:
                $request['bundle_type'] = "DMZ";
                break;
        }
        $client = new rabbitMQClient("testRabbitMQ.ini","DeploymentServer");
        $client->send_request($request);

        if($response)
            echo "Rollback Successful!" . PHP_EOL;
        else
            echo "Rollback failed...\n";
    }

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
        //var_dump($bundle);
        echo $bundle['name'] . " - " . $bundle['status'] . PHP_EOL;   
    }
//data dmz frontend rabbit

    $database = $response[0]['name'];
    $dmz = $response[1]['name'];
    $frontend = $response[2]['name'];
    $rabbit = $response[3]['name'];
    echo PHP_EOL;

    echo "Would you like to set the status of a bundle, or would you like to do a rollback?\n
    1 - Status\n
    2 - Rollback\n";

    $ready = false;
    $entry = readline("Please enter a code: ");
    while(!$ready)
    {
        if(!is_numeric($entry))
        {
            echo "Only numbers 1 and 2 are valid codes" . PHP_EOL;
            $entry = readline("Please enter a code: ");
            continue;
        }

        if($entry < 1 || $entry > 2)
        {
            echo "Only numbers 1 and 2 are valid codes" . PHP_EOL;
            $entry = readline("Please enter a code: ");
            continue;
        }

        $ready = true;
    }

    if($entry == 1)
    {
        status($database, $dmz, $frontend, $rabbit);
    }
    else
    {
        rollback($ipaddress);
    }
?>