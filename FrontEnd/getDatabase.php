<?
function getDB() {
$servername = '192.168.191.78';
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
