<?php

//Universal Functions


function handleQuery($q, mysqli $db, $msg = 'MYSQL: Query Successful') {

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

function duplicateFound($attribute, $col_name, $table_name, mysqli $db) {
    $query = "SELECT COUNT(".$col_name.") 
    FROM ".$table_name." 
    WHERE ".$col_name." = '".$attribute."';";
    $response = handleQuery($query, $db, 'MYSQL: duplicateFound Query Successful');

    $n = $response->fetch_row();
    if ($n[0] > 0) {
        echo "MYSQL: ".$n[0]." duplicates found".PHP_EOL;
        return true;
    }
    else {
        echo "MYSQL: No duplicates found".PHP_EOL;
        return false;
    }

}

//Register Functions


function addAccount($username, $email ,$password, mysqli $db) {
    $query = "INSERT INTO accounts
    (username, email, password) 
    VALUES ('".$username."', '".$email."', '".$password."');";

    //TODO: check if username or email already exsits

    $response = handleQuery($query, $db, "MYSQL: Added Account Succesfuly");

    if ($response == false) {
        //send error
    }
    else {
        //send succesful
    }; 
    
}



?>