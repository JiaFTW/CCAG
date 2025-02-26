#!/usr/bin/php
<?php

//TODO: make this into class or function

require_once 'dbFunctionsLib.php';

class mysqlConnect {
	protected $dbConnectionStatus = false; 
	protected $mydb;

	public function __construct($address, $db_user, $db_pass, $db_name) {
		//use 127.0.0.1 to connect to your local mysql server.
		$this->mydb = new mysqli($address,$db_user ,$db_pass, $db_name);
		$this->connectDB();
	}

	protected function connectDB() {
		if ($this->mydb->errno != 0) {
			echo "failed to connect to database: ". $this->mydb->error . PHP_EOL;
			$dbConnectionStatus = false;
		}
		else {
			echo "successfully connected to database".PHP_EOL;
			$dbConnectionStatus = true;
		}
	}

	//Returns Bool
	public function getConnectionStatus () {
		return $this->dbConnectionStatus;
	}


	//Returns Array
	public function registerAccount($username, $email, $password) {
		$register_status;
		$invalid_status = isDuplicateFound($username, "username", "accounts", $this->mydb) ? 'user_duplicate' : '';
		$invalid_status = isDuplicateFound($email, "email", "accounts", $this->mydb) ? 'email_duplicate' : '';

		if ($invalid_status != '') {
			$register_status = 'Invalid';
			return array('status' => $register_status, 'invalid_type' => $invalid_status);
		}
		//TODO: Validate Email and user format
		//TODO: Hash Password before query

		$register_status = addAccount($username, $email, $password, $this->mydb) ? 'Success' : 'Error';


		return array('status' => $register_status, 'invalid_type' => null);
	}

	//Returns Array
	public function loginAccount($username, $password) {
		$query = "SELECT username, password FROM accounts 
		WHERE username = ?";
		$status;
		$cookie = null;


		
		         // Prepare and execute the query
        $stmt = $this->mydb->prepare($query); // Modified: Added prepared statement
        $stmt->bind_param('s', $username); // Modified: Added prepared statement
        $stmt->execute(); // Modified: Added prepared statement
        $result = $stmt->get_result(); // Modified: Added prepared statement

        if ($result->num_rows === 0) {
            $status = 'Invalid';
            return array('status' => $status, 'cookie' => $cookie);
        }

        $ac = $result->fetch_assoc();

        if ($ac == null || !password_verify($password, $ac['password'])) { // Modified: Use password_verify for hashed passwords
            $status = 'Invalid';
        } else {
            $status = 'Success';
            $cookie = generateSession($username, 3600, $this->mydb); // Modified: Generate session and return cookie_token
        }

        return array('status' => $status, 'cookie' => $cookie); 
    }
		

/*
		$response = handleQuery($query, $this->mydb, "Query Status: Login Succesful");
	
		if ($response == false) {
			$status = 'Error';
			return array('status' => $status, 'cookie' => $cookie );
		}

		$ac = $response->fetch_assoc();

		if ($ac == null || $password != $ac['password']) { //TODO: change != to password_verify() or bycrypt_vertify() for hashed
			$status = 'Invalid';
		} 
		else {
			$status = 'Success';
			$cookie = generateSession($username, 3600, $this->mydb);
		}


		return array('status' => $status, 'cookie' => $cookie ); 
	
	}


	//returns Array
	public function validateSession($token) {
		$status;
		$query = "SELECT cookie_token, end_time FROM sessions 
		WHERE cookie_token = '".$token.";";    //TODO change to verftiy() for hashed tokens (might need to change query)
		$response = handleQuery($query, $this->mydb, "Query Status: Validate Session Succesful");
		if ($response == false) {
			$status = 'Error';
			return array('status' => $status);
		}

		$response_arr = $response->fetch_assoc();
		
		if($response_arr = null) {
			$staus = 'NotFound';
			return array('status' => $status);
		}
		else {
			$status = ($response_arr['end_time'] > time()) ? 'Success' : 'Expired';
			return array('status' => $status);
		}
	}

}
 */

     // Returns Array
    public function validateSession($auth_token, $session_id) {
        $query = "SELECT cookie_token, end_time 
                  FROM sessions 
                  WHERE cookie_token = ? AND uid = (
                      SELECT uid FROM sessions WHERE cookie_token = ?
                  )"; // Modified: Added prepared statement
        $status;

        // Prepare and execute the query
        $stmt = $this->mydb->prepare($query); // Modified: Added prepared statement
        $stmt->bind_param('ss', $auth_token, $session_id); // Modified: Added prepared statement
        $stmt->execute(); // Modified: Added prepared statement
        $result = $stmt->get_result(); // Modified: Added prepared statement

        if ($result->num_rows === 0) {
            $status = 'NotFound';
            return array('status' => $status);
        }

        $response_arr = $result->fetch_assoc();

        if ($response_arr['end_time'] > time()) {
            $status = 'Success';
        } else {
            $status = 'Expired';
        }

        return array('status' => $status);
    }
 	


    // Returns Array (New Method)
    public function getUserByToken($auth_token) {
        // Added: New method to fetch user data by auth_token
        $query = "SELECT accounts.username, accounts.email 
                  FROM accounts 
                  JOIN sessions ON accounts.uid = sessions.uid 
                  WHERE sessions.cookie_token = ?";

        // Prepare and execute the query
        $stmt = $this->mydb->prepare($query); // Added: Prepared statement
        $stmt->bind_param('s', $auth_token); // Added: Prepared statement
        $stmt->execute(); // Added: Prepared statement
        $result = $stmt->get_result(); // Added: Prepared statement

        // Fetch the user data
        if ($result->num_rows > 0) {
            return $result->fetch_assoc(); // Return the user data as an associative array
        } else {
            return null; // No user found
        }
    }
}


//For Testing  and debugging
function showAr ($array) {
	foreach ($array as $key => $value) {
		echo "Key: $key; Value: $value\n";
	}
}


$testObj = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

showAr($testObj->registerAccount("Bob","bobby@gmail.com", "crabcake"));
showAr($testObj->registerAccount("dummyuser","dummy@email.com", "dummypass"));
showAr($testObj->registerAccount("Larry2","Larry6@email.com", "snail"));

showAr($testObj->loginAccount("dummyuser", "dummypass"));    //TODO test validSession function
showAr($testObj->registerAccount("dummyuser","dummy@email.com", "dummypass"));



?>
