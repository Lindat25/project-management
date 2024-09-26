<?php
session_start();
include("db.php");

// Check if user is logged in and is an admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit();
}

// Check if task_id is provided
if(!isset($_POST['task_id'])) {
    echo json_encode(["status" => "error", "message" => "No task ID provided"]);
    exit();
}

$task_id = intval($_POST['task_id']);

// Prepare and execute the delete query
$stmt = $con->prepare("DELETE FROM tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Task deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No task found with the given ID"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Error deleting task: " . $con->error]);
}

$stmt->close();
$con->close();
?>