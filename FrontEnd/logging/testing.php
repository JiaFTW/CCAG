#! /usr/bin/php
<?php
$ipline = exec("hostname -I");
$ipaddress = substr($ipline, strpos($ipline, " ",0) + 1);
$ipaddress = substr($ipaddress, 0, strpos($ipaddress, " ",0));

echo $ipaddress
?>