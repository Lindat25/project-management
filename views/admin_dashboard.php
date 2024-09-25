<?php
session_start();
include("db.php");

// Check if user is logged in and is an admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle task assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_task'])) {
    $user_id = $_POST['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $stmt = $con->prepare("INSERT INTO tasks (user_id, title, description, due_date, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $title, $description, $due_date, $status);
    
    if ($stmt->execute()) {
        $success_message = "Task assigned successfully!";
    } else {
        $error_message = "Error assigning task: " . $con->error;
    }
}

// Fetch all users
$users_result = $con->query("SELECT id, username FROM form WHERE role = 'user'");

// Fetch all tasks
$tasks_result = $con->query("SELECT t.*, f.username FROM tasks t JOIN form f ON t.user_id = f.id ORDER BY t.due_date ASC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Project Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
            overflow: hidden;
            padding: 0 20px;
        }
        header {
            background: #35424a;
            color: #ffffff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #e8491d 3px solid;
        }
        header a {
            color: #ffffff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 16px;
        }
        header ul {
            padding: 0;
            margin: 0;
            list-style: none;
        }
        header li {
            display: inline;
            padding: 0 20px 0 20px;
        }
        header #branding {
            float: left;
        }
        header #branding h1 {
            margin: 0;
        }
        header nav {
            float: right;
            margin-top: 10px;
        }
        .card {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        form input, form textarea, form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            background: #e8491d;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background: #333;
        }
        .message {
            padding: 10px;
            margin: 5px 0 15px 0;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1>Admin Dashboard</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#assign-task">Assign Task</a></li>
                    <li><a href="#all-tasks">All Tasks</a></li>
                    <li><a href="login.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="main">
        <div class="container">
            <?php
            if (isset($success_message)) {
                echo "<div class='message success'>$success_message</div>";
            }
            if (isset($error_message)) {
                echo "<div class='message error'>$error_message</div>";
            }
            ?>
            <div class="card" id="assign-task">
                <h2>Assign New Task</h2>
                <form id="assignTaskForm" method="POST">
                    <select name="user_id" required>
                        <option value="">Select User</option>
                        <?php
                        while ($user = $users_result->fetch_assoc()) {
                            echo "<option value='" . $user['id'] . "'>" . htmlspecialchars($user['username']) . "</option>";
                        }
                        ?>
                    </select>
                    <input type="text" name="title" placeholder="Task Title" required>
                    <textarea name="description" placeholder="Task Description" required></textarea>
                    <input type="date" name="due_date" required>
                    <select name="status">
                        <option value="Not Started">Not Started</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                    <button type="submit" name="assign_task" class="btn">Assign Task</button>
                </form>
            </div>

            <div class="card" id="all-tasks">
                <h2>All Users' Tasks</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Task Title</th>
                            <th>Description</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($task = $tasks_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($task['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($task['title']) . "</td>";
                            echo "<td>" . htmlspecialchars($task['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($task['due_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($task['status']) . "</td>";
                            echo "<td>
                                    <button class='btn' onclick='editTask(" . $task['id'] . ")'>Edit</button>
                                    <button class='btn' onclick='deleteTask(" . $task['id'] . ")'>Delete</button>
                                    
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function editTask(taskId) {
            // Implement edit functionality (you might want to open a modal or redirect to an edit page)
            alert('Editing task ' + taskId);
        }

        function deleteTask(taskId) {
            if(confirm('Are you sure you want to delete this task?')) {
                $.ajax({
                    url: 'delete_task.php',
                    method: 'POST',
                    data: { task_id: taskId },
                    success: function(response) {
                        if(response === 'success') {
                            alert('Task deleted successfully');
                            location.reload();
                        } else {
                            alert('Error deleting task');
                        }
                    }
                });
            }
        }

        $(document).ready(function() {
            $('#assignTaskForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'assign_task.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if(response === 'success') {
                            alert('Task assigned successfully');
                            location.reload();
                        } else {
                            alert('Error assigning task');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>