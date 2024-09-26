<?php
session_start();
include("db.php");

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if task_id is provided
if(!isset($_GET['task_id'])) {
    echo json_encode(["status" => "error", "message" => "Task ID not provided"]);
    exit();
}

$task_id = intval($_GET['task_id']);

// Verify that the task belongs to the user or the user is an admin
$stmt = $con->prepare("SELECT id FROM tasks WHERE id = ? AND (user_id = ? OR ? IN (SELECT id FROM form WHERE role = 'admin'))");
$stmt->bind_param("iii", $task_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access to this task"]);
    exit();
}

// Fetch notes for the task
$stmt = $con->prepare("SELECT content, created_at FROM notes WHERE task_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();

$notes = [];
while ($row = $result->fetch_assoc()) {
    $notes[] = [
        'content' => $row['content'],
        'created_at' => $row['created_at']
    ];
}

echo json_encode(["status" => "success", "notes" => $notes]);

$stmt->close();
$con->close();
?>