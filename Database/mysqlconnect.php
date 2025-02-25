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
	
		$response = handleQuery($query, $this->mydb, "MYSQL: Login Query Succesful");
	
		if ($response == false) {
			$status = 'Error';
		}

		$ac = $response->fetch_assoc();

		if ($ac == null || $password != $ac['password']) { //TODO: change != to password_verify() or bycrypt_vertify() for hashed
			$status = 'Invalid';
		} 
		else {
			$status = 'Success';
			$cookie = generateSession($username, 3600, $this->mydb);
		}

		//TODO: send cookie to client

		return array('status' => $status, 'cookie' => $cookie ); 
	
	}

}

	
/* $mydb = new mysqli('127.0.0.1','ccagUser','12345','ccagDB');

if ($mydb->errno != 0)
{
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	exit(0); 
}

echo "successfully connected to database".PHP_EOL;


//Test Adding to Database
//addAccount("Bob","bobby@gmail.com","crabcake",$mydb);

//Test Query
/*$query = "select * from accounts;";

$response = $mydb->query($query);
if ($mydb->errno != 0)
{
	echo "failed to execute query:".PHP_EOL;
	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
	exit(0);
}
if successful, echos out all usernames from accounts table (for testing)
else { 
	while ($r = mysqli_fetch_assoc($response)) {
		echo $r['username'].PHP_EOL;
	}
} 
isDuplicateFound("dummyuser", "username","accounts", $mydb);
isDuplicateFound("Joey", "username","accounts", $mydb);
getUIDbyUsername("Bobby", $mydb);	

*/


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

showAr($testObj->loginAccount("dummyuser", "dummypass"));
showAr($testObj->registerAccount("dummyuser","dummy@email.com", "dummypass"));



?>
