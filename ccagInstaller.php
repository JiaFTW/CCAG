#!/usr/bin/php

<?php
$zipFile = '/home/GaryShi/TestFolder.zip';
$extractTo = __DIR__;

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