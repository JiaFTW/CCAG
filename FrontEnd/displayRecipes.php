<script>
    
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
                    let buttonText = isFavorited ? "💔 Remove Favorite" : "❤️ Favorite";

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
                            button.text("💔 Remove Favorite");
                            button.data("action", "removefavorite.php");
                            favoriteRids.push(rid);
                        } else {
                            button.text("❤️ Favorite");
                            button.data("action","favorite.php");
                            favoriteRids = favoriteRids.filter(id => id !== rid);
                        }
                    });
                });

            }
</script>