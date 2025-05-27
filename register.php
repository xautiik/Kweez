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

try {
    // Check if email already exists
    $stmt = $con->prepare('SELECT email FROM "user" WHERE email = :email');
    $stmt->execute(['email' => $email]);
    if ($stmt->rowCount() > 0) {
        header("location:index.php?q7=Email Already Registered!!!");
        exit();
    }

    // Insert new user
    $insert_stmt = $con->prepare('INSERT INTO "user" (name, email, password) VALUES (:name, :email, :password)');
    $result = $insert_stmt->execute([
        'name' => $name,
        'email' => $email,
        'password' => $password_hash
    ]);

    if ($result) {
        session_start();
        $_SESSION["email"] = $email;
        $_SESSION["name"] = $name;

        header("location:profile.php?q=1");
    } else {
        header("location:index.php?q7=Registration Failed!");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

ob_end_flush();
?>
