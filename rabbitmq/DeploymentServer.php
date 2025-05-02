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
	$bundle_status = "Unchecked";

	$bundles = $processor->getBundleArrayByID($tempID);
	//var_dump($bundles);

	if ($cluster == 'Production') {
		foreach ($bundles as $b) { //Checks if Current QA Versions are suitable for Production Deployment
			$machine = substr($b, 0, strpos($b, '_'));
			$current_name = $connect->getCurrentVersion($machine, 'QA');
			if ($current_name == null) {
				$processor->deleteBundlesByID($tempID);
				return ['msg' => "No current QA bundle for " . $machine . " found."];
			}
			else {
				$current_status = $connect->getBundleStatus($current_name);
				if ($current_status != "Approved") {
					$processor->deleteBundlesByID($tempID);
					return['msg' => "Current QA bundle ".$current_name." must be 'Approved' before deploying to Production. Current Status: ".$current_status ];
				} 
			}
			
		}

		foreach ($bundles as $b) { //Changes statuses for all apporoved QA bundles to Published
			$machine = substr($b, 0, strpos($b, '_'));
			$current_name = $connect->getCurrentVersion($machine, 'QA');
			$connect->changeBundleStatus($current_name, 'Published');
		}
		$bundle_status = "Working"; //Setting Status of incoming Production Bundle

	}
	
	$version_num = $connect->generateVersionNumAll($cluster);
	foreach ($bundles as $b) {
		$machine = substr($b, 0, strpos($b, '_'));
		$version = $version_num + 1;
		$name = $machine."_".$cluster."_V".$version;
		$path = $workingDir."/".$machine."/".$name.".zip";

		echo "Identified Machine: ".$machine." | ";
		echo "Identified Version Num: ".$version." | ";
		echo "Name Created: ".$name." | ";
		echo "Identified Path: ".$path.PHP_EOL;

		$current_name = $connect->getCurrentVersion($machine, $cluster); 
		if ($current_name != null) { //Set isCurrentVersion of previous Bundle to false if one exists
			if (!$connect->changeCurrentVersion($current_name, false)) { 
				$processor->deleteBundlesByID($tempID);
				return ['msg' => 'Deploy Server Error (changeCurrentVersion). Aborting Deployment'];
			}
		}
		echo $bundle_status.PHP_EOL;
		$record_bool = $connect->recordIncomingBundle($name, $version, $bundle_status, $machine, $path, $cluster);
		if (!$record_bool) {
			$processor->deleteBundlesByID($tempID);
			return ['msg' => 'Deploy Server Error (recordIncomingBundle). Aborting Deployment'];
		}
		$processor->changeBundleName($b, $name, $machine); //Moves file to appropiate subfolder and changes file name
		
	}

	return ['msg' => "Successfuly created Bundles Version[". $version_num + 1 ."] for ".$cluster.". Updated Current Version for Deployment!"];
}

function doChangeBundleStatus($name, $bundle_status) 
{
	$connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDeploy');
	$status = $connect->changeBundleStatus($name, $bundle_status);

	return ['msg' => $status ? $name.' status changed to '.$bundle_status : 'Deploy Server Error'];
}

function doGetBundleList($machine, $cluster)
{
	$connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDeploy');
	return $connect->getBundleList($machine, $cluster);
}

function doGetUpdate($address) {
	$connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDeploy');
	$address_info = $connect->getInfoFromAddress($address);
	$update_paths = [];
	if ($address_info['type'] == "BackEnd") {
		$update_paths[] = $connect->getCurrentPath('Database', $address_info['cluster']);
		$update_paths[] = $connect->getCurrentPath('rabbitmq', $address_info['cluster']);
	}
	else {
		$update_paths[] = $connect->getCurrentPath($address_info['type'], $address_info['cluster']);
	}
	return $update_paths;
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
	case 'getUpdate':
		return doGetUpdate($request['ipaddress']);
	default:
		return [
			'status' => 'error',
			'message' => 'Unsupported request type',
		];
	}
}


$server = new rabbitMQServer("testRabbitMQ.ini","testServer"); //DeploymentServer

echo "Deployment Server BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "Deployment Server END".PHP_EOL;
exit();
?>