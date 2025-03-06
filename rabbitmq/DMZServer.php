#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('../Database/mysqlconnect.php');

function storeRecipe($recipeName, $yield, $imageUrl, $ingredients, $healthLabels)
{
	// TODO: Insert the recipe into the database

	//since we are still working on the database i wrote this to  simulate database insertion, for debugging purpose, you can remove it later.

	echo "Storing recipe: " . $recipeName . "\n";
	echo "Yield: " . $yield . " servings\n";
	echo "Image URL: " . $imageUrl . "\n";
	echo "Ingredients:\n";

	foreach ($ingredients as $ingredient) 
	{
		echo "- " . $ingredient . "\n";
	}

	echo "Health Labels:\n";
	foreach ($healthLabels as $label) 
	{
		echo "- " . $label . "\n";
	}

	return [
		'status' => 'success',
		'message' => 'Recipe stored successfully',
	];
}


function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);

  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "storeRecipe":
	    return storeRecipe(
		    $request['recipe_name'],
		    $request['yield'],
		    $request['image_url'],
		    $request['ingredients'],
		    $request['health_labels'],
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


