<?php
//require_once('sessionValidate.php');
if (!isset($_COOKIE['session_token'])) {
    header("Location: loginPage.php");
    die();
}

require_once('favoriteCheck.php');
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
            let favoriteRids = <?php echo json_encode($favoriteRids); ?>;

            $("#search-form").submit(function (e) {
                e.preventDefault(); 

                let keyword = $("#search").val();
                
                $.post("search.php", { search: keyword }, function (response) {
                    let recipes = JSON.parse(response);
                    displayResults(recipes);
                });

                $("#search").val("");
            });

            function displayResults(recipes) {
                let resultsContainer = $("#results-container");
                resultsContainer.empty();

                if (recipes.length === 0) {
                    resultsContainer.append("<p>No recipes found.</p>");
                    return;
                }

                recipes.forEach(recipe => {
                    let isFavorited = favoriteRids.includes(recipe.rid);
                    let buttonAction = isFavorited ? "removefavorite.php" : "favorite.php";
                    let buttonText = isFavorited ? "&#x1F5A4; Remove Favorite" : "&#x2764;&#xfe0f; Favorite";

                    let recipeCard = `
                        <div class="recipe-card">
                            <h3>${recipe.name}</h3>
                            <img src="${recipe.image}" alt="${recipe.name}" class="recipe-img">
                            <p><strong>Calories: </strong> ${recipe.calories} kcal</p>
                            <p><strong>Servings: </strong> ${recipe.servings}</p>
                            <p><strong># of Ingredients: </strong> ${recipe.num_ingredients}</p>
                            <p><strong>Ingredients: </strong> ${recipe.ingredients}</p>
                            <p><strong>Labels: </strong> ${recipe.labels_str}</p>
                            <p><strong>RID: </strong> ${recipe.rid}</p>
                            <button class="favorite-button" data-rid="${recipe.rid}" data-action="${buttonAction}">${buttonText}</button>
                        </div>
                    `;
                    resultsContainer.append(recipeCard);
                });

                $(".favorite-button").click(function() {
                    let button = $(this)
                    let rid = button.data("rid");
                    let action = button.data("action");




                    $.post(action, { recipe_id: rid }, function(response) {
                        if (action === "favorite.php") {
                            button.text("Remove Favorite");
                            button.data("action", "removefavorite.php");
                            favoriteRids.push(rid);
                        } else {
                            button.text("Favorite");
                            button.data("action","favorite.php");
                            favoriteRids = favoriteRids.filter(id => id !== rid);
                        }
                    });
                });

            }
        });
    </script>
</body>
</html>