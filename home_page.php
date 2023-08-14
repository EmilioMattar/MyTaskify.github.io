<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Style.css">
    <title>Home page</title>
    <style>
        .add-list-button {
            position: fixed;
            bottom: 38px;
            right: 20px;
            z-index: 1;
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 40px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .add-list-button:hover {
            background-color: #45a049;
            cursor: pointer;
        }

    </style>
    <script>
        function openPopup() {
            var width = 400;
            var height = 400;
            var left = (screen.width / 2) - (width / 2);
            var top = (screen.height / 2) - (height / 2);
            var features = 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top;
            window.open("new_list.php", "_blank", features);
        }
    </script>
</head>
<body>
    <?php
    session_start();

    if (!isset($_SESSION['email']) && (empty($_COOKIE['remember_me']) || !isset($_COOKIE['remember_me']))) {
        setcookie('remember_me', '', time() - 3600, '/');
        header('Location: http://localhost/EX3_MyProject/sign_in.php');
        exit;
    }

    if (isset($_GET['signout'])) {
        session_unset();
        session_destroy();
        setcookie('remember_me', '', time() - 3600, '/');
        header('Location: http://localhost/EX3_MyProject/sign_in.php');
        exit;
    }

    $host = 'localhost';
    $database = 'MyProject';
    $username = 'root';
    $dbPassword = '';

    $connection = new mysqli($host, $username, $dbPassword, $database);

    if ($connection->connect_error) {
        die('Connection failed: ' . $connection->connect_error);
    }

    $email = isset($_SESSION['email']) ? $_SESSION['email'] : ''; 

    $query = "SELECT Task, CreationDate, Users FROM taskaccess WHERE Users LIKE '%$email%'";
    $result = mysqli_query($connection, $query);
    ?>

    <header>
        <nav>
            <ul>
                <li><a href="home_page.php" class="homeNav">Home</a></li>
                <li><a href="?signout=1" class="signOutNav">Sign Out</a></li>
            </ul>
        </nav>
    </header>

    <table id="hometable">
        <caption>Task List</caption>
        <thead>
            <tr>
                <th>Task</th>
                <th>Creation Date</th>
                <th>Users with access</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $task = $row['Task'];
                    $creationDate = $row['CreationDate'];
                    $users = $row['Users'];

                    echo '<tr>';
                    echo '<td><a href="Task.php?task=' . urlencode($task) . '" class="tasks">' . $task . '</a></td>';
                    echo '<td>' . $creationDate . '</td>';
                    echo '<td>' . $users . '</td>';
                    echo '</tr>';
                }


                mysqli_free_result($result);
            } else {
                echo "Error: " . mysqli_error($connection);
            }

            mysqli_close($connection);
            ?>
        </tbody>
    </table>

    <button class="add-list-button" onclick="openPopup()">
        Add New List
    </button>

    <footer>
        <ul>
            <li><a href="#" id="footer1">About</a></li>
            <li><a href="#" id="footer2">Contact</a></li>
            <li style="margin-left: 50px;" id="footer3"><p>All rights reserved.</p></li>
        </ul>
    </footer>

    <?php
    if (isset($_GET['new_list_added'])) {
        echo '<script>window.location.reload();</script>';
    }
    ?>
</body>
</html>