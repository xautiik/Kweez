<?php
session_start();

if (isset($_SESSION["email"])) {
    session_destroy();
}

include_once 'dbConnection.php'; 

$ref = $_GET['q'] ?? 'index.php'; 
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

try {
    $stmt = $con->prepare('SELECT name, password FROM "user" WHERE email = :email');
    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION["name"] = $user['name'];
        $_SESSION["email"] = $email;
        header("location: profile.php?q=1");
        exit();
    } else {
        header("location: $ref?w=Wrong Username or Password");
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
