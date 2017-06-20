<?php

require_once __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('UTC');
$debug = false;

// Command that starts the built-in web server
$command = sprintf(
    'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
    WEB_SERVER_HOST,
    WEB_SERVER_PORT,
    WEB_SERVER_DOCROOT
);

// Execute the command and store the process ID
$output = [];
exec($command, $output);
$pid = (int)$output[0];

if ($debug) {
    echo sprintf(
        '%s - Web server started on %s:%d with PID %d',
        date('r'),
        WEB_SERVER_HOST,
        WEB_SERVER_PORT,
        $pid
    ) . PHP_EOL;
}


// Kill the web server when the process ends
register_shutdown_function(
    function () use ($pid, $debug) {
        if ($debug) {
            echo sprintf('%s - Killing process with ID %d', date('r'), $pid) .
                PHP_EOL;
        }
        exec('kill ' . $pid);
    }
);
