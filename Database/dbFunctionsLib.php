<?php

//Universal Functions


function handleQuery($q, mysqli $db, $msg = 'Query Status: Successful') {

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
    $response = handleQuery($query, $db, 'Query Status: duplicateFound '.$attribute.' Successful');

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
    $response = handleQuery($query, $db, 'Query Status: get UID by username '.$username.' Successful');
    $u = $response->fetch_assoc();

    if ($u == null) {
        echo "getUIDbyUsername: uid for username ".$username." not found".PHP_EOL;
        return null;
    }

    echo "getUIDbyUsername: ".$u['uid']." is related to ".$username.PHP_EOL;
    return $u['uid'];

}



//Account Functions

function addAccount($username, $email ,$password, mysqli $db) {
    $query = "INSERT INTO accounts
    (username, email, password) 
    VALUES ('".$username."', '".$email."', '".$password."');";

    $response = handleQuery($query, $db, "Query Status: Added Account Succesful");

    return $response;
    
}


//Sessions Functions

function generateSession(string $username, int $time_sec, mysqli $db) {
    $uid = getUIDbyUsername($username, $db);
    $token = bin2hex(random_bytes(32)); 
    //TODO hash token $hash_token
    $start_time = time();
    $end_time = $start_time + $time_sec;

    $query = "INSERT INTO sessions
    (uid, cookie_token, start_time, end_time)
    VALUES ('".$uid."', '".$token."', ".$start_time.", ".$end_time.");";

    $response = handleQuery($query, $db, "Query Status: Generate Session Successful");

    return $cookie_token = $response ? $token : null;
}

?>