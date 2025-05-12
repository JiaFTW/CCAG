<?php

$ccag_machines = parse_ini_file('ccag_machines', true); //Multidimenstional Array
//Usage: $ccag_machines[Dev][FrontEnd] Output >> 192.168.193.70


function pulseCheck($address) { //returns boolean (true if ping successful)
    $command = sprintf('ping -c 1 -W 1 %s > /dev/null 2>&1', escapeshellarg($ip));
    exec($command, $output, $exitCode);
    return $exitCode === 0;
}

?>