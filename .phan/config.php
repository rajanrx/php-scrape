<?php

$config = require(__DIR__ . '/config.critical.php');

// Remove the severity (which would be critical only) back to the Phan default.
unset($config['minimum_severity']);

return $config;
