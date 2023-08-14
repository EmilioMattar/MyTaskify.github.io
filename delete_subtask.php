<?php

$host = 'localhost';
$database = 'MyProject';
$username = 'root';
$dbPassword = '';


$connection = new mysqli($host, $username, $dbPassword, $database);


if ($connection->connect_error) {
    die('Connection failed: ' . $connection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subtaskId = $_POST['task'];

    $deleteStmt = $connection->prepare("DELETE FROM subtasks WHERE task = ?");
    $deleteStmt->bind_param("s", $subtaskId);

    if ($deleteStmt->execute()) {
        echo 'Subtask deleted from the database';
    } else {
        echo 'Error deleting subtask from the database';
    }

    $deleteStmt->close();
}

$connection->close();
?>