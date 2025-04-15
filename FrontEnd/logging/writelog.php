#! /usr/bin/php
<?php
function writelog($message,$username){


$filename =  __DIR__ . "/" . $username . '_log.txt';
echo $filename;

$datetime = date('Y-m-d H:i:s');
$logMessage = "[$datetime] $username $message\n";

file_put_contents($filename,$logMessage, FILE_APPEND | LOCK_EX);

}

?>