#!/usr/bin/php

<?php
function extract_bundle($path)
{
    $zipFile = $path; //The file we will get is the message we will get from Sinchi's deployment server
    $extractTo = "~/CCAG/"; //This extracts to current directory.
    
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
        exec("scp " . " deploy@192.168.193.69:" . $path . " " . $path, $output);

        rename($path, substr($path, 0, strpos($path, "_", 0)) . ".zip");
        extract_bundle($path);
        exec("rm " . $path, $killme);
    }
    echo "Bundles Updated and extracted" . PHP_EOL;
}
?>