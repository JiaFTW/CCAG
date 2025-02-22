<?php

//Universal Functions


function handleQuery($q, mysqli $db, $msg = 'MYSQL: Query Successful') {

    $response = $db->execute_query($q);
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

function isDuplicateFound($attribute, $col_name, $table_name, mysqli $db) {
    $query = "SELECT COUNT(".$col_name.") 
    FROM ".$table_name." 
    WHERE ".$col_name." = '".$attribute."';";
    $response = handleQuery($query, $db, 'MYSQL: duplicateFound '.$attribute.' Query Successful');

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

function getUIDbyUsername(string $username, mysqli $db) {
    $query = "SELECT uid FROM accounts WHERE username = '".$username."';";
    $response = handleQuery($query, $db, 'MYSQL: get UID by username '.$username.' Query Successful');
    $u = $response->fetch_assoc();

    if ($u == null) {
        echo "MySQL: uid for username ".$username." not found".PHP_EOL;
        return null;
    }

    echo "MYSQL: ".$u['uid']." related to ".$username.PHP_EOL;
    return $u['uid'];

}



//Account Functions

function addAccount($username, $email ,$password, mysqli $db) {
    $query = "INSERT INTO accounts
    (username, email, password) 
    VALUES ('".$username."', '".$email."', '".$password."');";

    $response = handleQuery($query, $db, "MYSQL: Added Account Query Succesful");

    return $response;
    
}

//Sessions Functions

?>