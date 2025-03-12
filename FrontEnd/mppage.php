<?php
require_once('../rabbitmq/testRabbitMQClient.php');

// get favorited recipes for the logged-in user, basically tried following your formet
$favoriteRequest = array(
	'type' => 'getFavorites', 
	'username' => $_COOKIE['username'], 
);
$response = sendMessage($favoriteRequest); 
$favoriteRecipes = $response;



// gets the current  saved meal plans for the logged-in user
$mealPlanRequest = array(
	'type' => 'getMealPlans', 
	'username' => $_COOKIE['username'], 
);

$mealPlans = sendMessage($mealPlanRequest); 


?>


<html>
    <head>
    <title>CCAG Meal Plan</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <script>
        // function to show/hide the dropdown menu
        function toggleDropdown(day, mealTime) {
            const dropdown = document.getElementById(`dropdown-${day}-${mealTime}`);
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }
        function saveMeal(day, mealTime) {
            // get the selected recipe from the dropdown
            const select = document.getElementById(`select-${day}-${mealTime}`);
            const recipeId = select.value; 
            const recipeName = select.options[select.selectedIndex].text; 

            // sends a POST request to the backend to save the meal plan
            fetch('saveMealPlan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    username: '<?php echo $_COOKIE['username']; ?>', 
                    day: day, 
                    mealTime: mealTime, 
                    recipeId: recipeId, 
                }),
            })

            .then(response => response.json()) 
            .then(data => {
                if (data.status === 'success') {
                    // updates the saved meal display
                    const savedMeal = document.getElementById(`saved-${day}-${mealTime}`);
                    savedMeal.textContent = `Saved: ${recipeName}`; 
                    alert('Meal plan updated successfully!'); 
                } else {
                    alert('Failed to save meal plan.'); 
                }
            });
        }
    </script>

  </head>
    <body>
    <?php include('header.php'); ?>

    <h1>Meal Plan</h1>
    <div id="meal-plan">
        <?php
        // the days of the week and meal times
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $mealTimes = ['Breakfast', 'Lunch', 'Dinner'];

        // is going to loop through each day of the week
        foreach ($days as $day) {
            echo "<div class='day'>"; 
            echo "<h2>$day</h2>"; 

            // is going to loop through each meals breakfast, lunch, dinner
            foreach ($mealTimes as $mealTime) {
                echo "<div class='meal-time'>"; 
                echo "<button onclick='toggleDropdown(\"$day\", \"$mealTime\")'>$mealTime</button>"; // added a button to toggle the dropdown

                // created the dropdown menu for selecting a recipe
                echo "<div id='dropdown-$day-$mealTime' class='dropdown'>";
                echo "<select id='select-$day-$mealTime'>"; 

                // populate the dropdown with the user's favorited recipes
                if (is_array($favoriteRecipes)) {
                    foreach ($favoriteRecipes as $recipe) {
                        echo "<option value='{$recipe['rid']}'>{$recipe['name']}</option>"; // adding each recipe as an option
                    }
                }
                echo "</select>"; 
                echo "<button onclick='saveMeal(\"$day\", \"$mealTime\")'>Save</button>"; //save button
                echo "</div>"; 

                // when you save a meal it will display it
                $savedMeal = array_filter($mealPlans, function($plan) use ($day, $mealTime) {
                    return $plan['day'] === $day && $plan['meal_time'] === $mealTime;
                });
                if (!empty($savedMeal)) {
                    $savedMeal = reset($savedMeal); 
                    echo "<div id='saved-$day-$mealTime' class='saved-meal'>Saved: {$savedMeal['name']}</div>"; 
                } else {
                    echo "<div id='saved-$day-$mealTime' class='saved-meal'></div>"; 
                }
                echo "</div>"; 
            }
            echo "</div>"; 
        }
        ?>
    </div>
        
    </body>
</html>
