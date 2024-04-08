<?php
require_once("includes/config.php");
session_start();

$userID = 0;

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $query = $mysqli->prepare("INSERT INTO tasks (name, description, status) VALUES (?, ?, 'open')");
    $query->bind_param("ss", $_POST['taskName'], $_POST['taskDescription']);
    $query->execute();

    $taskID = mysqli_insert_id($mysqli);

    $query = $mysqli->prepare("INSERT INTO tasklisttasks (taskListID, taskID) VALUES (?, ?)");
    $query->bind_param("ss", $_GET['tasklist_id'], $taskID);
    $query->execute();

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
                <input class="new-task-name-box" type="text" placeholder="Task Name" name="taskName" required>
                <input class="new-task-description-box" type="text" placeholder="Task Description" name="taskDescription" required>
                <?php
                if (!isset($_GET['taskID'])) {
                    echo "<button type='sumbit' class='create-new-task-button' name='create'>Create Task</button>";
                } else {
                    echo "<button type='sumbit' class='update-new-tasklist-button' name='update'>Update Task</button>";
                }
                ?>
            </form>
        </div>
    </div>
</body>
</html>