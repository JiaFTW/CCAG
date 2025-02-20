<?php

//Universal Functions


function handleQuery($q, mysqli $db, $msg = 'Query Successful') {

    $response = $db->query($q);
    if ($db->errno != 0) {
        echo "failed to execute query:".PHP_EOL;
        echo __FILE__.':'.__LINE__.":error: ".$db->error.PHP_EOL;
        return $response;
    }
    else {
        echo $msg.PHP_EOL;
        return $response;
    }
}

function duplicateFound($d, $col_name, mysqli $db) {
    $query = ""; // TODO: add select query
    $response = handleQuery($query);

}

//Register Functions


function addAccount($username, $email ,$password, mysqli $db) {
    $query = "insert into accounts
    (username, email, password) 
    values ('".$username."', '".$email."', '".$password."');";

    //TODO: check if username or email already exsits

    $response = handleQuery($query, $db, "Added Account Succesfuly" );

    if ($response == false) {
        //send error
    }
    else {
        //send succesful
    }; 
    
}



?>