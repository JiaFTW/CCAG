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
		$path = $workingDir."/".$machine."/".$name;

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
		$release_bool = $connect->recordRelease($name, $machine, $cluster);
		if (!$release_bool) {
			$processor->deleteBundlesByID($tempID);
			return ['msg' => 'Deploy Server Error (recordRelease). Aborting Deployment'];
		}
		$processor->changeBundleName($b, $name, $machine); //Moves file to appropiate subfolder and changes file name
		
	}

	return ['msg' => "Successfuly created Bundles Version ". $version_num + 1 ." for ".$cluster.". Updated Current Version for Deployment!"];
}

function doChangeBundleStatus($machine, $cluster, $bundle_status) //changes the status of current Bundle Deployed
{
	$connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDeploy');

	$current_name = $connect->getCurrentVersion($machine, $cluster);

	$status = $connect->changeBundleStatus($current_name, $bundle_status);

	return ['msg' => $status ? $name.' status changed to '.$bundle_status : 'Deploy Server Error'];
}

function doGetBundleList($ip)
{
	$connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDeploy');
	$info = $connect->getInfoFromAddress($address);
	$cluster = $info['cluster'];
	return $connect->getBundleList($cluster);
}

function doGetUpdate($address) {
	$connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDeploy');
	//$address_info = $connect->getInfoFromAddress($address);
	$update_paths = [];
	$names = $connect->getReleaseList($address);
	//var_dump($address_info);
	if ($names == null) {
		echo "No Updates Needed".PHP_EOL;
		return $update_paths;
	}
	foreach ($names as $n) {
		$update_paths[] = $connect->getBundlePath($n);
	}
	/*
	if ($address_info['type'] == "BackEnd") {
		$update_paths[] = $connect->getCurrentPath('Database', $address_info['cluster']);
		$update_paths[] = $connect->getCurrentPath('rabbitmq', $address_info['cluster']);
	}
	else {
		$update_paths[] = $connect->getCurrentPath($address_info['type'], $address_info['cluster']);
	}
	*/
	$connect->refreshIsDeployed($address);
	//var_dump($update_paths); 
	return $update_paths;
}

function doRollBack($address, $machine) {
	$connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDeploy');
	$info = $connect->getInfoFromAddress($address);
	$cluster = $info['cluster'];
	$boolean = $connect->rollbackPrevious($address, $machine, $cluster);
	
	return ['msg' => $boolean ? "RollBack Succesfully" : 'Deploy Server Error'];
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
		return doChangeBundleStatus($request['bundle_name'], $request['bundle_status']); //change to name and status only
	case 'getBundleList':
		return doGetBundleList($request['ip']); 
	case 'getUpdate':
		return doGetUpdate($request['ip']);
	case 'rollback':
		return doRollBack($request['ip'], $request['type']);
	default:
		return [
			'status' => 'error',
			'message' => 'Unsupported request type',
		];
	}
}


$server = new rabbitMQServer("testRabbitMQ.ini","DeploymentServer"); //DeploymentServer does not need to use getRabbitMQChannel()

echo "Deployment Server BEGIN".PHP_EOL;

//var_dump(doIncoming(111, 'QA'));
//var_dump(doGetUpdate('1.1.1.1'));

//var_dump(doRollBack('192.168.193.71', "FrontEnd"));

$server->process_requests('requestProcessor');
echo "Deployment Server END".PHP_EOL;
exit();
?>