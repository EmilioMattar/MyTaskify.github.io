<?php
session_start();

if (isset($_SESSION['email']) || (isset($_COOKIE['remember_me']) && !empty($_COOKIE['remember_me']))) {
  header('Location: http://localhost/EX3_MyProject/home_page.php');
  exit;
}

$errorMsg = "";
$errorMsgEmail = ""; 
$errorMsgPassword = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstname'];
    $lastName = $_POST['lastname'];
    $email = $_POST['email'];
    $userPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

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
            $errorMsgEmail = "Email already exists.";
        } elseif ($userPassword !== $confirmPassword) {
            $errorMsgPassword = "Passwords do not match. Please make sure the passwords match.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)");

            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $userPassword);

            $stmt->execute();

            header("Location: Sign_In.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Sign up</title>
  <link rel="stylesheet" type="text/css" href="Style.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    .error-message {
       position:relative;
       top:30px;
    }
    .cl1{
      margin-right: 150px;
    }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
$(document).ready(function() {
  function checkEmailAvailability(email) {
    $.ajax({
      type: 'POST',
      url: 'check_email.php',
      data: { email: email },
      success: function(response) {
        $('.email-error').text('');

        if (response === 'Invalid email') {
          $('.email-error').text(response);
          return;
        }

        if (response === 'Email already exists') {
          $('.email-error').text(response);
          return;
        }
      }
    });
  }

  $('#email').on('blur', function() {
    var email = $(this).val();

    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      $('.email-error').text('Invalid email');
      return;
    }

    checkEmailAvailability(email);
  });

  $('#confirm_password').on('blur', function() {
    var password = $('#password').val();
    var confirmPassword = $(this).val();

    if (password !== confirmPassword) {
      $('.confirm-password-error').text('Passwords do not match');
      $(this).addClass('error');
    } else {
      $('.confirm-password-error').text('');
      $(this).removeClass('error');
    }
  });
});



  </script>
</head>
<body>
  <header>
    <nav>
      <ul>
        <li><a href="<?php echo isset($_SESSION['email']) ? 'http://localhost/EX3_MyProject/home_page.php' : '#'; ?>" class="homeNav">Home</a></li>
      </ul>
    </nav>
  </header>

  <div>
    <center><h1 id="h1-Register">Register</h1></center><br>
    <form action="sign_up.php" method="POST">
    <div class="input-box">
      <label for="firstname">First Name</label>
      <input type="text" id="firstname" name="firstname" required>
    </div>
    <div class="input-box">
      <label for="lastname">Last Name</label>
      <input type="text" id="lastname" name="lastname" required>
    </div>
    <div class="input-box">
      <label for="email">Email</label>
      <div class="email-container">
        <input type="email" id="email" name="email" required>
        <div style="position: absolute; top: 100%; right: 0; font-size: 14px; color: red; margin-top: 5px; left: 3px;" class="email-error"><?php echo $errorMsgEmail; ?></div>
      </div>
    </div>

    <div class="input-box">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>

    <div class="input-box">
    <label for="confirm_password">Confirm Password</label>
    <div style="position: absolute; top: 100%; right: 0; font-size: 14px; color: red; margin-top: 5px; left: 3px;" class="confirm-password-error"><?php echo $errorMsgPassword; ?></div>
    <input type="password" id="confirm_password" name="confirm_password">
</div>
      <br>
      <center><button type="submit">Submit</button></center>
      <div class="signInPage">
        <a href="Sign_In.php">Already a user? Click here.</a>
      </div>
    </form>
  </div>

  <footer>
    <ul>
      <li><a href="#" id="footer1">About</a></li>
      <li><a href="#" id="footer2">Contact</a></li>
      <li style="margin-left: 50px;" id="footer3"><p>All rights reserved.</p></li>
    </ul>
  </footer>
</body>
</html>