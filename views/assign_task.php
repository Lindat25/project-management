<?php
session_start();
include("db.php");

// Check if user is logged in and is an admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit();
}

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = sanitize_input($_POST['user_id']);
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $due_date = sanitize_input($_POST['due_date']);
    $status = sanitize_input($_POST['status']);

    // Validate inputs
    if(empty($user_id) || empty($title) || empty($description) || empty($due_date) || empty($status)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit();
    }

    // Insert the new task into the database
    $stmt = $con->prepare("INSERT INTO tasks (user_id, title, description, due_date, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $title, $description, $due_date, $status);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Task assigned successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error assigning task: " . $con->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$con->close();
?>