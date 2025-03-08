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
        "name" => "Creamy Garlic Pasta",
        "image" => "https://via.placeholder.com/250",
        "url" => "https://example.com/recipe1",
    ],
    [
        "name" => "Spaghetti Carbonara",
        "image" => "https://via.placeholder.com/250",
        "url" => "https://example.com/recipe2",
    ],
    [
        "name" => "Pesto Pasta",
        "image" => "https://via.placeholder.com/250",
        "url" => "https://example.com/recipe3",
    ]
];

echo json_encode($mockResponse); 

?>
