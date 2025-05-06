#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('../Database/mysqlconnect.php');

/*function doLogin($username,$password)
{
  
    $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

    return $connect->loginAccount($username, $password);
    
}*/

function doLogin($username, $password) {
    $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');
    $response = $connect->loginAccount($username, $password);

    if ($response['status'] === 'Success') {
        // Check email verification
        $userData = $connect->mydb->query(
            "SELECT email_verified, email, tfa_enabled 
             FROM accounts WHERE username = '".
             $connect->mydb->real_escape_string($username)."'"
        )->fetch_assoc();

        if (!$userData['email_verified']) {
            return ['status' => 'EmailNotVerified', 'email' => $userData['email']];
        }

        // NEW: Check 2FA status
        if ($userData['tfa_enabled']) {
            $code = bin2hex(random_bytes(16));
            $expiry = time() + 3600; // 1 hour
            
            $connect->mydb->query(
                "UPDATE accounts SET 
                 verification_code = '".$connect->mydb->real_escape_string($code)."',
                 code_expiry = $expiry 
                 WHERE username = '".$connect->mydb->real_escape_string($username)."'"
            );
            
            require_once('../FrontEnd/emailConfig.php');
            if (sendVerificationEmail($userData['email'], $code)) {
                return [
                    'status' => '2FA_Required',
                    'email' => $userData['email']
                ];
            }
            return ['status' => 'Error', 'message' => 'Failed to send 2FA code'];
        }
    }
    
    return $response;
}

function doVerification($email, $code) {
    try {
        $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("invalid_email_format", 1001);
        }

        $escEmail = $connect->mydb->real_escape_string($email);
        $escCode = $connect->mydb->real_escape_string($code);

        // Get current verification state
        $result = $connect->mydb->query(
            "SELECT verification_code, code_expiry, username
             FROM accounts 
             WHERE email = '$escEmail'"
        );
        
        if ($result->num_rows === 0) {
            throw new Exception("account_not_found", 1002);
        }

        $user = $result->fetch_assoc();

        // Validate code match
        if ($user['verification_code'] !== $escCode) {
            throw new Exception("code_mismatch", 1003);
        }

        // Check expiration (server time vs stored expiry)
        if (time() > $user['code_expiry']) {
            throw new Exception("code_expired", 1004);
        }

        // Start transaction
        $connect->mydb->begin_transaction();

        try {
            // Update verification status
            $updateResult = $connect->mydb->query(
                "UPDATE accounts SET 
                verification_code = NULL,
                code_expiry = NULL,
                email_verified = 1 
                WHERE email = '$escEmail'"
            );

            if ($connect->mydb->affected_rows === 0) {
                throw new Exception("verification_update_failed", 1005);
            }

            // Generate session token
            $token = bin2hex(random_bytes(32));
            $start_time = time();
            $end_time = $start_time + 3600;

            // Insert session
            $connect->mydb->query(
                "INSERT INTO sessions (uid, cookie_token, start_time, end_time)
                SELECT uid, '$token', $start_time, $end_time
                FROM accounts WHERE email = '$escEmail'"
            );

            $connect->mydb->commit();
        } catch (Exception $e) {
            $connect->mydb->rollback();
            throw $e;
        }

        return [
            'status' => 'Success',
            'cookie' => $token,
            'username' => $user['username']
        ];

    } catch (Exception $e) {
        error_log("Verification Error: " . $e->getMessage());
        return [
            'status' => 'Error',
            'message' => $e->getMessage(),
            'error_code' => $e->getCode()
        ];
    }
}
/*


*/



function doRegistration($username, $password, $email) {
    $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');
    $result = $connect->registerAccount($username, $email, $password);
    
    if ($result['status'] === 'Success') {
        // Add proper escaping
        $code = bin2hex(random_bytes(16));
        $expiry = time() + 3600;
        
        // Escape values
        $escEmail = $connect->mydb->real_escape_string($email);
        $escCode = $connect->mydb->real_escape_string($code);
        
        $updateQuery = "UPDATE accounts 
                        SET verification_code = '$escCode', 
                            code_expiry = $expiry 
                        WHERE email = '$escEmail'";
        
        if ($connect->mydb->query($updateQuery)) {
            require_once('../FrontEnd/emailConfig.php');
            if (sendVerificationEmail($email, $code)) {
                return $result;
            }
            return ['status' => 'Error', 'message' => 'Email send failed'];
        }
        return ['status' => 'Error', 'message' => 'Database update failed'];
    }
    return $result;
}

/*function doRegistration($username,$password,$email)
{
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->registerAccount($username, $email, $password);
}*/


function doUpdate2FA($username, $status) {
    try {
        error_log("=== START 2FA UPDATE ===");
        $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');
        
        // Verify connection
        if ($connect->mydb->connect_errno) {
            throw new Exception("DB connection failed: ".$connect->mydb->connect_error);
        }

        // Debug user input
        error_log("Username: $username, Status: $status");

        // Execute query
        $esc_user = $connect->mydb->real_escape_string($username);
        $query = "UPDATE accounts SET tfa_enabled = ".(int)$status." 
                WHERE username = '$esc_user'";
        error_log("Executing: $query");
        
        if ($connect->mydb->query($query)) {
            error_log("=== UPDATE SUCCESS ===");
            return ['status' => 'Success'];
        }
        
        throw new Exception("Query failed: ".$connect->mydb->error);
    } catch (Exception $e) {
        error_log("!!! 2FA ERROR: ".$e->getMessage());
        return ['status' => 'Error', 'message' => $e->getMessage()];
    }
}

function doGet2FAStatus($username) {
    try {
        error_log("Getting 2FA status for: $username");
        $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');
        
        $esc_user = $connect->mydb->real_escape_string($username);
        $query = "SELECT tfa_enabled FROM accounts WHERE username = '$esc_user'";
        
        error_log("Executing query: $query");
        $result = $connect->mydb->query($query);
        
        if (!$result) {
            throw new Exception("Query failed: ".$connect->mydb->error);
        }
        
        if ($result->num_rows === 0) {
            throw new Exception("User not found");
        }
        
        $row = $result->fetch_assoc();
        error_log("Query result: ".print_r($row, true));
        
        return [
            'status' => 'Success',
            'tfa_enabled' => (bool)$row['tfa_enabled']
        ];
        
    } catch (Exception $e) {
        error_log("2FA Status Error: ".$e->getMessage());
        return [
            'status' => 'Error',
            'message' => $e->getMessage()
        ];
    }
}



function doValidate($token){

  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->validateSession($token);
  
}

function doLogout($token) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->invalidateSession($token);
}

function doDiet($username, $pref_array) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->changeUserPref($username, $pref_array);
}

function doGetDiet($username) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->getUserDiet($username);
}

function doAddFavorite($username, $rid) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->addFavorite($username, $rid);
}

function doRemoveFavorite($username, $rid) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->removeFavorite($username, $rid);
}

function doGetFavorites($username) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->getUserFavorites($username);
}

function doEditRecipe($rid, $ingredients, $name, $username) {
  $connect = new mysqlConnect('p:127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->editRecipe($rid, $ingredients, $name, $username);
}
function doAddReview($username, $rid, $rate, $text) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->addReview($username, $rid, $rate, $text);
}

function doRemoveReview($rate_id) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->removeReview($rate_id);
}

function doGetUserReviews($username) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->getUserReviews($username);
}

function doAddMealPlan($array) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->addMealPlan($array);

}

function doGetUserMealPlans($username) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->getUserMealPlans($username);
}

function doGetRex($username) {
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  return $connect->getRex($username);
}

function doRecipe($keyword, $username) //perform search check
{
  $connect = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

  $labels = $connect->getUserDiet($username);

  print_r($labels);
 
  $response = $connect->checkRecipe($keyword); //TODO search by label

  if($response == 'false') { //return if mysql error
    echo "Recipe Search: Database ERROR | Returning DB_ERROR".PHP_EOL;
    return array('status' => 'DB_Error');
  }

  if ($response == null) { //will fetch from DMZ if no results from database
    
    echo "Recipe Search: Not Enough Results Found in Database | Calling DMZ".PHP_EOL;
    $client = new rabbitMQClient("testRabbitMQ.ini","DMZServer");
    $request = array();
    $request['type'] = "searchRecipe";
    $request['query'] = $keyword; 
    $dmz_response = $client->send_request($request);
    //print_r($dmz_response);
    if($dmz_response['status'] != 'success') {
      echo $dmz_response['message'];
      return array('status' => 'DMZ_Error');
    }
    if($connect->populateRecipe($dmz_response['recipes']) === false) {  //populate db with response
      echo "Recipe Search: Database Populating Issue | Returning DB_ERROR".PHP_EOL;
      return array('status' => 'DB_Error');
    }

    $response = $connect->checkRecipe($keyword); //perform search check agian */
  }

  echo "Recipe Search: Results Found in Database  | Returning Array".PHP_EOL;

  print_r($response);
  return $response;
}

function requestProcessor($request)
{
  error_log("Processing request type: ".$request['type']); 
 // echo "received request".PHP_EOL;
  // var_dump($request);
  if(!isset($request['type']))
  {
   // return "ERROR: unsupported message type";
   return ["status" => "Error", "message" => "Unsupported message type"];
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
    case "logout":
      return doLogout($request['sessionId']);
    case "register":
      return doRegistration($request['username'],$request['password'],$request['email']);
    case "getRecipe":
      return doRecipe($request['keyword'], $request['username']);
    case "editRecipe":
      return doEditRecipe($request['rid'], $request['ingredients'], $request['name'], $request['username']);
    case "diet":
      return doDiet($request['username'], $request['restrictions']);
    case "getDiet":
      return doGetDiet($request['username']);
    case "addFavorite":
      return doAddFavorite($request['username'], $request['rid']);
    case "removeFavorite":
      return doRemoveFavorite($request['username'], $request['rid']);
    case "getFavorites":
      return doGetFavorites($request['username']);
    case "addReview":
      return doAddReview($request['username'], $request['rid'], $request['rating'], $request['review']);
    case "removeReview":
      return doRemoveReview($request['rate_id']);
    case "getUserReviews":
      return doGetUserReviews($request['username']);
    case "addMealPlan":
      return doAddMealPlan($request);
    case "getUserMealPlans":
      return doGetUserMealPlans($request['username']);
    case "verify_code":
      return doVerification($request['email'], $request['code']);
//new cases

    case "update_2fa":
      return doUpdate2FA($request['username'], $request['tfa_enabled']);
    case "get_2fa_status":
      return doGet2FAStatus($request['username']);

//
    case "getRex":
      return doGetRex($request['username']);
    default:
      return "type fail".PHP_EOL;
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

//echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
//echo "testRabbitMQServer END".PHP_EOL;
exit();
?>




