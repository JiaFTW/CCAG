#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

require_once('../Deployment/bundleprocessor.php');
require_once('../Database/mysqlconnect.php');


function doIncoming($tempID, $cluster) 
{
	$workingDir = "/home/".get_current_user()."/Bundles";
	$processor = new bundleProcessor ($workingDir);
	$connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDeploy');

	$bundles = $processor->getBundleArrayByID($tempID);
	//var_dump($bundles);
	$version_num = $connect->generateVersionNumAll($cluster);
	
	foreach ($bundles as $b) {
		$machine = substr($b, 0, strpos($b, '_'));
		$version = $version_num + 1;
		$name = $machine."_".$cluster."_V".$version;
		$processor->changeBundleName($b, $name, $machine); //Moves file to appropiate subfolder and changes file name
		$path = $processor->getBundlePathByNameStr($name).'.zip';

		echo "Identified Machine: ".$machine." | ";
		echo "Identified Version Num: ".$version." | ";
		echo "Name Created: ".$name." | ";
		echo "Identified Path: ".$path.PHP_EOL;
			
		$connect->recordIncomingBundle($name, $version, $machine, $path, $cluster);
		
	}

	return ['msg' => "Successfuly created Bundles Version[". $version_num + 1 ."] for ".$cluster.". Updated Current Versions for Deployment"];
}

function doChangeBundleStatus($name, $bundle_status) 
{
	$connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDeploy');
	return $connect->changeBundleStatus($name, $bundle_status);
}

function doGetBundleList($machine, $cluster)
{
	$connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDeploy');
	return $connect->getBundleList($machine);
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
		return doIncoming($request['id'], $request['location']);
	case 'changeBundleStatus':
		return doChangeBundleStatus($request['name'], $request['bundle_status']);
	case 'getBundleList':
		return doGetBundleList($request['location']);
	default:
		return [
			'status' => 'error',
			'message' => 'Unsupported request type',
		];
	}
}


$server = new rabbitMQServer("testRabbitMQ.ini","DeploymentServer"); //DeploymentServer

echo "Deployment Server BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "Deployment Server END".PHP_EOL;
exit();
?>