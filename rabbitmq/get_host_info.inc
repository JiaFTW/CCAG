<?php
function getHostInfo($params)
{
    $hostInfo = [
        "testServer" => [
            "BROKER_HOST" => "192.168.191.18", // Replace with your RabbitMQ server IP
            "BROKER_PORT" => 5672,             // Default RabbitMQ port
            "USER" => "ccag",                  // RabbitMQ username
            "PASSWORD" => "ccag",              // RabbitMQ password
            "VHOST" => "ccagHost",             // RabbitMQ virtual host
            "EXCHANGE" => "clientExchange",    // Exchange name
            "QUEUE" => "clientQueue",          // Queue name
            "EXCHANGE_TYPE" => "topic",        // Exchange type
            "AUTO_DELETE" => false             // Auto-delete setting
        ]
    ];

    return $hostInfo;
}
?>
