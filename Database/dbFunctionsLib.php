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

function isDuplicateFound($attribute, $col_name, $table_name, mysqli $db) {  //returns boolean if a duplicate is found
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

    echo "getUIDbyUsername: ".$u['uid']." related to ".$username.PHP_EOL;
    return $u['uid'];

}



//Account Functions

function addAccount($username, $email ,$password, mysqli $db) {
    $query = "INSERT INTO accounts
    (username, email, password) 
    VALUES ('".$username."', '".$email."', '".$password."');";

    $response = handleQuery($query, $db, "Query Status: Added Account Query Succesful");

    return $response;
    
}

function getBookmarks($uid) {

}

function getMealPlan($uid) {

}

function getUserPreference($uid) {

}

function getReview($rate_id) {
    
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

    $response = handleQuery($query, $db, "Query Status: Generate Session Query Successful");

    return $cookie_token = $response ? $token : null;
}

//Recipe Funcitons

function addRecipe($name, $image, $num_ingredients, $ingredients, $calories, $servings, $labels, mysqli $db) {

$labels_arr = array_map('trim', explode(',', $labels));
$formatted_labels = "'" . implode("','", $labels_arr) . "'";
//echo $formatted_labels;

$first_query = 
"INSERT INTO recipes (name, image, num_ingredients, ingredients, calories, servings) 
VALUES ('".$name."', '".$image."', ".$num_ingredients.", '".$ingredients."', ".$calories.", ".$servings.");";

$second_query = 
"INSERT INTO recipe_labels (rid, label_id)
SELECT LAST_INSERT_ID(), label_id
FROM labels WHERE label_name IN (".$formatted_labels.");";

$response = handleQuery($first_query, $db, "Query Status: Add Recipe Successfull");
if (!$response) {
    return $response;
}
$response = handleQuery($second_query, $db, "Query Status: Add Recipe Labels Successfull");

return $response;
}

function getRecipe($rid) {

}

//Bookmark Functions
function addBookmark($uid, $rid, mysqli $db) {
    $query = "";
    $response = handleQuery($query, $db, "Query Status: Add Bookmark Successfull");

    return $response;
}

function removeBookmark($uid, $rid) {
    $query = "";
    $response = handleQuery($query, $db, "Query Status: Remove Bookmark Successfull");

    return $response;
}

?>