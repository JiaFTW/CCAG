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

<!DOCTYPE html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCAG Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles/styles.css">
</head>
<body>

    <?php include('header.php'); ?>
    <?php include('headerprofile.php'); ?>

    <div class="container mt-4">
        <main>
            <form action="sendDiet.php" method="POST">

                <h2>Allergens</h2>

                <input type="checkbox" name="dairy-free" id="dairy-free" <?php echo isChecked('dairy-free', $savedRestrictions); ?>>
                <label for="dairy-free">Dairy-Free</label><br>

                <input type="checkbox" name="egg-free" id="egg-free" <?php echo isChecked('egg-free', $savedRestrictions); ?>>
                <label for="egg-free">Egg-Free</label><br>

                <input type="checkbox" name="peanut-free" id="peanut-free" <?php echo isChecked('peanut-free', $savedRestrictions); ?>>
                <label for="peanut-free">Peanut-Free</label><br>

                <input type="checkbox" name="tree-nut-free" id="tree-nut-free" <?php echo isChecked('tree-nut-free', $savedRestrictions); ?>>
                <label for="tree-nut-free">Tree-Nut-Free</label><br>

                <input type="checkbox" name="wheat-free" id="wheat-free" <?php echo isChecked('wheat-free', $savedRestrictions); ?>>
                <label for="wheat-free">Wheat-Free</label><br>

                <input type="checkbox" name="soy-free" id="soy-free" <?php echo isChecked('soy-free', $savedRestrictions); ?>>
                <label for="soy-free">Soy-Free</label><br>

                <input type="checkbox" name="fish-free" id="fish-free" <?php echo isChecked('fish-free', $savedRestrictions); ?>>
                <label for="fish-free">Fish-Free</label><br>

                <input type="checkbox" name="shellfish-free" id="shellfish-free" <?php echo isChecked('shellfish-free', $savedRestrictions); ?>>
                <label for="shellfish-free">Shellfish-Free</label><br>

                <input type="checkbox" name="sesame-free" id="sesame-free" <?php echo isChecked('sesame-free', $savedRestrictions); ?>>
                <label for="sesame-free">Sesame-Free</label><br>

                <input type="checkbox" name="gluten-free" id="gluten-free" <?php echo isChecked('gluten-free', $savedRestrictions); ?>>
                <label for="gluten-free">Gluten-Free</label><br>

                <h2>Diet Restrictions</h2>

                <input type="checkbox" name="alcohol-free" id="alcohol-free" <?php echo isChecked('alcohol-free', $savedRestrictions); ?>>
                <label for="alcohol-free">Alcohol-Free</label><br>

                <input type="checkbox" name="kosher" id="kosher" <?php echo isChecked('kosher', $savedRestrictions); ?>>
                <label for="kosher">Kosher</label><br>

                <input type="checkbox" name="keto-friendly" id="keto-friendly" <?php echo isChecked('keto-friendly', $savedRestrictions); ?>>
                <label for="keto-friendly">Keto</label><br>

                <input type="checkbox" name="vegetarian" id="vegetarian" <?php echo isChecked('vegetarian', $savedRestrictions); ?>>
                <label for="vegetarian">Vegetarian</label><br>

                <input type="checkbox" name="high-fiber" id="high-fiber" <?php echo isChecked('high-fiber', $savedRestrictions); ?>>
                <label for="high-fiber">High-Fiber</label><br>

                <input type="checkbox" name="high-protein" id="high-protein" <?php echo isChecked('high-protein', $savedRestrictions); ?>>
                <label for="high-protein">High-Protein</label><br>

                <input type="checkbox" name="low-carb" id="low-carb" <?php echo isChecked('low-carb', $savedRestrictions); ?>>
                <label for="low-carb">Low-Carb</label><br>

                <input type="checkbox" name="low-fat" id="low-fat" <?php echo isChecked('low-fat', $savedRestrictions); ?>>
                <label for="low-fat">Low-Fat</label><br>

                <input type="checkbox" name="low-sodium" id="low-sodium" <?php echo isChecked('low-sodium', $savedRestrictions); ?>>
                <label for="low-sodium">Low-Sodium</label><br>

                <input type="checkbox" name="low-sugar" id="low-sugar" <?php echo isChecked('low-sugar', $savedRestrictions); ?>>
                <label for="low-sugar">Low-Sugar</label><br>

                <button type="submit" class="smol-button">Save</button>
            </form>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

