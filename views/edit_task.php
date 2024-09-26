<?php
session_start();
include("db.php");

// Check if user is logged in and is an admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit();
}

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Handling GET request to fetch task data
if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['task_id'])) {
    $task_id = sanitize_input($_GET['task_id']);
    
    $stmt = $con->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();
    
    if ($task) {
        echo json_encode($task);
    } else {
        echo json_encode(["status" => "error", "message" => "Task not found"]);
    }
}

// Handling POST request to update task data
elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = sanitize_input($_POST['task_id']);
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $due_date = sanitize_input($_POST['due_date']);
    $status = sanitize_input($_POST['status']);
    $user_id = sanitize_input($_POST['user_id']);

    // Validate inputs (you can add more validation as needed)
    if(empty($task_id) || empty($title) || empty($description) || empty($due_date) || empty($status) || empty($user_id)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit();
    }

    // Update the task in the database
    $stmt = $con->prepare("UPDATE tasks SET user_id = ?, title = ?, description = ?, due_date = ?, status = ? WHERE id = ?");
    $stmt->bind_param("issssi", $user_id, $title, $description, $due_date, $status, $task_id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Task updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error updating task: " . $con->error]);
    }
}

// If neither GET nor POST with correct parameters
else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}

// Close the database connection
$con->close();
?>