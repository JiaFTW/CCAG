#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
//require_once('../Database/mysqlconnect.php');
require_once('../DMZ/edamamProcessor.php');
require_once('pulseCheck.php');


function searchRecipe($query)
{
	// getting data from Edamam API
	$recipes = fetchEdamamData($query);

	// checks if recipes were fetched successfully
	if (!empty($recipes))
	{
		return [
			'status' => 'success',
			'message' => 'Recipes fetched successfully',
            		'recipes' => $recipes, // returns the filtered recipes
	       	];
	}
	else
       	{
	       	return [
            	'status' => 'error',
            	'message' => 'Failed to fetch recipes from Edamam API',
        	];
    	}
}

function requestProcessor($request)
{
	echo "received request".PHP_EOL;
  	var_dump($request);
	if (detectCluster() === "Production_Backup") {
		if(pulseCheck($ccag_machines['Production_Main']['DMZ'])) {
			echo "Main DMZ Server Online, ignoring request".PHP_EOL;
			return;
		}
	}
	

  	if (!isset($request['type'])) 
       	{
	       	return [
		       	'status' => 'error',
		  	'message' => 'Unsupported message type',
		];
	}

	switch ($request['type']) 
	{
	case 'searchRecipe':

		// handle fetching recipes from Edamam API
		if (!isset($request['query'])) 
		{
                	return [
			       	'status' => 'error',
			       	'message' => 'Query parameter is missing',
			];
		}
		return searchRecipe($request['query']);

	default:
		return [
			'status' => 'error',
			'message' => 'Unsupported request type',
		];
	}
}

$rabbit_channel = getRabbitMQChannel('dmz');
$cluster = detectCluster();

echo "You are in ".$cluster." running in Rabbit Channel: ".$rabbit_channel.PHP_EOL;

$server = new rabbitMQServer("testRabbitMQ.ini",$rabbit_channel);

echo "DMZServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "DMZServer END".PHP_EOL;
exit();
?>



