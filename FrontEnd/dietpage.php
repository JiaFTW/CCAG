<?php 
require_once('sessionValidate.php');
require_once('../rabbitmq/testRabbitMQClient.php');

$fetchDiet = [
    'type' => 'getDiet',
    'username' => $_COOKIE['username'],
];

$response = sendMessage($fetchDiet);
$restrictions = isset($response['labels_str']) ? $response['labels_str'] : [];

function isChecked($match, $selectedRestrictions) {
    return in_array($match, $selectedRestrictions) ? 'checked' : '';
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
            <form method="POST">

                    <h2><label>Allergens</label></h2> <br>

                    <label for="dairy-free">Dairy-Free</label>
                    <input type="checkbox" name="dairy-free" id="dairy-free" <?php isChecked('dairy-free', $restrictions) ?>> <br>

                    <label for="egg-free">Egg-Free</label>
                    <input type="checkbox" name="egg-free" id="egg-free" <?php isChecked('egg-free', $restrictions) ?>> <br>

                    <label for="peanut-free">Peanut-Free</label>
                    <input type="checkbox" name="peanut-free" id="peanut-free"<?php isChecked('peanut-free', $restrictions) ?>> <br>

                    <label for="tree-nut-free">Tree-Nut-Free</label>
                    <input type="checkbox" name="tree-nut-free" id="tree-nut-free"<?php isChecked('tree-nut-free', $restrictions) ?>> <br>

                    <label for="wheat-free">Wheat-Free</label>
                    <input type="checkbox" name="wheat-free" id="wheat-free"<?php isChecked('wheat-free', $restrictions) ?>> <br>

                    <label for="soy-free">Soy-Free</label>
                    <input type="checkbox" name="soy-free" id="soy-free"<?php isChecked('soy-free', $restrictions) ?>> <br>

                    <label for="fish-free">Fish-Free</label>
                    <input type="checkbox" name="fish-free" id="fish-free"<?php isChecked('fish-free', $restrictions) ?>> <br>

                    <label for="shellfish-free">Shellfish-Free</label>
                    <input type="checkbox" name="shellfish-free" id="shellfish-free"<?php isChecked('shellfish-free', $restrictions) ?>> <br>

                    <label for="sesame-free">Sesame-Free</label>
                    <input type="checkbox" name="sesame-free" id="sesame-free"<?php isChecked('sesame-free', $restrictions) ?>> <br>

                    <h2><label>Diet Restrictions</label></h2> <br>

                    <label for="gluten-free">Gluten-Free</label>
                    <input type="checkbox" name="gluten-free" id="gluten-free"<?php isChecked('gluten-free', $restrictions) ?>> <br>

                    <label for="alcohol-free">Alcohol-Free</label>
                    <input type="checkbox" name="alcohol-free" id="alcohol-free"<?php isChecked('alcohol-free', $restrictions) ?>> <br>

                    <label for="kosher">Kosher</label>
                    <input type="checkbox" name="kosher" id="kosher"<?php isChecked('kosher', $restrictions) ?>> <br>

                    <label for="keto-friendly">Keto</label>
                    <input type="checkbox" name="keto-friendly" id="keto-friendly"<?php isChecked('keto-friendly', $restrictions) ?>> <br>

                    <label for="vegetarian">Vegetarian</label>
                    <input type="checkbox" name="vegetarian" id="vegetarian"<?php isChecked('vegetarian', $restrictions) ?>> <br>

                    <label for="high-fiber">High-Fiber</label>
                    <input type="checkbox" name="high-fiber" id="high-fiber"<?php isChecked('high-fiber', $restrictions) ?>> <br>

                    <label for="high-protein">High-Protein</label>
                    <input type="checkbox" name="high-protein" id="high-protein"<?php isChecked('high-protein', $restrictions) ?>> <br>

                    <label for="low-carb">Low-Carb</label>
                    <input type="checkbox" name="low-carb" id="low-carb"<?php isChecked('low-carb', $restrictions) ?>> <br>

                    <label for="low-fat">Low-Fat</label>
                    <input type="checkbox" name="low-fat" id="low-fat"<?php isChecked('low-fat', $restrictions) ?>> <br>

                    <label for="low-sodium">Low-Sodium</label>
                    <input type="checkbox" name="low-sodium" id="low-sodium"<?php isChecked('low-sodium', $restrictions) ?>> <br>

                    <label for="low-sugar">Low-Sugar</label>
                    <input type="checkbox" name="low-sugar" id="low-sugar"<?php isChecked('low-sugar', $restrictions) ?>> <br>

                    <button type="submit" class="save-button">Save</button>
                </form>
        </main>
    </body>
</html>

<?php

$restrictions = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $restriction_keys = [
        'dairy-free', 'egg-free', 'peanut-free', 'tree-nut-free','wheat-free',
        'soy-free', 'fish-free', 'shellfish-free', 'sesame-free', 'gluten-free',
        'alcohol-free', 'kosher', 'keto-friendly'
    ];

    foreach ($allRestrictions as $key => $label) {
        if (isset($_POST[$key])) {
            $restrictions[] = $key;
        }
    }

    $dietdata = array(
        'type' => 'diet',
        'username' => $_COOKIE['username'],
        'restrictions' => $restrictions
    );

    $response = sendMessage($dietdata);

 
}
?>
