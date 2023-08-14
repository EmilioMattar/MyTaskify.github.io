<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = $_POST['title'];
    $selectedUsers = $_POST['users'];

    $host = 'localhost';
    $database = 'MyProject';
    $username = 'root';
    $dbPassword = '';

    $connection = new mysqli($host, $username, $dbPassword, $database);

   
    if ($connection->connect_error) {
        die('Connection failed: ' . $connection->connect_error);
    }

   
    $query = "SELECT Users FROM taskaccess";
    $result = $connection->query($query);
    $userEmails = array();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users = $row['Users'];
            $emails = explode(",", $users);
            foreach ($emails as $email) {
                $email = trim($email);
                if (!empty($email)) {
                    $userEmails[] = $email;
                }
            }
        }
    }

   
    $insertQuery = "INSERT INTO taskaccess (Task, CreationDate, Users) VALUES ('$title', CURDATE(), '$selectedUsers')";
    if ($connection->query($insertQuery) === TRUE) {
       
        echo '<script>window.opener.location.reload();</script>';
        echo '<script>window.close();</script>';
    } else {
       
        echo 'Error: ' . $insertQuery . '<br>' . $connection->error;
    }


    $connection->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New List</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('photos/P.jpg'); 
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.7); 
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .container h1 {
            text-align: center;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }
        .form-group label {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .form-group input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-group input[type="submit"] {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>New List</h1>
        <form action="new_list.php" method="POST">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="users">Users:</label>
                <input type="text" id="users" name="users" required list="userEmailsList">
                <datalist id="userEmailsList">
                    <?php
                  
                    $host = 'localhost';
                    $database = 'MyProject';
                    $username = 'root';
                    $dbPassword = '';

                    $connection = new mysqli($host, $username, $dbPassword, $database);

                    if ($connection->connect_error) {
                        die('Connection failed: ' . $connection->connect_error);
                    }

                    $query = "SELECT email FROM users";
                    $result = $connection->query($query);

                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            $email = $row['email'];
                            echo '<option value="' . htmlspecialchars($email) . '">';
                        }

                        mysqli_free_result($result);
                    } else {
                        echo "Error: " . mysqli_error($connection);
                    }

                    $connection->close();
                    ?>
                </datalist>
            </div>


            <div class="form-group">
                <input type="submit" value="Create List">
            </div>
        </form>
    </div>
</body>

<script>
    var usersInput = document.getElementById('users');
    var datalist = document.getElementById('userEmailsList');

    usersInput.addEventListener('input', function() {
        var value = this.value.toLowerCase();
        datalist.innerHTML = '';

        if (value.length >= 2) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_emails.php?query=' + encodeURIComponent(value), true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var emails = JSON.parse(xhr.responseText);
                    emails.forEach(function(email) {
                        var option = document.createElement('option');
                        option.value = email;
                        datalist.appendChild(option);
                    });
                }
            };
            xhr.send();
        }
    });
</script>

</html>