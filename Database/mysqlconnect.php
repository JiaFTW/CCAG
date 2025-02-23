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

	public function getConnectionStatus () {
		return $this->dbConnectionStatus;
	}


	//Returns String
	public function registerAccount($username, $email, $password) { 
		$register_status = isDuplicateFound($username, "username", "accounts", $this->mydb) ? 'Duplicate' : '';
		$register_status = isDuplicateFound($email, "email", "accounts", $this->mydb) ? 'Duplicate' : '';

		if ($register_status != '') {
			return $register_status;
		}
		//TODO: Validate Email and user format
		//TODO: Hash Password before query

		$register_status = addAccount($username, $email, $password, $this->mydb) ? 'Success' : 'Error';

		return $register_status;
	}

	//Returns String
	public function loginAccount($username, $password) {
		$query = "SELECT username, password FROM accounts 
		WHERE username = '".$username."';";
	
		$response = handleQuery($query, $this->mydb, "MYSQL: Login Query Succesful");
	
		if ($response == false) {
			return 'Error';
		}

		$ac = $response->fetch_assoc();

		if ($ac == null || $password != $ac['password']) { //TODO: change != to password_verify() for hashed
			return 'Invalid';
		} 
		else {
			return 'Success';
		}

		//TODO: generate session server side
		//TODO: send cookie to client
	
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


$testObj = new mysqlConnect('127.0.0.1','ccagUser','12345','ccagDB');
echo $testObj->loginAccount("dummyuser", "dummypass").PHP_EOL;
echo $testObj->registerAccount("dummyuser","dummy@email.com", "dummypass").PHP_EOL;



?>
