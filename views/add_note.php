<?php
session_start();
include("db.php");

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the required fields are set
if(!isset($_POST['task_id']) || !isset($_POST['note_content'])) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit();
}

$task_id = intval($_POST['task_id']);
$note_content = trim($_POST['note_content']);

// Validate input
if(empty($note_content)) {
    echo json_encode(["status" => "error", "message" => "Note content cannot be empty"]);
    exit();
}

// Check if the task belongs to the user
$stmt = $con->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Task not found or doesn't belong to the user"]);
    exit();
}

// Insert the note
$stmt = $con->prepare("INSERT INTO notes (task_id, user_id, content) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $task_id, $user_id, $note_content);

if($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Note added successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to add note: " . $con->error]);
}

$stmt->close();
$con->close();
?>