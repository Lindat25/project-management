<?php
session_start();
include("db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit();
}

if(!isset($_GET['task_id'])) {
    echo json_encode(["status" => "error", "message" => "Task ID not provided"]);
    exit();
}

$task_id = intval($_GET['task_id']);

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