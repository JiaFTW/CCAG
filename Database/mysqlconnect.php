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
		WHERE username = '".$username."';";
		$status;
		$cookie = null;
	
		$response = handleQuery($query, $this->mydb, "Query Status: Login Succesfull");
	
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

		$arraytest = array('status' => $status, 'cookie' => $cookie, 'username' => $username );
		//showAr($arraytest);
		return $arraytest; 
	
	}


	//returns Array
	public function validateSession($token) {
		$status;
		$query = "SELECT cookie_token, end_time FROM sessions 
		WHERE cookie_token = '".$token."';";    //TODO change to verftiy() for hashed tokens (might need to change query)
		$response = handleQuery($query, $this->mydb, "Query Status: Validate Session Succesfull");
		if ($response == false) {
			$status = 'Error';
			return array('status' => $status);
		}

		$response_arr = $response->fetch_assoc();
		
		if($response_arr == null) {
			$status = 'NotFound';
			return array('status' => $status);
		}
		elseif ($response_arr['end_time'] <= time()) {
			$status = 'Expired';
			return array('status' => $status);
		} 
		else {
			$status = 'Success';
			return array('status' => $status);
		}
	}

	public function invalidateSession($token) {
		$query = "DELETE FROM sessions WHERE cookie_token = '".$token."';";
		$response = handleQuery($query, $this->mydb, "Query Status: Invalidate Session Successfull");
		
		return array('status' => $response ? 'Success' : 'Error');
	}

}

	


//For Testing  and debugging
/*function showAr ($array) {
	foreach ($array as $key => $value) {
		echo "Key: $key; Value: $value\n";
	}
}


$testObj = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');

showAr($testObj->registerAccount("Bob","bobby@gmail.com", "crabcake"));
showAr($testObj->registerAccount("dummyuser","dummy@email.com", "dummypass"));
showAr($testObj->registerAccount("Larry2","Larry6@email.com", "snail"));

showAr($testObj->loginAccount("dummyuser", "dummypass"));    //TODO test validSession function
showAr($testObj->registerAccount("dummyuser","dummy@email.com", "dummypass")); */



?>
