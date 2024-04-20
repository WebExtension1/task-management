<?php
require_once("includes/config.php");
session_start();

$userID = $_SESSION['user_id'];
$userQuery = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $userQuery->fetch_object();

if (isset($_GET['taskID'])) {
    $taskID = $_GET['taskID'];
    $query = $mysqli->query("SELECT * FROM tasks WHERE taskID = $taskID");
    $result = $query->fetch_object();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['create'])) {
        $query = $mysqli->prepare("INSERT INTO tasks (name, description, status) VALUES (?, ?, 'open')");
        $query->bind_param("ss", $_POST['taskName'], $_POST['taskDescription']);
        $query->execute();
    
        $taskID = mysqli_insert_id($mysqli);
    
        $query = $mysqli->prepare("INSERT INTO tasklisttasks (taskListID, taskID) VALUES (?, ?)");
        $query->bind_param("ss", $_GET['tasklist_id'], $taskID);
        $query->execute();

        $notificationMessage = "$user->firstname $user->surname created the task";
        $query = $mysqli->prepare("INSERT INTO notification (associatedTask, description) VALUES (?, ?)");
        $query->bind_param('is', $taskID , $notificationMessage);
        $query->execute();
    
        header("Location: index.php?task_id=$taskID");
    } else if (isset($_POST['update'])) {
        $taskID = $_GET['taskID'];
        $oldQuery = $mysqli->query("SELECT * FROM tasks WHERE taskID = $taskID");
        $oldQueryResult = $oldQuery->fetch_object();

        $query = $mysqli->prepare("UPDATE tasks SET name = ?, description = ? WHERE taskID = ?");
        $query->bind_param("sss", $_POST['taskName'], $_POST['taskDescription'], $taskID);
        $query->execute();

        $newQuery = $mysqli->query("SELECT * FROM tasks WHERE taskID = $taskID");
        $newQueryResult = $newQuery->fetch_object();

        if ($oldQueryResult->name != $newQueryResult->name) {
            $notificationMessage = "$user->firstname $user->surname has changed the task name from '$oldQueryResult->name' to '$newQueryResult->name'";
            $query = $mysqli->prepare("INSERT INTO notification (associatedTask, description) VALUES (?, ?)");
            $query->bind_param('is', $taskID , $notificationMessage);
            $query->execute();
        }

        if ($oldQueryResult->description != $newQueryResult->description) {
            $notificationMessage = "$user->firstname $user->surname has changed the task description from '$oldQueryResult->description' to '$newQueryResult->description'";
            $query = $mysqli->prepare("INSERT INTO notification (associatedTask, description) VALUES (?, ?)");
            $query->bind_param('is', $taskID , $notificationMessage);
            $query->execute();
        }

        echo $oldQueryResult->name . " " . $newQueryResult->name . " " . $oldQueryResult->description . " " . $newQueryResult->description;
    }
    header("Location: index.php?task_id=$taskID");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Task</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 792px)"/>
    <script src="js/main.js" defer></script>
</head>
<body>
    <?php include("includes/header.php") ?>
    <div class="info-container">
        <div class="info-box">
            <form method="post" class="new-task-form">
                <?php
                echo "
                <input class='new-task-name-box' type='text' placeholder='Task Name' name='taskName'" . (isset($result) ? "value='$result->name'" : "value='Hi'") . " required>
                <input class='new-task-description-box' type='text' placeholder='Task Description' name='taskDescription'" . (isset($result) ? "value='$result->description'" : "") . " required>
                ";
                ?>
                <?php
                if (!isset($_GET['taskID'])) {
                    echo "<button type='sumbit' class='create-new-task-button' name='create'>Create Task</button>";
                } else {
                    echo "<button type='sumbit' class='update-task-button' name='update'>Update Task</button>";
                }
                ?>
            </form>
        </div>
    </div>
</body>
</html>