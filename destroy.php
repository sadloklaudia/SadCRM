<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$path = 'SadCRM';

if (in_array(strtoupper(PHP_OS),[ 'WIN32', 'WINNT', 'WINDOWS'])) {
    exec(sprintf("rd /s /q %s", escapeshellarg($path)));
}
else {
    exec(sprintf("rm -rf %s", escapeshellarg($path)));
}

echo "$path removed";