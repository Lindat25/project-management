<?php
session_start();
include("db.php");

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $username = $_POST['username'];
    $password = $_POST['pass'];

    if(!empty($username) && !empty($password))
    {
        // Use prepared statement to prevent SQL injection
        $stmt = $con->prepare("SELECT id, pass, role FROM form WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0)
        {
            $user_data = $result->fetch_assoc();

            // TEMPORARY: Direct comparison for plain text passwords
            if($user_data['pass'] === $password)
            {
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['role'] = $user_data['role']; // Assuming you have a 'role' column

                // Redirect based on user role
                if($user_data['role'] == 'admin') {
                    header("Location: admin_dashboard.html");
                } else {
                    header("Location: dashboard.html");
                }
                exit;
            }
            
            // FUTURE: Use this block when passwords are hashed
            // if(password_verify($password, $user_data['pass']))
            // {
            //     $_SESSION['user_id'] = $user_data['id'];
            //     $_SESSION['role'] = $user_data['role'];
            //
            //     if($user_data['role'] == 'admin') {
            //         header("Location: admin_dashboard.php");
            //     } else {
            //         header("Location: user_dashboard.php");
            //     }
            //     exit;
            // }
        }

        echo "<script type='text/javascript'> alert('Wrong username or password')</script>";
    }
    else
    {
        echo "<script type='text/javascript'> alert('Please enter username and password')</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <form method="POST">
            <h1>Login</h1>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="pass" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;
            
            if (username.trim() === '' || password.trim() === '') {
                e.preventDefault();
                alert('Please enter both username and password.');
            }
        });
    </script>
</body>
</html>