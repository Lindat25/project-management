<?php
require_once 'includes/db_connect.php';

try {
    $stmt = $pdo->query("SELECT 1");
    echo "Database connection successful!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>