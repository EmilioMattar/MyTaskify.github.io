<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = "Invalid email or token.";
} elseif (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    if (verifyToken($email, $token)) {
        $message = "Please enter a new password below.";
    } else {
        $message = "Invalid email or token.";
    }
} else {
    $message = "Invalid email or token.";
}

function verifyToken($email, $token) {
    return true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style.css">  
    <title>Reset Password</title>
    <style>.center-message {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
</style>
</head>
<body>
<header>
        <nav>
            <ul> 
                <li><a href="http://localhost/EX3_MyProject/home_page.php" class="homeNav">Home</a></li>
            </ul>
        </nav>
    </header>
    <div class="center-message">
        <h2>You received an email to change your password, please follow the description.</h2>
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