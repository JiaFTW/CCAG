<?php

$ccag_machines = parse_ini_file('ccag_machines.ini', true); //Multidimenstional Array Usage: $ccag_machines[Dev][FrontEnd] Output >> 192.168.193.70

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
            if (in_array($ip, $host_ips)) {
                return $layer;
            }
        }
    }

    return 'Not_Found';
}

$test_detect = detectCluster();
echo $test_detect.PHP_EOL;
echo "Ping Test: " . (pulseCheck('192.168.193.69') ? 'Success' : 'Failed').PHP_EOL;

?>