<?php

require_once('../rabbitmq/testRabbitMQClient.php');
require_once('./logging/writelog.php');

$restrictions =[];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['dairy-free'])) {
        $restrictions[] = 'dairy-free';
    }

    if (isset($_POST['egg-free'])) {
        $restrictions[] = 'egg-free';
    }

    if (isset($_POST['peanut-free'])) {
        $restrictions[] = 'peanut-free';
    }

    if (isset($_POST['tree-nut-free'])) {
        $restrictions[] = 'tree-nut-free';
    }

    if (isset($_POST['wheat-free'])) {
        $restrictions[] = 'wheat-free';
    }

    if (isset($_POST['soy-free'])) {
        $restrictions[] = 'soy-free';
    }

    if (isset($_POST['fish-free'])) {
        $restrictions[] = 'fish-free';
    }

    if (isset($_POST['shellfish-free'])) {
        $restrictions[] = 'shellfish-free';
    }

    if (isset($_POST['sesame-free'])) {
        $restrictions[] = 'sesame-free';
    }

    if (isset($_POST['gluten-free'])) {
        $restrictions[] = 'gluten-free';
    }

    if (isset($_POST['alcohol-free'])) {
        $restrictions[] = 'alcohol-free';
    }

    if (isset($_POST['kosher'])) {
        $restrictions[] = 'kosher';
    }

    if (isset($_POST['keto-friendly'])) {
        $restrictions[] = 'keto-friendly';
    }

    if (isset($_POST['vegetarian'])) {
        $restrictions[] = 'vegetarian';
    }

    if (isset($_POST['high-fiber'])) {
        $restrictions[] = 'high-fiber';
    }

    if (isset($_POST['high-protein'])) {
        $restrictions[] = 'high-protein';
    }

    if (isset($_POST['low-carb'])) {
        $restrictions[] = 'low-carb';
    }

    if (isset($_POST['low-fat'])) {
        $restrictions[] = 'low-fat';
    }

    if (isset($_POST['low-sodium'])) {
        $restrictions[] = 'low-sodium';
    }

    if (isset($_POST['low-sugar'])) {
        $restrictions[] = 'low-sugar';
    }

    $dietdata = array (
        'type' => 'diet',
        'username' => $_COOKIE['username'],
        'restrictions' => $restrictions
    );

    $response = sendMessage($dietdata);
    writelog("made changes to their diet restrictions.", $_COOKIE['username']);
    header("Location: dietpage.php");
    die();


    /*THIS IS WHERE WE WILL NEED TO DO SENDMESSAGE FOR THE LABELS/RESTRICTION FUNCTIONS FOR USER.
    echo "THIS IS TESTING DIET FOR ";
    echo $_COOKIE['username'];
    echo "ARRAY BEING SENT: ";
    print_r($restrictions);
    echo "THIS IS DIETDATA ARRAY: ";
    echo $dietdata['type'];
    echo $dietdata['username'];
    echo $dietdata['restrictions'];*/
}

?>