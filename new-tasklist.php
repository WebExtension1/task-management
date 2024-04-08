<?php
require_once("includes/config.php");
session_start();

$userID = $_SESSION['user_id'];

if (isset($_GET['tasklistID'])) {
    $taskListID = $_GET['tasklistID'];
    $query = $mysqli->query("SELECT * FROM tasklistaccess WHERE taskListID = $taskListID AND userID = $userID");
    $result = $query->fetch_object();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['create'])) {
        $query = $mysqli->prepare("INSERT INTO tasklists (name) VALUES (?);");
        $query->bind_param('s', $_POST['taskListName']);
        $query->execute();
    
        $colour = $_POST['option'];
        $userID = $_SESSION["user_id"];
        $taskListID = mysqli_insert_id($mysqli);
    
        $query = $mysqli->prepare("INSERT INTO tasklistaccess (userID, taskListID, colour, owner) VALUES (?, ?, ?, 1);");
        $query->bind_param('sss', $userID, $taskListID, $colour);
        $query->execute();
    } else {
        $query = $mysqli->prepare("UPDATE tasklists SET name = ? WHERE tasklistID = ?");
        $query->bind_param('ss', $_POST['taskListName'], $_GET['tasklistID']);
        $query->execute();

        $query = $mysqli->prepare("UPDATE tasklistaccess SET colour = ? WHERE tasklistID = ?");
        $query->bind_param('ss', $_POST['option'], $_GET['tasklistID']);
        $query->execute();
    }
    header("Location: index.php?task_id=$taskID");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Tasklist</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 792px)"/>
    <script src="js/main.js" defer></script>
</head>
<body>
    <?php include("includes/header.php") ?>
    <div class="info-container">
        <div class="info-box">
            <form method="post" class="new-tasklist-form">
                <input class="new-tasklist-name-box" type="text" placeholder="Task List Name" name="taskListName" required>
                <div class="colour-checkboxes">
                    <label for="white">White</label><input type="radio" name="option" id="white" value="White" onclick="document.getElementById('colour').innerHTML = 'White'" <?php if (isset($result)) { if ($result->colour == "white") { echo "checked"; } } ?>>
                    <label for="blue">Blue</label><input type="radio" name="option" id="blue" value="Blue" onclick="document.getElementById('colour').innerHTML = 'Blue'" <?php if (isset($result)) { if ($result->colour == "blue") { echo "checked"; } } ?>>
                    <label for="red">Red</label><input type="radio" name="option" id="red" value="Red" onclick="document.getElementById('colour').innerHTML = 'Red'" <?php if (isset($result)) { if ($result->colour == "red") { echo "checked"; } } ?>>
                    <label for="green">Green</label><input type="radio" name="option" id="green" value="Green" onclick="document.getElementById('colour').innerHTML = 'Green'" <?php if (isset($result)) { if ($result->colour == "green") { echo "checked"; } } ?>>
                </div>
                <?php
                if (!isset($_GET['tasklistID'])) {
                    echo "<button type='sumbit' class='create-new-tasklist-button' name='create'>Create Task List</button>";
                } else {
                    echo "<button type='sumbit' class='update-new-tasklist-button' name='update'>Update Task List</button>";
                }
                ?>
            </form>
        </div>
    </div>
</body>
</html>