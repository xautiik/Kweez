<?php
try {
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '5432';
    $dbname = getenv('DB_NAME') ?: 'your_db';
    $user = getenv('DB_USER') ?: 'your_user';
    $password = getenv('DB_PASSWORD') ?: 'your_password';

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

    $con = new PDO($dsn, $user, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
