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
        min-height: 120px;
        border-bottom: #008a9a 3px solid;
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
        background: #3fa9f4;
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

    #editTaskModal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        z-index: 1000;
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

    @media (max-width: 768px) {
    .container {
        width: 95%;
        padding: 0 10px;
    }

    header #branding,
    header nav,
    header nav ul,
    header nav li {
        float: none;
        text-align: center;
        width: 100%;
    }

    header nav ul {
        padding-top: 20px;
    }

    .card {
        padding: 15px;
    }

    table, tbody, tr {
        display: block;
    }

    thead {
        display: none;
    }

    tr {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        padding: 8px;
        background-color: #f8f8f8;
    }

    td {
        display: block;
        text-align: right;
        padding-left: 50%;
        position: relative;
        border-bottom: 1px solid #eee;
    }

    td:last-child {
        border-bottom: none;
    }

    td:before {
        content: attr(data-label);
        position: absolute;
        left: 6px;
        width: 45%;
        padding-right: 10px;
        white-space: nowrap;
        text-align: left;
        font-weight: bold;
    }

    .btn {
        display: inline-block;
        width: auto;
        margin-right: 5px;
        margin-bottom: 5px;
    }

    #editTaskModal {
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
    }
}

    @media (max-width: 480px) {
        header {
            min-height: auto;
            padding-bottom: 20px;
        }

        form input, form textarea, form select {
            font-size: 16px;
        }
    }



    @media (max-width: 768px) {
    header {
        padding: 10px 0;
        min-height: auto;
    }

    header #branding,
    header nav {
        float: none;
        text-align: center;
        width: 100%;
    }

    header #branding h1 {
        margin-bottom: 10px;
        font-size: 24px;
    }

    header nav ul {
        padding: 0;
    }

    header nav li {
        display: block;
        margin-bottom: 5px;
    }

    header nav a {
        display: block;
        padding: 8px 0;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    header nav a:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }
}

@media (max-width: 480px) {
    header #branding h1 {
        font-size: 20px;
    }

    header nav a {
        font-size: 14px;
    }
}


</style>
</head>

<body>
    

    <div class="container">
    <header>
        <div id="branding">
            <h1>Admin Dashboard</h1>
        </div>
        <nav>
            <ul>
                <li><a href="#assign-task">Assign Task</a></li>
                <li><a href="#all-tasks">All Tasks</a></li>
                <li><a href="../index.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    </div>

    

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
    <div class="table-responsive">
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
                    echo "<td data-label='User'>" . htmlspecialchars($task['username']) . "</td>";
                    echo "<td data-label='Task Title'>" . htmlspecialchars($task['title']) . "</td>";
                    echo "<td data-label='Description'>" . htmlspecialchars($task['description']) . "</td>";
                    echo "<td data-label='Due Date'>" . htmlspecialchars($task['due_date']) . "</td>";
                    echo "<td data-label='Status'><span class='status status-" . strtolower(str_replace(' ', '-', $task['status'])) . "'>" . htmlspecialchars($task['status']) . "</span></td>";
                    echo "<td data-label='Actions'>
                            <button class='btn' onclick='viewNotes(" . $task['id'] . ")'>View Notes</button>
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
        </div>
    </section>

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
                $('#notesModal').show();
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




    function editTask(taskId) {
        $.ajax({
            url: 'edit_task.php',
            method: 'GET',
            data: { task_id: taskId },
            success: function(response) {
                const task = JSON.parse(response);
                // Populate a form with the task details
                let form = `
                    <form id="editTaskForm">
                        <input type="hidden" name="task_id" value="${task.id}">
                        <input type="text" name="title" value="${task.title}" required>
                        <textarea name="description" required>${task.description}</textarea>
                        <input type="date" name="due_date" value="${task.due_date}" required>
                        <select name="status">
                            <option value="Not Started" ${task.status === 'Not Started' ? 'selected' : ''}>Not Started</option>
                            <option value="In Progress" ${task.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                            <option value="Completed" ${task.status === 'Completed' ? 'selected' : ''}>Completed</option>
                        </select>
                        <select name="user_id" required>
                            ${getUserOptions(task.user_id)}
                        </select>
                        <button type="submit">Update Task</button>
                    </form>
                `;
                $('#editTaskModal').html(form).show();
            }
        });
    }


    function updateStatus(taskId, element) {
        const newStatus = element.value;
        
        $.ajax({
            url: 'update_status.php',
            method: 'POST',
            data: { task_id: taskId, status: newStatus },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    // Optionally, update the UI to reflect the change
                } else {
                    alert('Failed to update status: ' + response.message);
                    // Reset the dropdown to its previous value
                    $(element).val($(element).data('previous-value'));
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                alert('Error updating status. Please check the console for more details.');
                // Reset the dropdown to its previous value
                $(element).val($(element).data('previous-value'));
            }
        });
    }

    // Add this to preserve the previous value before change
    $(document).on('focus', '.status-dropdown', function() {
        $(this).data('previous-value', this.value);
    });







    function getUserOptions(selectedUserId) {
        let options = '';
        <?php
        $users_result->data_seek(0);
        while ($user = $users_result->fetch_assoc()) {
            echo "options += '<option value=\"{$user['id']}\" ' + ({$user['id']} == selectedUserId ? 'selected' : '') + '>{$user['username']}</option>';\n";
        }
        ?>
        return options;
    }

    function deleteTask(taskId) {
    if(confirm('Are you sure you want to delete this task?')) {
        $.ajax({
            url: 'delete_task.php',
            method: 'POST',
            data: { task_id: taskId },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    alert('Task deleted successfully');
                    location.reload();
                } else {
                    alert('Error deleting task: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                alert('Error deleting task. Please check the console for more details.');
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
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error assigning task: ' + response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    alert('Error assigning task. Please check the console for more details.');
                }
            });
        });

        $(document).on('submit', '#editTaskForm', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'edit_task.php',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    const result = JSON.parse(response);
                    if(result.status === 'success') {
                        alert(result.message);
                        location.reload();
                    } else {
                        alert('Error updating task: ' + result.message);
                    }
                }
            });
        });
    });
</script>

<div id="editTaskModal" style="display:none;"></div>

<div id="notesModal" style="display:none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%;">
        <span onclick="document.getElementById('notesModal').style.display='none'" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        <div id="notesContent"></div>
    </div>
</div>
</body>
</html>