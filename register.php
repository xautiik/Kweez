<?php
include_once 'dbConnection.php'; 
ob_start();

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$name = ucwords(strtolower(trim($name)));

$email = trim($email);
$email = filter_var($email, FILTER_VALIDATE_EMAIL);
if (!$email) {
    header("location:index.php?q7=Invalid Email!!!");
    exit();
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$email_check = pg_query_params($con, "SELECT email FROM user WHERE email = $1", [$email]);
if (!$email_check) {
    die("Database error: " . pg_last_error());
}

if (pg_num_rows($email_check) > 0) {
    header("location:index.php?q7=Email Already Registered!!!");
    exit();
}

$insert_query = "INSERT INTO user (name, email, password) VALUES ($1, $2, $3)";
$result = pg_query_params($con, $insert_query, [$name, $email, $password_hash]);

if ($result) {
    session_start();
    $_SESSION["email"] = $email;
    $_SESSION["name"] = $name;

    header("location:profile.php?q=1");
} else {
    header("location:index.php?q7=Registration Failed!");
}

ob_end_flush();
?>
