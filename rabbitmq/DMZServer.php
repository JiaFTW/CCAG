#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('../Database/mysqlconnect.php');
require_once('../DMZ/edamamProcessor.php');


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


$server = new rabbitMQServer("testRabbitMQ.ini","DMZServer");

echo "DMZServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "DMZServer END".PHP_EOL;
exit();
?>



