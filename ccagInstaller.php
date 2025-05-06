#!/usr/bin/php

<?php
 require_once('rabbitmq/path.inc');
 require_once('rabbitmq/get_host_info.inc');
 require_once('rabbitmq/rabbitMQLib.inc');
function extract_bundle($path)
{
    $zipFile = $path; //The file we will get is the message we will get from Sinchi's deployment server
    $fuckYou = "/home/".get_current_user()."/";
    $length = strlen($fuckYou);
    $tail = substr($path,$length);
    echo $tail;
    $extractTo = __DIR__ . "/" . $tail; //This extracts to current directory.
    echo $extractTo;
    $zip = new ZipArchive;
    
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo($extractTo);
        $zip->close();
        echo "IT WORKED!";
    }
    else {
        echo "It no work...";
    }
}
$client = new rabbitMQClient("testRabbitMQ.ini","DeploymentServer");
$request = array();
$request['type'] = "getUpdate";

$ipline = exec("hostname -I");
$ipaddress = substr($ipline, strpos($ipline, " ",0) + 1);
$ipaddress = substr($ipaddress, 0, strpos($ipaddress, " ",0));

$request['ip'] = $ipaddress;
$response = $client->send_request($request);

if(count($response) > 0)
{
    $path;
    for($i = 0; $i < count($response); $i++)
    {
        $path = $response[$i];
        echo $path. PHP_EOL;
        $location = substr($path,21);
        $location = substr($location, strpos($location, "/") + 1);
        $location = "/home/".get_current_user()."/".$location;
        echo $location . PHP_EOL;
        exec("scp " . " deploy@192.168.193.69:" . $path . " " . $location, $output);
        
        //exec("rm -rf " . substr($location, 0, strpos($location, "_", 0)), $killme);
        rename($location, substr($location, 0, strpos($location, "_", 0)));
        $location = substr($location, 0, strpos($location, "_", 0));
        extract_bundle($location);
        //exec("rm " . $location, $killme);
    }
    echo "Bundles Updated and extracted" . PHP_EOL;
}
?>