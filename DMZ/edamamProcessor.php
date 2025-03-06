<?php

require_once('../rabbitmq/path.inc');          
require_once('../rabbitmq/get_host_info.inc');
require_once('../rabbitmq/rabbitMQLib.inc');  

// Edamam API credentials
$app_id = "e87d2844";               
$app_key = "5a5a8669d8e868a26407128df3f1f1d7"; 

// function to fetch data from Edamam API
function fetchEdamamData($query) 
{
	global $app_id, $app_key;
       	
	//create the API URL with the query and credentials
	$url = "https://api.edamam.com/search?q=" . urlencode($query) . "&app_id=" . $app_id . "&app_key=" . $app_key;
	
	// initialize cURL to make an HTTP request to the Edamam API
	 $ch = curl_init(); // initialize cURL session
	 curl_setopt($ch, CURLOPT_URL, $url); // set the URL to fetch
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the response as a string instead of outputting it
	 $response = curl_exec($ch); // execute the cURL request and store the response
	 curl_close($ch); 
	 return json_decode($response, true);
}

// checking if a query is provided as a command-line argument
if ($argc < 2)
{
       	echo "Usage: php edamamProcessor.php <query>\n";
       	exit(1);
}

// Get the query from the command-line argument
$query = $argv[1]; 


// fetch data from Edamam API using the query
$edamamData = fetchEdamamData($query);

// check if data was successfully fetched
if ($edamamData && isset($edamamData['hits'])) 
{
	echo "Recipes for '$query':\n";
	echo "=====================\n";

	/*
	$client = new rabbitMQClient("conf-RabbitMQ.ini", "testServer");
	 */

	// loop through each recipe 
	foreach ($edamamData['hits'] as $hit) 
	{
	       	if (isset($hit['recipe'])) 
		{
			$recipe = $hit['recipe'];
			                   
			// get the required fields

			$recipeData =[
				'recipe_name' => $recipe['label'],
				'yield' => $recipe['yield'],
				'image_url' => $recipe['image'],
				'ingredients' => $recipe['ingredientLines'],
				'health_labels' => $recipe['healthLabels'],
			];


			echo "Recipe: " . $recipeData['recipe_name'] . "\n";
			echo "Yield: " . $recipeData['yield'] . " servings\n";
			echo "Image URL: " . $recipeData['image_url'] . "\n";
			echo "Ingredients:\n";
			foreach ($recipeData['ingredients'] as $ingredient)
			{
			       	echo "- " . $ingredient . "\n";
		       	}
			echo "Health Labels:\n";
			
			foreach ($recipeData['health_labels'] as $label) 
			{
				echo "- " . $label . "\n";
	       		}
			echo "\n"; 

			/*
			 $message = [
				 'type' => 'storeRecipe',
				 'recipe_name' => $recipeData['recipe_name'],
				 'yield' => $recipeData['yield'],
				 'image_url' => $recipeData['image_url'],
				 'ingredients' => $recipeData['ingredients'],
				 'health_labels' => $recipeData['health_labels'],
			 ];

			 $response = $client->send_request($message);
			 echo "Data sent to RabbitMQ. Response: " . print_r($response, true) . PHP_EOL;
			 */
	       	}
       	}
}
else
{
       	echo "Failed to fetch data from Edamam API.\n";
}
   
?>
