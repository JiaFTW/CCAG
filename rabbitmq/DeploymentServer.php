#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

require_once('../Deployment/InFileProcessor.php');
require_once('../Database/mysqlconnect.php');

$workingDir = "home/deploy/Bundles";

function doIncoming($file_name, $tempID) 
{
	$processor = new bundleProcessor ($workingDir);
	echo  $processor.getBundlePath($file_name).PHP_EOL;

	if ($processor.changeBundleName($file_name, "changedBundle.zip")) {
		echo "Changed Named SuccessFul".PHP_EOL;
	}
	else {
		echo "Changed Name Failed".PHP_EOL;
	}
}

function requestProcessor($request)
{
	echo "received request".PHP_EOL;
  	var_dump($request);

  	if (!isset($request['type'])) 
       	{
	       	return [
		       	'status' => 'error',
		  	'message' => 'Unsupported message type',
		];
	}

	switch ($request['type']) 
	{
	case 'incomingBundle':
		return doIncoming($request['file_name'], $request['tempID']);

	default:
		return [
			'status' => 'error',
			'message' => 'Unsupported request type',
		];
	}
}


$server = new rabbitMQServer("testRabbitMQ.ini","DMZSever");

echo "Deployment Server BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "Deployment Server END".PHP_EOL;
exit();
?>