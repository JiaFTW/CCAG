#!/usr/bin/php

<?php
$zipFile = '/home/GaryShi/TestFolder.zip'; //The file we will get is the message we will get from Sinchi's deployment server
$extractTo = __DIR__; //This extracts to current directory.

$zip = new ZipArchive;

if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($extractTo);
    $zip->close();
    echo "IT WORKED!";
}
else {
    echo "It no work...";
}
?>