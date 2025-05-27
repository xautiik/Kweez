<?php
include_once 'dbConnection.php';

$ref = $_GET['q'] ?? 'index.php';
$email = $_POST['uname'] ?? '';
$password = $_POST['password'] ?? '';

try {
    $stmt = $con->prepare("SELECT email, password FROM admin WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        session_start();
        session_regenerate_id(true);

        $_SESSION["name"] = 'Admin';
        $_SESSION["key"] = 'kweez1234567890';
        $_SESSION["email"] = $email;

        header("Location: dashboard.php?q=0");
        exit();
    } else {
        header("Location: $ref?w=Warning : Access denied");
        exit();
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
