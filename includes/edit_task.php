<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ?, status = ? WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$title, $description, $due_date, $status, $id, $user_id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update task']);
    }
}