<?php
require_once('sessionValidate.php');
require_once('../rabbitmq/testRabbitMQClient.php'); 
$username = $_COOKIE['username'];


$fetchData = array(
    'type' => 'getDiet',
    'username' => $username
);

$response = sendMessage($fetchData); 


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
            <h2><label>Allergens</label></h2><br>

            <?php
           
            $allRestrictions = [
                "dairy-free" => "dairy-Free",
                "egg-free" => "egg-Free",
                "peanut-free" => "peanut-Free",
                "tree-nut-free" => "tree-nut-free",
                "wheat-free" => "wheat-Ffree",
                "soy-free" => "soy-free",
                "fish-free" => "fish-free",
                "shellfish-free" => "shellfish-free",
                "sesame-free" => "sesame-free",
                "gluten-free" => "gluten-free",
                "alcohol-free" => "alcohol-free",
                "kosher" => "kosher",
                "keto-friendly" => "keto",
                "vegetarian" => "vegetarian",
                "high-fiber" => "high-fiber",
                "high-protein" => "high-protein",
                "low-carb" => "low-carb",
                "low-fat" => "low-fat",
                "low-sodium" => "low-sodium",
                "low-sugar" => "low-sugar"
            ];

            foreach ($allRestrictions as $key => $label) {
                $isChecked = in_array($key, $savedRestrictions) ? "checked" : "";
                echo "<label for='$key'>$label</label>
                      <input type='checkbox' name='$key' id='$key' $isChecked><br>";
            }
            ?>

            <button type="submit" class="save-button">Save</button>
        </form>
    </main>
</body>
</html>

<?php
$restrictions = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
