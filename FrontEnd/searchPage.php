<?php
require_once('sessionValidate.php');
?>
<html>
<head>
    <title>CCAG Search Page</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!--Using AJAX PLEASEEEE WORK :CCCCCC -->
</head>
<body>
    <?php include('header.php'); ?>

    <div class="search-container">
        <form id="search-form">
            <input type="text" id="search" name="search" class="search-box" placeholder="Search recipes..." required>
            <button type="submit" class="search-button">Search</button>
        </form>
    </div>

    <div id="results-container"></div> 

    <script>
        $(document).ready(function () {
            $("#search-form").submit(function (e) {
                e.preventDefault(); 

                let keyword = $("#search").val();
                
                $.post("search.php", { search: keyword }, function (response) {
                    let recipes = JSON.parse(response);
                    checkFavorites(recipes);
                });

                $("#search").val("");
            });

            function checkFavorites(recipes) {
                $.post("favorite.php", { action: "check" }, function (favoritedRecipes) {
                    let favorited = JSON.parse(favoritedRecipes);
                    displayResults(recipes, favorited);
                });
            }

            function displayResults(recipes, favorited) {
                let resultsContainer = $("#results-container");
                resultsContainer.empty();

                if (recipes.length === 0) {
                    resultsContainer.append("<p>No recipes found.</p>");
                    return;
                }

                recipes.forEach(recipe => {
                    let isFavorited = favorited.includes(recipe.name);
                    let buttonText = isFavorited ? "💔 Unfavorite" : "❤️ Favorite";
                    let buttonClass = isFavorited ? "unfavorite-btn" : "favorite-btn";

                    let recipeCard = `
                        <div class="recipe-card">
                            <h3>${recipe.name}</h3>
                            <img src="${recipe.image}" alt="${recipe.name}" class="recipe-img">
                            <p><strong>Calories: </strong> ${recipe.calories} kcal</p>
                            <p><strong>Servings: </strong> ${recipe.servings}</p>
                            <p><strong># of Ingredients: </strong> ${recipe.num_ingredients}</p>
                            <p><strong>Ingredients: </strong> ${recipe.ingredients}</p>
                            <p><strong>Labels: </strong> ${recipe.labels_str}</p>
                            <button class="${buttonClass}" data-recipe='${JSON.stringify(recipe)}'>${buttonText}</button>
                        </div>
                    `;
                    resultsContainer.append(recipeCard);
                });

                $(".favorite-btn, .unfavorite-btn").click(function () {
                    let recipeData = $(this).data("recipe");
                    let isUnfavoriting = $(this).hasClass("unfavorite-btn");
                    let action = isUnfavoriting ? "unfavorite" : "favorite";

                    $.post("favorite.php", { action: action, recipe: JSON.stringify(recipeData) }, function (response) {
                        alert(response); 
                        checkFavorites(recipes); 
                    });
                });
            }
        });
    </script>
</body>
</html>
