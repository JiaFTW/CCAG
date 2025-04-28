#! /usr/bin/php
<?php
    require_once('rabbitmq/path.inc');
    require_once('rabbitmq/get_host_info.inc');
    require_once('rabbitmq/rabbitMQLib.inc');
    function send_bundle($bundleID, $folderType)
    {
        $fileName = $folderType . "_" . $bundleID . ".zip";
        //$rootPath = rtrim($rootPath, '\\/');

        // Get real path for our folder
        $rootPath = realpath(__DIR__ . "/" . $folderType);

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($fileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();

        //$session = ssh2_connect("192.168.193.78",22);
        $filePath = __DIR__ . "/" . $fileName;
        echo $filePath.PHP_EOL;
        exec("scp " . $filePath . " deploy@192.168.193.69:~/Bundles/".$fileName , $output);

        exec("rm " . $filePath, $killme);
    }

    $toSend = array();
    $toSend["db"] = false;
    $toSend["dmz"] = false;
    $toSend["front"] = false;
    $toSend["rabbit"] = false;
    $opener = "Welcome to the CCAG deployment system!
    \nPlease enter what folder(s) you wish to deploy, then type \"0\" when done.
    \nCodes are as follows:
    \nDatabase - 1
    \nDMZ\t - 2
    \nFrontEnd - 3
    \nRabbitMQ - 4
    \nAll\t - 5
    \nCancel\t - 6" . PHP_EOL;
    echo $opener;

    $entry = readline("Please enter a code: ");

    while($entry != 0)
    {
        if(!is_numeric($entry))
        {
            echo "Only numbers from 0-6 are valid codes" . PHP_EOL;
            $entry = readline("Please enter a code: ");
            continue;
        }

        switch($entry)
        {
            case 1:
                $toSend["db"] = true;
                break;
            case 2:
                $toSend["dmz"] = true;
                break;
            case 3:
                $toSend["front"] = true;
                break;
            case 4:
                $toSend["rabbit"] = true;
                break;
            case 5:
                $toSend["db"] = true;
                $toSend["dmz"] = true;
                $toSend["front"] = true;
                $toSend["rabbit"] = true;
                break;
            case 6:
                echo "Cancelling deployment!" . PHP_EOL;
                exit();
            default:
                echo "Invalid code number" . PHP_EOL;
        }

        if($entry == 5)
        {
            echo "All selected!" . PHP_EOL;
            break;
        }

        $entry = readline("Please enter a code: ");
    }

    if(!$toSend["db"] && !$toSend["dmz"] && !$toSend["front"] && !$toSend["rabbit"])
    {
        echo "Nothing selected! Closing file..." . PHP_EOL;
        exit();
    }
    
    $locText = "Please select where these files will be deployed:
    \nQA\t   - 1
    \nProduction - 2
    \nCancel\t   - 3" .PHP_EOL;
    echo $locText;

    $entry = readline("Please enter a code: ");
    $ready = false;
    $location;

    while(!$ready)
    {
        if(!is_numeric($entry))
        {
            echo "Only numbers from 1-3 are valid codes" . PHP_EOL;
            $entry = readline("Please enter a code: ");
            continue;
        }
        
        switch($entry)
        {
            case 1:
                $location = "QA";
                $ready = true;
                break;
            case 2:
                $location = "Production";
                $ready = true;
                break;
            case 3:
                echo "Cancelling deployment!" . PHP_EOL;
                exit();
            default:
                echo "Invalid code number" . PHP_EOL;
        }

        if(!$ready)
        $entry = readline("Please enter a code: ");
    }

    $id = rand();
    //echo $id .PHP_EOL;

    if($toSend["db"])
        send_bundle($id, "Database");

    if($toSend["dmz"])
        send_bundle($id, "DMZ");

    if($toSend["front"])
        send_bundle($id,"FrontEnd");

    if($toSend["rabbit"])
        send_bundle($id, "rabbitmq");

    $client = new rabbitMQClient("testRabbitMQ.ini","DeploymentServer");
    $request = array();
    $request['type'] = "deploy";
    $request['location'] = $location;
    $request['id'] = $id;
    $response = $client->send_request($request);
    echo $response;
?>