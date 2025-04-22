#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

require_once('../Deployment/bundleprocessor.php');
require_once('../Database/mysqlconnect.php');

$workingDir = "home/deploy/Bundles"; //TODO make it configable

function doIncoming($tempID) 
{
	$processor = new bundleProcessor ($workingDir);
	$connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

	$bundles = $processor->getBundleArrayByID($tempID);
	var_dump($bundles);

	foreach ($bundles as $b) {

		$path = $processor->getBundlePathByNameStr($bundles[$b]);
		$machine = substr($bundles[$b], 0, strpos($bundles[$b], '_'));
		$name = null;
		$version = 0;
		
		$connect->recordIncomingBundle($name, $version, $machine, $path);
	}

	return ['status' => 'WIP'];
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
		return doIncoming($request['tempID']);

	default:
		return [
			'status' => 'error',
			'message' => 'Unsupported request type',
		];
	}
}


$server = new rabbitMQServer("testRabbitMQ.ini","DMZServer");

echo "Deployment Server BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "Deployment Server END".PHP_EOL;
exit();
?>