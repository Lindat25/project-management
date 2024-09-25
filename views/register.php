<?php
session_start();
include("db.php");

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $username = $_POST['username'];
    $gmail = $_POST['email'];
    $password = $_POST['pass'];
    $role = $_POST['role'];

    if(!empty($gmail) && !empty($password) && !is_numeric($gmail))
    {
        // Hash the password
        $hashed_password = password_hash($_POST['pass'], PASSWORD_DEFAULT);

        // Use prepared statement
        $stmt = $con->prepare("INSERT INTO form (username, email, pass, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $gmail, $hashed_password, $role);

        if($stmt->execute())
        {
            echo "<script type='text/javascript'> alert('Successfully Registered'); window.location.href = 'login.php';</script>";
        }
        else
        {
            echo "<script type='text/javascript'> alert('Registration failed: " . $stmt->error . "')</script>";
        }
        $stmt->close();
    }
    else 
    {
        echo "<script type='text/javascript'> alert('Please enter valid information')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <form method="POST">
            <h1>Register</h1>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="pass" required>
            
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
    <script src="register.js"></script>
</body>
</html>