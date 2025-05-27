<?php
session_start(); // Always start session first

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
        header("Location: dashboard.php?q=0"); // Redirect to dashboard
        exit();
    } else {
        header("Location: $ref?w=Wrong Username or Password");
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
