<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, due_date, status) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $title, $description, $due_date, $status])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add task']);
    }
}