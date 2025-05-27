<?php
include_once 'dbConnection.php';

$ref = @$_GET['q'];
$email = $_POST['uname'];
$password = $_POST['password'];

try {
    $stmt = $con->prepare("SELECT email FROM admin WHERE email = :email AND password = :password");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        session_start();
        if (isset($_SESSION['email'])) {
            session_unset();
        }
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
