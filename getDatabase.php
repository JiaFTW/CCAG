<?
function getDB() {
$servername = '127.0.0.1';
$username = 'ccagUser';
$password = '12345';
$database = 'ccagDB';

$mydb = new mysqli($servername, $username, $password, $database);

if ($mydb->errno != 0)
{
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	exit(0);
}

echo "successfully connected to database".PHP_EOL;
return $mydb;
}

?>