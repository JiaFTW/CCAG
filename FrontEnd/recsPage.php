<?php 
require_once('../rabbitmq/testRabbitMQClient.php');


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
            <form action="recs.php" method="POST">

                    <button type="submit" class="save-button">Get Recommendations</button>
                </form>
        </main>
    </body>
</html>

