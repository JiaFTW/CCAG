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
                    displayResults(recipes);
                });
            });

            function displayResults(recipes) {
                let resultsContainer = $("#results-container");
                resultsContainer.empty();

                if (recipes.length === 0) {
                    resultsContainer.append("<p>No recipes found.</p>");
                    return;
                }

                recipes.forEach(recipe => {
                    let recipeCard = `
                        <div class="recipe-card">
                            <h3>${recipe.name}</h3>
                            <img src="${recipe.image}" alt="${recipe.name}" class="recipe-img">
                            <p><a href="${recipe.url}" target="_blank">View Recipe</a></p>
                        </div>
                    `;
                    resultsContainer.append(recipeCard);
                });
            }
        });
    </script>
</body>
</html>
