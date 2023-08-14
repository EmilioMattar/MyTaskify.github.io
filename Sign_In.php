<?php
session_start();

if (isset($_SESSION['email']) || (isset($_COOKIE['remember_me']) && !empty($_COOKIE['remember_me']))) {
    header('Location: http://localhost/EX3_MyProject/home_page.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stayConnected = isset($_POST['stay_connected']);
    $host = 'localhost';
    $database = 'MyProject';
    $username = 'root';
    $dbPassword = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            if ($password === $user['password']) {
       
                session_start();
                $_SESSION['email'] = $email;

                if ($stayConnected) {
                    setcookie('remember_me', $email, time() + (30 * 24 * 60 * 60), '/');
                }

                header("Location: http://localhost/EX3_MyProject/home_page.php");
                exit();
            } else {
                $error = "Invalid email or password. Please try again.";
            }
        } else {
            $error = "Invalid email or password. Please try again.";
        }
    } catch (PDOException $e) {
        $error = "An error occurred. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style.css">  
    <title>Sign In</title>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>

    <header>
        <nav>
            <ul> 
            <li><a href="<?php echo isset($_SESSION['email']) ? 'http://localhost/EX3_MyProject/home_page.php' : '#'; ?>" class="homeNav">Home</a></li>
            </ul>
        </nav>
    </header>
    <div class="login-container">
    <center> <h2 id="login-container">Login</h2></center>
        <form action="sign_in.php" method="POST">
            <div class="input-box">
                <label for="email">Username</label>
                <input type="email" id="email" name="email" value="<?php echo isset($error) ? '' : (isset($email) ? $email : ''); ?>" required>
            </div>
            <div class="input-box">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" value="" required>
            </div>
            <?php if (isset($error)) { echo '<p class="error">' . $error . '</p>'; } ?>
            <div class="input-box">
                <label for="stay_connected" class="stayconnected">Stay Connected</label>
                <br>
                <input type="checkbox" class="button1" id="stay_connected" name="stay_connected">
            </div>

            <center><button type="submit" >Submit</button></center>
        </form>
        <div class="signInPage">
            <a href="http://localhost/EX3_MyProject/Sign_Up.php">New to the site? Click here.</a>
        </div>
        <div class="signInPage">
            <a href="http://localhost/EX3_MyProject/forgot_password.php">Forgot password?</a>
        </div>
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