<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $task_id = $_GET['task_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT * FROM notes WHERE task_id = ? AND user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$task_id, $user_id]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($notes);
}