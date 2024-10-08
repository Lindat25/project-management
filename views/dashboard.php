<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db_connect.php';

// Fetch user information
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management Dashboard</title>
    <style>
        /* CSS styles */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
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
            list-style: none;
        }
        header li {
            display: inline;
            padding: 0 20px 0 20px;
        }
        .tasks ul {
            list-style-type: none;
            padding: 0;
        }
        .tasks li {
            background: #fff;
            margin-bottom: 10px;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .add-task form, .editTaskForm {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        input[type="text"], input[type="date"], textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            display: inline-block;
            background: #e8491d;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #333;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.8em;
        }
        .status-not-started { background-color: #ffd700; }
        .status-in-progress { background-color: #1e90ff; color: white; }
        .status-completed { background-color: #32cd32; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <nav>
                <ul>
                    <li><a href="#tasks">My Tasks</a></li>
                    <li><a href="#add-task">Add Task</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section id="tasks" class="tasks">
                <h2>Your Tasks</h2>
                <ul id="taskList">
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC");
                    $stmt->execute([$_SESSION['user_id']]);
                    while ($task = $stmt->fetch()) {
                        echo "<li data-task-id='" . $task['id'] . "'>";
                        echo "<h3>" . htmlspecialchars($task['title']) . "</h3>";
                        echo "<p>Description: " . htmlspecialchars($task['description']) . "</p>";
                        echo "<p>Due Date: " . htmlspecialchars($task['due_date']) . "</p>";
                        echo "<p>Status: <span class='status'>" . htmlspecialchars($task['status']) . "</span></p>";
                        echo "<button onclick='editTask(" . $task['id'] . ")'>Edit</button>";
                        echo "<button onclick='deleteTask(" . $task['id'] . ")'>Delete</button>";
                        echo "</li>";
                    }
                    ?>
                </ul>
            </section>

            <section id="add-task" class="add-task">
                <h2>Add New Task</h2>
                <form id="addTaskForm">
                    <input type="text" name="title" placeholder="Task Title" required>
                    <textarea name="description" placeholder="Task Description" required></textarea>
                    <input type="date" name="due_date" required>
                    <select name="status">
                        <option value="Not Started">Not Started</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                    <button type="submit">Add Task</button>
                </form>
            </section>

            <section id="task-notes" class="task-notes" style="display:none;">
                <h2>Task Notes</h2>
                <div id="notesContent"></div>
                <form id="addNoteForm">
                    <input type="hidden" name="task_id" id="noteTaskId">
                    <textarea name="note_content" placeholder="Add a note" required></textarea>
                    <button type="submit">Add Note</button>
                </form>
            </section>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="dashboard.js"></script>
</body>
</html>