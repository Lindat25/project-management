<?php
session_start();
include("db.php");

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "error";
    exit();
}

// Check if the task ID is provided
if (isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    // Prepare and execute the DELETE query
    $stmt = $con->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $task_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error; // Log the specific database error
    }
} else {
    echo "error";
}
?>