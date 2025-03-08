<?php 
require_once('../rabbitmq/testRabbitMQClient.php');

/*$recipedata = array (
    'type' => 'getRecipe',
    'username' => $_COOKIE['username'],
    'keyword' => filter_input(INPUT_POST,'search'),
);

//Sends the login request
$response = sendMessage($recipedata);*/

//Test Code (To Do: Replace with real API call)
$mockResponse = [
    [
        "name" => "Bob's Crabcakes",
        "image" => "https://crabcake.com/",
        "url" => "https://crabcake.com/recipe",
    ],
    [
        "name" => "Bob's Pizza",
        "image" => "https://bobcrabcake.com/",
        "url" => "https://bobcrabcake.com/recipe2",
    ],
    [
        "name" => "Bob's Pasta",
        "image" => "https://bobcrabcake.com/",
        "url" => "https://bobcrabcake.com/recipe3",
    ]
];

echo json_encode($mockResponse); 

?>
