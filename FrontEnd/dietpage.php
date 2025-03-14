<?php 
require_once('../rabbitmq/testRabbitMQClient.php');

if (!isset($_COOKIE['session_token'])) {
    header("Location: loginPage.php");
    die();
  }

$getDiet = array (
    'type' => 'getDiet',
    'username' => $_COOKIE['username'],
);

$response = sendMessage($getDiet);

$savedRestrictions = is_array($response) ? $response : [];

function isChecked($restriction, $savedRestrictions) {
    return in_array($restriction, $savedRestrictions) ? 'checked' : '';
}


?>


<html>
    <head>
    <title>CCAG Profile</title>
    <link rel="stylesheet" href="./styles/styles.css">
  </head>
    <body>
    <?php include('header.php'); ?>
    <?php include('headerprofile.php'); ?>

        <main>
            <form action="sendDiet.php" method="POST">

                    <h2><label>Allergens</label></h2> <br>

                    <label for="dairy-free">Dairy-Free</label>
                    <input type="checkbox" name="dairy-free" id="dairy-free" <?php echo isChecked('dairy-free', $savedRestrictions); ?>> <br>

                    <label for="egg-free">Egg-Free</label>
                    <input type="checkbox" name="egg-free" id="egg-free" <?php echo isChecked('egg-free', $savedRestrictions); ?>> <br>

                    <label for="peanut-free">Peanut-Free</label>
                    <input type="checkbox" name="peanut-free" id="peanut-free" <?php echo isChecked('peanut-free', $savedRestrictions); ?>> <br>

                    <label for="tree-nut-free">Tree-Nut-Free</label>
                    <input type="checkbox" name="tree-nut-free" id="tree-nut-free" <?php echo isChecked('tree-nut-free', $savedRestrictions); ?>> <br>

                    <label for="wheat-free">Wheat-Free</label>
                    <input type="checkbox" name="wheat-free" id="wheat-free" <?php echo isChecked('wheat-free', $savedRestrictions); ?>> <br>

                    <label for="soy-free">Soy-Free</label>
                    <input type="checkbox" name="soy-free" id="soy-free" <?php echo isChecked('soy-free', $savedRestrictions); ?>> <br>

                    <label for="fish-free">Fish-Free</label>
                    <input type="checkbox" name="fish-free" id="fish-free" <?php echo isChecked('fish-free', $savedRestrictions); ?>> <br>

                    <label for="shellfish-free">Shellfish-Free</label>
                    <input type="checkbox" name="shellfish-free" id="shellfish-free" <?php echo isChecked('shellfish-free', $savedRestrictions); ?>> <br>

                    <label for="sesame-free">Sesame-Free</label>
                    <input type="checkbox" name="sesame-free" id="sesame-free" <?php echo isChecked('sesame-free', $savedRestrictions); ?>> <br>

                    <label for="gluten-free">Gluten-Free</label>
                    <input type="checkbox" name="gluten-free" id="gluten-free" <?php echo isChecked('gluten-free', $savedRestrictions); ?>> <br>

                    <h2><label>Diet Restrictions</label></h2> <br>


                    <label for="alcohol-free">Alcohol-Free</label>
                    <input type="checkbox" name="alcohol-free" id="alcohol-free" <?php echo isChecked('alcohol-free', $savedRestrictions); ?>> <br>

                    <label for="kosher">Kosher</label>
                    <input type="checkbox" name="kosher" id="kosher" <?php echo isChecked('kosher', $savedRestrictions); ?>> <br>

                    <label for="keto-friendly">Keto</label>
                    <input type="checkbox" name="keto-friendly" id="keto-friendly" <?php echo isChecked('keto-friendly', $savedRestrictions); ?>> <br>

                    <label for="vegetarian">Vegetarian</label>
                    <input type="checkbox" name="vegetarian" id="vegetarian" <?php echo isChecked('vegetarian', $savedRestrictions); ?>> <br>

                    <label for="high-fiber">High-Fiber</label>
                    <input type="checkbox" name="high-fiber" id="high-fiber" <?php echo isChecked('high-fiber', $savedRestrictions); ?>> <br>

                    <label for="high-protein">High-Protein</label>
                    <input type="checkbox" name="high-protein" id="high-protein" <?php echo isChecked('high-protein', $savedRestrictions); ?>> <br>

                    <label for="low-carb">Low-Carb</label>
                    <input type="checkbox" name="low-carb" id="low-carb" <?php echo isChecked('low-carb', $savedRestrictions); ?>> <br>

                    <label for="low-fat">Low-Fat</label>
                    <input type="checkbox" name="low-fat" id="low-fat" <?php echo isChecked('low-fat', $savedRestrictions); ?>> <br>

                    <label for="low-sodium">Low-Sodium</label>
                    <input type="checkbox" name="low-sodium" id="low-sodium" <?php echo isChecked('low-sodium', $savedRestrictions); ?>> <br>

                    <label for="low-sugar">Low-Sugar</label>
                    <input type="checkbox" name="low-sugar" id="low-sugar" <?php echo isChecked('low-sugar', $savedRestrictions); ?>> <br>

                    <button type="submit" class="save-button">Save</button>
                </form>
        </main>
    </body>
</html>

