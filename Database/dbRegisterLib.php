<?php

//WIP add user function 
function addUser($username, $email ,$password, mysqli $db) {
    $query = "insert into accounts 
    ('username', 'email', 'password') 
    values ('.$username.', '.$email.', '.$password.')";

    $response = $db->query($query);
    if ($db->errno != 0) {
        echo "failed to execute query:".PHP_EOL;
        echo __FILE__.':'.__LINE__.":error: ".$db->error.PHP_EOL;
    }
    else {
        echo "Successfully added user";
    }
}



?>