<?php
session_start();

if (isset($_SESSION["email"])) {
    session_destroy();
}

include_once 'dbConnection.php'; 
$ref = @$_GET['q'];
$email = trim($_POST['email']);
$password = $_POST['password'];

$conn = pg_connect("host=localhost dbname=your_db user=your_user password=your_password");
if (!$conn) {
    die("Error in connection: " . pg_last_error());
}

$result = pg_query_params($conn, "SELECT name, password FROM user WHERE email = $1", array($email));

if (pg_num_rows($result) == 1) {
    $row = pg_fetch_assoc($result);
    if (password_verify($password, $row['password'])) {
        $_SESSION["name"] = $row['name'];
        $_SESSION["email"] = $email;
        header("location: profile.php?q=1");
        exit();
    } else {
        header("location: $ref?w=Wrong Username or Password");
        exit();
    }
} else {
    header("location: $ref?w=Wrong Username or Password");
    exit();
}

pg_close($conn);
?>
