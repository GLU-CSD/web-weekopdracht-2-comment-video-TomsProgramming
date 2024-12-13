<?php
session_start();

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "youtube";

$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if ($con -> connect_errno) {
    echo "Failed to connect to MySQL: " . $con -> connect_error;
    exit();
}

$encryptionOptions = [
    'memory_cost' => 1<<15,
    'time_cost' => 4,
    'threads' => 1
];

function prettyDump ( $var ) {
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}