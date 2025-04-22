<?php 
require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');

$recipedata = array (
    'type' => 'getRecipe',
    'username' => $_COOKIE['username'],
    'keyword' => filter_input(INPUT_POST,'search'),
);

//Sends the login request
$response = sendMessage($recipedata);
writelog("searched for: " . $recipedata['keyword'], $_COOKIE['username']);
echo json_encode($response); 

/*//Test Code (To Do: Replace with real API call)
$mockResponse = [
    [
        "name" => "Bob's Crabcakes",
        "image" => "https://bobcrabcake.com/",
        "num_ingredients" => "2",
        "ingredients" => "Crab and Cake",
        "calories" => "500000000",
        "servings" => "Enough to feed the world.",
        "labels_str" => "Aura",
    ],
    [
        "name" => "Bob's Chicken Ceasar Salad",
        "image" => "https://bobcrabcake.com/",
        "num_ingredients" => "9",
        "ingredients" => "grilled chicken, romaine lettuce, parmesan, croutons, olive oil, lemon juice, garlic, anchovy paste, pepper",
        "calories" => "380",
        "servings" => "2",
        "labels_str" => "dairy-free, gluten-free, high-protein",
    ],
    [
        "name" => "Bob's Chicken Ceasar Salad",
        "image" => "https://bobcrabcake.com/",
        "num_ingredients" => "9",
        "ingredients" => "grilled chicken, romaine lettuce, parmesan, croutons, olive oil, lemon juice, garlic, anchovy paste, pepper",
        "calories" => "380",
        "servings" => "2",
        "labels_str" => "dairy-free, gluten-free, high-protein",
    ]
];*/

?>
