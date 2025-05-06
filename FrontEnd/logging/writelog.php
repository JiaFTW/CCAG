
<?php


function writelog($message,$username){

$ipline = exec("hostname -I");
$ipaddress = substr($ipline, strpos($ipline, " ",0) + 1);
$ipaddress = substr($ipaddress, 0, strpos($ipaddress, " ",0));


$filename =  __DIR__ . "/logs/FrontEnd_" . $ipaddress . '_log.txt';
if (!file_exists($filename)) {
    touch($filename, 0744);
}

$datetime = date('Y-m-d H:i:s');
$logMessage = "[$datetime] User: $username $message\n";

file_put_contents($filename,$logMessage, FILE_APPEND | LOCK_EX);


}

?>