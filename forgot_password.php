<?php
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];


    $host = 'localhost';
    $database = 'MyProject';
    $username = 'root';
    $dbPassword = '';
    $connection = new mysqli($host, $username, $dbPassword, $database);


    if (emailExists($connection, $email)) {
     
        $token = generateToken();
       
        storeToken($connection, $email, $token);

        header("Location: reset_password.php?email=" . urlencode($email) . "&token=" . urlencode($token));
        exit();
    } else {
        $message = "Email does not exist."; 
    }

    $connection->close();
}

function generateToken() {

    $token = bin2hex(random_bytes(32));
    return $token;
}

function storeToken($connection, $email, $token) {

    $encryptedToken = encryptToken($token);

    
    $query = "UPDATE users SET token = ? WHERE email = ?";
    $statement = $connection->prepare($query);
    $statement->bind_param("ss", $encryptedToken, $email);
    $statement->execute();
    $statement->close();
}

function encryptToken($token) {
   
    $hashedToken = password_hash($token, PASSWORD_DEFAULT);
    return $hashedToken;
}

function emailExists($connection, $email) {
  
    $email = $connection->real_escape_string($email);
    $query = "SELECT COUNT(*) FROM users WHERE email = '$email'";
    $result = $connection->query($query);
    $count = $result->fetch_row()[0];
    return $count > 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style.css">  
    <title>Forgot Password</title>
</head>
<body>
    <header>
        <nav>
            <ul> 
            <li><a href="http://localhost/EX3_MyProject/home_page.php" class="homeNav">Home</a></li>
            </ul>
        </nav>
    </header>
    <div class="login-container">
        <center> <h2 id="login-container">Forgot Password</h2></center>
        <form method="POST">
            <div class="input-box">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <center><button type="submit">Submit</button></center>
        </form>
        <div id="message"><?php echo $message; ?></div> 
    </div>

    <footer>
        <ul>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
            <li style="margin-left: 50px;"><p>All rights reserved.</p></li>
        </ul>
    </footer>
</body>
</html>