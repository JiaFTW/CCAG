<?php

$ccag_machines = parse_ini_file('ccag_machines.ini', true); //Multidimenstional Array Usage: $ccag_machines['Dev']['FrontEnd'] Output >> 192.168.193.70

function pulseCheck($ip) { //returns boolean (true if ping successful)
    $command = sprintf('ping -c 1 -W 1 %s > /dev/null 2>&1', escapeshellarg($ip));
    exec($command, $output, $exitCode);
    return $exitCode === 0;
}

function detectCluster() {
    global $ccag_machines;
    $this_machine_ips = exec("hostname -I");
    $host_ips = array_map('trim', explode(' ', $this_machine_ips));

    foreach ($ccag_machines as $layer => $machine) {
        if ($layer === 'Deployment') continue;

        foreach ($machine as $ip) {
            if (in_array($ip, $host_ips)) return $layer;
        }
    }

    return 'Not_Found';
}

function getRabbitMQChannel($type) { //return str to be use in rabbitmqclient and server instantiation
    global $ccag_machines;
    $types = array("backend", "dmz");
    if (!in_array(strtolower($type), $types)) {
        return "Wrong Type";
    }
    $detect_cluster = detectCluster();
    $cluster = str_contains($detect_cluster, 'Production') ? "Production": $detect_cluster ;
    switch($cluster) {
        case "Dev":
            if (strtolower($type) == "backend") return "BackEnd_Dev";
            if (strtolower($type) == "dmz") return "DMZ_Dev";
            break;
        case "QA":
            if (strtolower($type) == "backend") return "BackEnd_QA";
            if (strtolower($type) == "dmz") return "DMZ_QA";
            break;
        case "Production": //returns main by default if online, otherwise uses backup
            if (pulseCheck($ccag_machines['Production_Main']['BackEnd'])) {
                if (strtolower($type) == "backend") return "BackEnd_Prod_Main";
                if (strtolower($type) == "dmz") return "DMZ_Prod_Main";
            } else {
                if (strtolower($type) == "backend") return "BackEnd_Prod_BK";
                if (strtolower($type) == "dmz") return "DMZ_Prod_BK";
            }
            break;
        default:
            return "Not Found";
    }
}

//$test_detect = detectCluster();
//echo $test_detect.PHP_EOL;
//echo "Ping Test: " . (pulseCheck('192.168.193.78') ? 'Success' : 'Failed').PHP_EOL;

//echo getRabbitMQChannel("backend").PHP_EOL;

?>