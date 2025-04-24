#! /usr/bin/php
<?php
function sendinglogs()
{

/*
$filename = "logs_" . gethostname() . ".zip";
*/


$logPath = __DIR__ . "/logs";


$files = glob($logPath . "/*");


$filePath = $files[0];


/*
$zip = new ZipArchive();
$zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

$zip->addFile($filePath, basename($filePath));

$zip->close();
*/

//echo "Zip file created at: " . $filePath . PHP_EOL;

exec("scp " . $filePath . " deploy@192.168.193.69:~/Logs/");

}


?>