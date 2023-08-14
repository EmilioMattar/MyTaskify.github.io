<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $host = 'localhost';
    $database = 'MyProject';
    $username = 'root';
    $dbPassword = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $emailCount = $stmt->fetchColumn();

        if ($emailCount > 0) {
            echo "Invalid email";
        } else {
            echo "";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>