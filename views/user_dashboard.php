<?php
session_start();
include("db.php");

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user information
$user_id = $_SESSION['user_id'];
$stmt = $con->prepare("SELECT username, role FROM form WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If user not found, redirect to login
if(!$user) {
    header("Location: login.php");
    exit();
}

$username = htmlspecialchars($user['username']);
$is_admin = ($user['role'] === 'admin');

// Handle task addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_task'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $stmt = $con->prepare("INSERT INTO tasks (user_id, title, description, due_date, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $title, $description, $due_date, $status);
    
    if ($stmt->execute()) {
        $success_message = "Task added successfully!";
    } else {
        $error_message = "Error adding task: " . $con->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_admin ? 'Admin' : 'User'; ?> Dashboard - Project Management</title>
    <style>
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
        input[type="text"], textarea, select {
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
        .task-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.8em;
            cursor: pointer;
        }
        .status-not-started { background-color: #ffd700; }
        .status-in-progress { background-color: #1e90ff; color: white; }
        .status-completed { background-color: #32cd32; color: white; }
        @media (max-width: 768px) {
            .container { width: 95%; }
        }
        
        .task-form {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .status-dropdown {
            padding: 5px;
            border-radius: 3px;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome, <?php echo $username; ?>!</h1>
            <nav>
                <ul>
                    <li><a href="#tasks">My Tasks</a></li>
                    <li><a href="#add-task">Add Task</a></li>
                    <?php if ($is_admin): ?>
                        <li><a href="#all-tasks">All Users' Tasks</a></li>
                    <?php endif; ?>
                    <li><a href="login.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section id="add-task" class="task-form">
                <h2>Add New Task</h2>
                <form method="POST">
                    <input type="text" name="title" placeholder="Task Title" required>
                    <textarea name="description" placeholder="Task Description" required></textarea>
                    <input type="date" name="due_date" required>
                    <select name="status" required>
                        <option value="Not Started">Not Started</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                    <button type="submit" name="add_task">Add Task</button>
                </form>
            </section>

            <section id="tasks" class="tasks">
                <h2>Your Tasks</h2>
                <ul id="taskList">
                    <?php
                    $stmt = $con->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC");
                    if ($stmt) {
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while ($task = $result->fetch_assoc()) {
                                echo "<li data-task-id='" . $task['id'] . "'>";
                                echo "<h3>" . htmlspecialchars($task['title']) . "</h3>";
                                echo "<p>Description: " . htmlspecialchars($task['description']) . "</p>";
                                echo "<p>Due Date: " . htmlspecialchars($task['due_date']) . "</p>";
                                echo "<p>Status: <select class='status-dropdown' onchange='updateStatus(" . $task['id'] . ", this)'>";
                                $statuses = ['Not Started', 'In Progress', 'Completed'];
                                foreach ($statuses as $status) {
                                    $selected = ($status == $task['status']) ? 'selected' : '';
                                    echo "<option value='$status' $selected>$status</option>";
                                }
                                echo "</select></p>";
                                echo "<button onclick='viewNotes(" . $task['id'] . ")'>View Notes</button>";
                                echo "</li>";
                            }
                        } else {
                            echo "<li>No tasks yet.</li>";
                        }
                    } else {
                        echo "<li>Error: Unable to fetch tasks.</li>";
                    }
                    ?>
                </ul>
            </section>

            <?php if ($is_admin): ?>
            <section id="all-tasks" class="tasks">
                <h2>All Users' Tasks</h2>
                <ul>
                    <?php
                    $all_tasks_query = "SELECT t.*, f.username FROM tasks t JOIN form f ON t.user_id = f.id ORDER BY t.due_date ASC";
                    $all_tasks_result = $con->query($all_tasks_query);
                    
                    if ($all_tasks_result->num_rows > 0) {
                        while ($task = $all_tasks_result->fetch_assoc()) {
                            echo "<li>";
                            echo "<h3>" . htmlspecialchars($task['title']) . " (by " . htmlspecialchars($task['username']) . ")</h3>";
                            echo "<p>Description: " . htmlspecialchars($task['description']) . "</p>";
                            echo "<p>Due Date: " . htmlspecialchars($task['due_date']) . "</p>";
                            echo "<p>Status: " . htmlspecialchars($task['status']) . "</p>";
                            echo "</li>";
                        }
                    } else {
                        echo "<li>No tasks found.</li>";
                    }
                    ?>
                </ul>
            </section>
            <?php endif; ?>

            <section id="task-notes" class="task-notes">
                <h2>Task Notes</h2>
                <div id="notesContent"></div>
                <form id="addNoteForm">
                    <select name="task_id" id="noteTaskId" required>
                        <option value="">Select a Task</option>
                        <?php
                        $stmt = $con->prepare("SELECT id, title FROM tasks WHERE user_id = ?");
                        if ($stmt) {
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($task = $result->fetch_assoc()) {
                                echo "<option value='" . $task['id'] . "'>" . htmlspecialchars($task['title']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                    <textarea name="note_content" placeholder="Add a note" required></textarea>
                    <button type="submit">Add Note</button>
                </form>
            </section>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function viewNotes(taskId) {
            $.ajax({
                url: 'get_notes.php',
                method: 'GET',
                data: { task_id: taskId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        let notesHtml = '<h3>Notes for Task</h3>';
                        if (response.notes.length > 0) {
                            notesHtml += '<ul>';
                            response.notes.forEach(function(note) {
                                notesHtml += `<li>${note.content} <small>(${note.created_at})</small></li>`;
                            });
                            notesHtml += '</ul>';
                        } else {
                            notesHtml += '<p>No notes for this task yet.</p>';
                        }
                        $('#notesContent').html(notesHtml);
                        $('#task-notes').show();
                    } else {
                        alert('Error fetching notes: ' + response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    alert('Error fetching notes. Please check the console for more details.');
                }
            });
        }

        function updateStatus(taskId, element) {
            const newStatus = element.value;
            
            $.ajax({
                url: 'update_status.php',
                method: 'POST',
                data: { task_id: taskId, status: newStatus },
                success: function(response) {
                    if (response === 'success') {
                        alert('Status updated successfully');
                    } else {
                        alert('Failed to update status');
                    }
                }
            });
        }

        $(document).ready(function() {
            $('#addNoteForm').submit(function(e) {
                e.preventDefault();
                var taskId = $('#noteTaskId').val();
                $.ajax({
                    url: 'add_note.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            viewNotes(taskId);
                            $('#addNoteForm')[0].reset();
                        } else {
                            alert('Error adding note: ' + response.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX error:', textStatus, errorThrown);
                        alert('Error adding note. Please check the console for more details.');
                    }
                });
            });
        });
    </script>
</body>
</html>