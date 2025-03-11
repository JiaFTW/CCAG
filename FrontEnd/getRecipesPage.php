<?php
if (!isset($_COOKIE['session_token'])) {
    header("Location: loginPage.php");
    die();
}

require_once('favoriteCheck.php');
?>
<html>
<head>
    <title>CCAG Recipe Page</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include('header.php'); ?>

    <div id="resultsContainer"></div> 

    <script>
        $(document).ready(function () {
            let favoriteRids = <?php echo json_encode($favoriteRids); ?>;

            // Automatically fetch recipes when the page loads
            fetchRecipes();

            function fetchRecipes() {
                $.post("search.php", {}, function (response) {
                    let recipes = JSON.parse(response);
                    displayResults(recipes);
                });
            }

            function displayResults(recipes) {
                let resultsContainer = $("#resultsContainer");
                resultsContainer.empty();

                if (recipes.length === 0) {
                    resultsContainer.append("<p>No recipes found.</p>");
                    return;
                }

                recipes.forEach(recipe => {
                    let isFavorited = favoriteRids.includes(recipe.rid);
                    let buttonAction = isFavorited ? "removeFavorite.php" : "favorite.php";
                    let buttonText = isFavorited ? "💔 Remove Favorite" : "❤️ Favorite";

                    let recipeCard = `
                        <div class="recipeCard">
                            <h3>${recipe.name}</h3>
                            <img src="${recipe.image}" alt="${recipe.name}" class="recipeImg">
                            <p><strong>Calories: </strong> ${recipe.calories} kcal</p>
                            <p><strong>Servings: </strong> ${recipe.servings}</p>
                            <p><strong># of Ingredients: </strong> ${recipe.num_ingredients}</p>
                            <p><strong>Ingredients: </strong> ${recipe.ingredients}</p>
                            <p><strong>Labels: </strong> ${recipe.labels_str}</p>
                            <p><strong>RID: </strong> ${recipe.rid}</p>
                            <button class="favoriteButton" data-rid="${recipe.rid}" data-action="${buttonAction}">${buttonText}</button>
                        </div>
                    `;
                    resultsContainer.append(recipeCard);
                });

                $(".favoriteButton").click(function() {
                    let button = $(this);
                    let rid = button.data("rid");
                    let action = button.data("action");

                    $.post(action, { recipe_id: rid }, function(response) {
                        if (action === "favorite.php") {
                            button.text("💔 Remove Favorite");
                            button.data("action", "removeFavorite.php");
                            favoriteRids.push(rid);
                        } else {
                            button.text("❤️ Favorite");
                            button.data("action", "favorite.php");
                            favoriteRids = favoriteRids.filter(id => id !== rid);
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>

