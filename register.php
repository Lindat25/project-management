<?php
require_once 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validate input (add more validation as needed)
    if (empty($username) || empty($email) || empty($password)) {
        die("All fields are required");
    }
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$username, $email, $hashed_password]);
        echo "Registration successful. You can now <a href='views/login.html'>login</a>.";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "Username or email already exists.";
        } else {
            echo "Registration failed. Please try again.";
        }
    }
}
?>