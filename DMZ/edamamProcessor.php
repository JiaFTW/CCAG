<?php

require_once('../rabbitmq/path.inc');          
require_once('../rabbitmq/get_host_info.inc');
require_once('../rabbitmq/rabbitMQLib.inc');  

$app_id = "e87d2844";               
$app_key = "5a5a8669d8e868a26407128df3f1f1d7"; 

// list of all allowed labels
$allowedLabels = ['Dairy-Free', 'Egg-Free', 'Peanut-Free', 'Tree-Nut-Free', 'Wheat-Free','Soy-Free', 'Fish-Free', 'Shellfish-Free', 'Sesame-Free', 'Gluten-Free','Alcohol-Free', 'Kosher', 'Keto', 'Vegetarian', 'High-Fiber', 'High-Protein','Low-Carb', 'Low-Fat', 'Low-Sodium', 'Low-Sugar'];

// function used to fetch data from Edamam API
function fetchEdamamData($query) 
{
	global $app_id, $app_key, $allowedLabels;

	// initialize cURL to make an HTTP request to the Edamam API
	$url = "https://api.edamam.com/search?q=" . urlencode($query) . "&app_id=" . $app_id . "&app_key=" . $app_key . "&to=25";
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	$response = curl_exec($ch);
	curl_close($ch); 
	return json_decode($response, true);
}



// function used to filter and merge health and diet labels
function filterLabels($healthLabels, $dietLabels) 
{
	global $allowedLabels;
	$allLabels = array_merge($healthLabels, $dietLabels);
	return array_intersect($allLabels, $allowedLabels);
}


$query = implode(" ", array_slice($argv, 1)); 

// fetch data from Edamam API using the query
$edamamData = fetchEdamamData($query);

// check if data was successfully fetched
if ($edamamData && isset($edamamData['hits'])) 
{
	
	// multidimensional array to store all recipes
	$recipes = [];
	foreach ($edamamData['hits'] as $hit)
	{
		if (isset($hit['recipe']))
		{
			$recipe = $hit['recipe'];
			$labels = filterLabels($recipe['healthLabels'], $recipe['dietLabels']);
		       	$recipeData = [
				'name' => $recipe['label'],
                		'image' => $recipe['image'],
                		'num_ingredients' => count($recipe['ingredientLines']),
                		'ingredients' => implode("', '", $recipe['ingredientLines']),
                		'calories' => $recipe['calories'],
                		'servings' => $recipe['yield'],
                		'labels' => implode("', '", $labels),
			];
			// adding recipe to the multidimensional array
			$recipes[] = $recipeData;
/*			
			echo "name: " . $recipeData['name'] . "\n";
            		echo "servings: " . $recipeData['servings'] . " servings\n";
            		echo "image: " . $recipeData['image'] . "\n";
            		echo "calories: " . $recipeData['calories'] . "\n";
            		echo "num_ingredients: " . $recipeData['num_ingredients'] . "\n";
            		echo "ingredients: '" . $recipeData['ingredients'] . "'\n";
            		echo "labels: '" . $recipeData['labels'] . "'\n";
            		echo "\n"; 
*/ 
		}
	}
	
	return $recipes;
}
else
{
    echo "Failed to fetch data from Edamam API.\n";
    return [];
}
?>
