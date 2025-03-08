#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('../Database/mysqlconnect.php');
require_once('../DMZ/edamamProcessor.php');

// function to store a recipe in the database
function storeRecipe($recipeData)
{
	// get the recipe data
	$name = $recipeData['name'];
	$image = $recipeData['image'];
	$numIngredients = $recipeData['num_ingredients'];
	$ingredients = $recipeData['ingredients'];
	$calories = $recipeData['calories'];
	$servings = $recipeData['servings'];
	$labels = $recipeData['labels'];
	
	// calling the addRecipe function to insert the recipe into the database
	$success = addRecipe($name, $image, $numIngredients, $ingredients, $calories, $servings, $labels);
	
	if ($success)
	{
		return [
			'status' => 'success',
			'message' => 'Recipe stored successfully',
		];
	}
	else
	{
		return [
			'status' => 'error',
			'message' => 'Failed to store the recipe',
		];
	}

}

function getRecipe($query)
{
	//fetching data
	$recipes = fetchEdamamData($query);
	if (!empty($recipes)) 
	{
		return [
			'status' => 'success',
            		'message' => 'Recipes fetched successfully',
            		'recipes' => $recipes,
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
	case 'storeRecipe':
		foreach ($request['recipes'] as $recipe)
		{
			$result = storeRecipe($recipe);
			if ($result['status'] === 'error') 
			{
				return $result;
			}
		}
		return[
			'status' => 'success',
			'message' => 'All recipes stored successfully',
		];
	case 'getRecipe':
		// Handle fetching recipes from Edamam API
		if (!isset($request['query'])) 
		{
                	return [
			       	'status' => 'error',
			       	'message' => 'Query parameter is missing',
			];
		}
	       	return getRecipe($request['query']);

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



