<?php
$con = new mysqli(
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASSWORD'),
    getenv('DB_NAME')
);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>
