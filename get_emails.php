<?php
$host = 'localhost';
$database = 'MyProject';
$username = 'root';
$dbPassword = '';

$connection = new mysqli($host, $username, $dbPassword, $database);

if ($connection->connect_error) {
    die('Connection failed: ' . $connection->connect_error);
}

$query = "SELECT email FROM users WHERE email LIKE '%" . $_GET['query'] . "%'";
$result = $connection->query($query);

$userEmails = array();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $userEmails[] = $row['email'];
    }
}

$connection->close();

header('Content-Type: application/json');
echo json_encode($userEmails);