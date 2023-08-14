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
} else {
    if (!isset($_SESSION['email']) && empty($_COOKIE['remember_me'])) {
        header('Location: http://localhost/EX3_MyProject/sign_in.php');
        exit;
    }
}

$email = $_SESSION['email'];

$host = 'localhost';
$database = 'MyProject';
$username = 'root';
$dbPassword = '';

$connection = new mysqli($host, $username, $dbPassword, $database);

$selectedTask = $_GET['task'];

$stmt = $connection->prepare("SELECT s.* FROM subtasks s INNER JOIN taskaccess t ON s.task_id = t.Task WHERE t.Task = ?");
$stmt->bind_param("s", $selectedTask);
$stmt->execute();

$result = $stmt->get_result();

$subtasks = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();

if ($connection->connect_error) {
    die('Connection failed: ' . $connection->connect_error);
}

$stmt = $connection->prepare("SELECT t.task, st.Date, st.Responsible, st.Completed FROM subtasks st INNER JOIN taskaccess t ON st.task_id = t.Task WHERE st.Responsible = ? AND t.task = ?");
$stmt->bind_param("ss", $email, $selectedTask);
$stmt->execute();

$result = $stmt->get_result();

$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subtaskTitle']) && isset($_POST['subtaskResponsible'])) {
    $title = $_POST['subtaskTitle'];
    $responsibleUser = $_POST['subtaskResponsible'];
    $selectedTask = $_POST['task_id']; 

    $stmt = $connection->prepare("SELECT COUNT(*) as count FROM subtasks WHERE task_id = ? AND task = ?");
    $stmt->bind_param("is", $selectedTask, $title);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $stmt->close();

    if ($count > 0) {
        echo '<script>alert("You cannot add a subtask with the same name as an existing subtask for the selected task.");</script>';
        echo '<script>window.location.href = "home_page.php";</script>';
    } else {
        $completed = 0; 
        $date = date('Y-m-d'); 

        $insertStmt = $connection->prepare("INSERT INTO subtasks (task, Date, Responsible, Completed, task_id) VALUES (?, ?, ?, ?, ?)");
        $insertStmt->bind_param("sssis", $title, $date, $responsibleUser, $completed, $selectedTask);
        $insertStmt->execute();
        $insertStmt->close();

        header('Location: '.$_SERVER['PHP_SELF'].'?task='.$selectedTask);
        exit;
    }
}

$stmt = $connection->prepare("SELECT Responsible FROM subtasks WHERE task_id = ?");
$stmt->bind_param("i", $selectedTask);
$stmt->execute();
$result = $stmt->get_result();
$responsibleUsers = array_unique(array_column($result->fetch_all(MYSQLI_ASSOC), 'Responsible'));
$stmt->close();

if (isset($_GET['task'])) {
    $selectedTask = $_GET['task'];
    $stmt = $connection->prepare("SELECT Users FROM taskaccess WHERE Task = ?");
    $stmt->bind_param("s", $selectedTask);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $responsibleUsers = $row['Users'];
    $responsibleUsersArray = explode(",", $responsibleUsers);
    $responsibleUsersArray = array_map('trim', $responsibleUsersArray);
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id']) && isset($_POST['completed'])) {
    $selectedTask = $_POST['task_id'];
    $completed = ($_POST['completed'] === 'true' || $_POST['completed'] === '1') ? 1 : 0;
    $stmt = $connection->prepare("UPDATE subtasks SET Completed = ? WHERE task = ?");
    $stmt->bind_param("is", $completed, $selectedTask);
    $stmt->execute();
    $stmt->close();
    exit();
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>SubTask</title>
    <style>
          body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; 
            min-height: 100vh; 
            margin: 0;
            font-family: Arial, sans-serif;
        }

   
        #subtaskForm {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 100px; 
            margin-bottom: 5px; 
        }

        #subtaskForm label {
            font-size: 20px;
        }

        #subtaskForm input,
        #subtaskForm select {
            margin-bottom: 10px;
            padding: 5px;
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        #subtaskForm button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        table {
            border-collapse: collapse;
            width: 80%;
            max-width: 800px;
            margin-top: -150px; 
            margin-bottom: 5px; 
        }

        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .completed {
            text-decoration: line-through;
            color: gray;
        }

        .delete-task-button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-task-button:hover {
            background-color: #d32f2f;
        }

        td.delete-cell {
            border: none;
            padding: 0;
            text-align: center;
        }
    </style>
    <script>
        function openPopup() {
            var width = 400;
            var height = 400;
            var left = (screen.width / 2) - (width / 2);
            var top = (screen.height / 2) - (height / 2);
            var features = 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top;
            window.open("subTask.php", "_blank", features);
        }
    </script>
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="http://localhost/EX3_MyProject/home_page.php" class="homeNav">Home</a></li>
            <li><a href="?signout=1" class="signOutNav">Sign Out</a></li>
        </ul>
    </nav>
</header>

<form id="subtaskForm" class="add-task-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($selectedTask); ?>">
    <label for="subtaskTitle">Subtask title:</label>
    <input type="text" id="subtaskTitle" name="subtaskTitle" required>
    <label for="subtaskResponsible">Responsible:</label>
    <select id="subtaskResponsible" name="subtaskResponsible">
    </select>
    <button type="submit" name="subtaskSubmit">Add Subtask</button>
</form>

<table>
    <br><br><br>
    <caption>Subtasks for <?php echo $selectedTask; ?></caption>
    <thead>
    <tr>
        <th>Subtask</th>
        <th>Date</th>
        <th>Responsible</th>
        <th>Completed</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($subtasks as $subtask) { ?>
    <tr>
        <td><?php echo $subtask['task']; ?></td>
        <td><?php echo $subtask['Date']; ?></td>
        <td><?php echo $subtask['Responsible']; ?></td>
        <td>
            <form class="task-form">
                <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($selectedTask); ?>">
                <input type="checkbox" data-task-id="<?php echo $subtask['task']; ?>" name="completed_<?php echo $subtask['task_id']; ?>" <?php if ($subtask['Completed'] == 1) echo 'checked'; ?>>
            </form>
        </td>
        <td class="delete-cell">
            <button class="delete-task-button" data-task-id="<?php echo $subtask['task']; ?>">Delete</button>
        </td>
    </tr>
<?php } ?>
    </tbody>
</table>

<br><br>
<button id="backButton" onclick="location.href='http://localhost/EX3_MyProject/home_page.php'">Main page</button>

<footer>
    <ul>
        <li><a href="#" id="footer1">About</a></li>
        <li><a href="#" id="footer2">Contact</a></li>
        <li style="margin-left: 50px;" id="footer3"><p>All rights reserved.</p></li>
    </ul>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
        $(document).ready(function() {
            $('input[type="checkbox"]').each(function() {
                var $taskRow = $(this).closest('tr');
                $taskRow.toggleClass('completed', this.checked);
            });

            function updateCheckboxState(taskId, completed) {
                $.ajax({
                    type: 'POST',
                    url: '', 
                    data: { task_id: taskId, completed: completed },
                    success: function(response) {
                        console.log('Checkbox state updated successfully');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating checkbox state: ' + error);
                    }
                });
            }

            $('input[type="checkbox"]').change(function() {
                var $taskRow = $(this).closest('tr');
                $taskRow.toggleClass('completed', this.checked);

                var taskId = $(this).data('task-id');
                var completed = this.checked ? 'true' : 'false';

                updateCheckboxState(taskId, completed);
            });

            $('.delete-task-button').click(function() {
                var taskId = $(this).data('task-id');

                var button = $(this);

                var confirmed = confirm("Are you sure you want to delete the subtask?");

                if (confirmed) {
                    $.ajax({
                        type: 'POST',
                        url: 'delete_subtask.php',
                        data: { task: taskId },
                        success: function(response) {
                            console.log('Subtask deleted successfully');

                            button.closest('tr').fadeOut(500, function() {
                                $(this).remove();
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting subtask: ' + error);
                        }
                    });
                }
            });

            var responsibleUsers = <?php echo json_encode($responsibleUsersArray); ?>;
            var responsibleSelect = $('#subtaskResponsible');
            responsibleSelect.empty();
            var ChooseUser = "Choose User";
            responsibleSelect.append('<option value="' + ChooseUser + '">' + ChooseUser + '</option>');
            for (var i = 0; i < responsibleUsers.length; i++) {
                responsibleSelect.append('<option value="' + responsibleUsers[i] + '">' + responsibleUsers[i] + '</option>');
            }

            var currentUserEmail = '<?php echo $email; ?>';

            responsibleSelect.val(ChooseUser);

            $('#subtaskForm').submit(function() {
                if (responsibleSelect.val() === ChooseUser) {
                    responsibleSelect.val(currentUserEmail);
                }
            });
        });
    </script>
</body>
</html>