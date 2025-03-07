

<?php

require_once('../rabbitmq/path.inc');          
require_once('../rabbitmq/get_host_info.inc');
require_once('../rabbitmq/rabbitMQLib.inc');  
//require_once('../rabbitmq/DMZServer.php');

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

	$client = new rabbitMQClient("conf-RabbitMQ.ini", "DMZServer");
	//$client = new rabbitMQClient("conf-RabbitMQ.ini", "testServer");

	// multidimensional array to store all recipes
	$recipes = [];

	foreach ($edamamData['hits'] as $hit) 
	
	{
		if (isset($hit['recipe'])) 
		{
			$recipe = $hit['recipe'];

			$recipeData = [
				'name' => $recipe['label'],
				'image' => $recipe['image'],
				'num_ingredients' => count($recipe['ingredientLines']),
				'ingredients' => implode("', '", $recipe['ingredientLines']),
				'calories' => $recipe['calories'],
				'servings' => $recipe['yield'],
				'labels' => implode("', '", $recipe['healthLabels']),
			];

			// adding recipe to the multidimensional array
			$recipes[] = $recipeData;


			// print the recipe details to the terminal for bug fixes
			echo "name: " . $recipeData['name'] . "\n";
            		echo "servings: " . $recipeData['servings'] . " servings\n";
            		echo "image: " . $recipeData['image'] . "\n";
            		echo "calories: " . $recipeData['calories'] . "\n";
            		echo "num_ingredients: " . $recipeData['num_ingredients'] . "\n";
            		echo "ingredients: '" . $recipeData['ingredients'] . "'\n";
            		echo "labels: '" . $recipeData['labels'] . "'\n";
            		echo "\n"; // Add a blank line between recipes
		}
	}

	// prepppare tosend the message to RabbitM
	$message = [
		'type' => 'storeRecipe',
		'recipes' => $recipes, // send the multidimensional array
	];
	// send the message to RabbitMQ
	$response = $client->send_request($message);
	echo "Data sent to RabbitMQ. Response: " . print_r($response, true) . PHP_EOL;
}


else
{
       	echo "Failed to fetch data from Edamam API.\n";
}
   
?>
