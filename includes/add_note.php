<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = $_POST['task_id'];
    $content = $_POST['note_content'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO notes (task_id, user_id, content) VALUES (?, ?, ?)");
    if ($stmt->execute([$task_id, $user_id, $content])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add note']);
    }
}