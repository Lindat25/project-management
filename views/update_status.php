<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
$new_status = isset($_POST['status']) ? $_POST['status'] : '';

if (!$task_id || !$new_status) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid task ID or status']);
    exit();
}

// If user is not an admin, verify that the task belongs to the user
if (!$is_admin) {
    $stmt = $con->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Task not found or access denied']);
        exit();
    }
}

// Update the task status
$stmt = $con->prepare("UPDATE tasks SET status = ? WHERE id = ?");
$stmt->bind_param("si", $new_status, $task_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Status updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update status: ' . $con->error]);
}

$stmt->close();
$con->close();
?>