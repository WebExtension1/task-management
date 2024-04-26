<?php
require_once("includes/config.php");
session_start();

$userID = $_SESSION['user_id'];

$access = true;
if (isset($_GET['tasklistID'])) {
    $taskListID = $_GET['tasklistID'];
    $query = $mysqli->query("SELECT * FROM tasklistaccess, tasklists WHERE tasklistaccess.taskListID = tasklists.taskListID AND tasklistaccess.taskListID = $taskListID AND userID = $userID AND tasklistaccess.owner = 1");
    $result = $query->fetch_object();
    if (mysqli_num_rows($query) == 0) {
        $access = false;
    }
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
    } else if (isset($_POST['delete'])) {
        $mysqli->query("DELETE FROM tasklistaccess WHERE taskListID = $taskListID");
        $linkedTasks = $mysqli->query("SELECT tasks.taskID FROM tasklisttasks, tasks WHERE tasklisttasks.taskListID = $taskListID AND tasks.taskID = tasklisttasks.taskID");
        while ($taskToRemove = $linkedTasks->fetch_object()) {
            $mysqli->query("DELETE FROM tasklisttasks WHERE taskID = $taskToRemove->taskID");
            $mysqli->query("DELETE FROM notification WHERE associatedTask = $taskToRemove->taskID");
            $mysqli->query("DELETE FROM taskcomment WHERE taskID = $taskToRemove->taskID");
            $mysqli->query("DELETE FROM tasks WHERE tasks.taskID = $taskToRemove->taskID");
            $mysqli->query("DELETE FROM taskcompleted WHERE taskID = $taskToRemove->taskID");
        }
        
        $mysqli->query("DELETE FROM tasklists WHERE taskListID = $taskListID");
    } else if (isset($_POST['update'])){
        $query = $mysqli->prepare("UPDATE tasklistaccess SET colour = ? WHERE tasklistID = ? AND userID = $userID");
        $query->bind_param('ss', $_POST['option'], $_GET['tasklistID']);
        $query->execute();

        if ($access == true) {
            $query = $mysqli->prepare("UPDATE tasklists SET name = ? WHERE tasklistID = ?");
            $query->bind_param('ss', $_POST['taskListName'], $_GET['tasklistID']);
            $query->execute();

            $query = $mysqli->query("DELETE FROM tasklistaccess WHERE tasklistID = $taskListID AND owner != 1");
        }
    }
    if (isset($_POST['update']) || isset($_POST['create'])) {
        $usedIDs = array();
        $mysqli->query("DELETE FROM tasklistaccess WHERE tasklistID = $taskListID AND userID != $userID");
        if (isset($_POST['owners'])) {
            $closeStatementCheck = 1;
            $queryText = "INSERT INTO tasklistaccess (userID, taskListID, colour, owner) VALUES ";
            foreach ($_POST['owners'] as $owner) {
                $queryText .= "($owner, $taskListID, 'white', 1)";
                $queryText .= ($closeStatementCheck == count($_POST['owners']) ? ";" : ", ");
                $closeStatementCheck++;
                array_push($usedIDs, $owner);
            }
            $mysqli->query($queryText);
        }
        $valid = false;
        if (isset($_POST['contributors'])) {
            $closeStatementCheck = 1;
            $queryText = "INSERT INTO tasklistaccess (userID, taskListID, colour, owner) VALUES ";
            foreach ($_POST['contributors'] as $contributor) {
                if (!in_array($contributor, $usedIDs)) {
                    $queryText .= "($contributor, $taskListID, 'white', 0)";
                    $queryText .= ($closeStatementCheck == count($_POST['contributors']) ? ";" : ", ");
                    $closeStatementCheck++;
                    $valid = true;
                }
            }
            echo $queryText;
            if ($valid == true) {
                $mysqli->query($queryText);
            }
        }
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
                <?php
                if ($access == true) {
                    echo "<input class='new-tasklist-name-box' type='text' placeholder='Task List Name' name='taskListName'" . (isset($result) ? "value='$result->name'" : "") . " required>";
                }
                ?>
                
                <div class="colour-checkboxes">
                    <label for="white">White</label><input type="radio" name="option" id="white" value="white" onclick="document.getElementById('colour').innerHTML = 'White'" <?php if (isset($result)) { if ($result->colour == "white") { echo "checked"; } } else if (!isset($_GET['tasklistID'])) { echo "checked"; } ?>>
                    <label for="blue">Blue</label><input type="radio" name="option" id="blue" value="blue" onclick="document.getElementById('colour').innerHTML = 'Blue'" <?php if (isset($result)) { if ($result->colour == "blue") { echo "checked"; } } ?>>
                    <label for="red">Red</label><input type="radio" name="option" id="red" value="red" onclick="document.getElementById('colour').innerHTML = 'Red'" <?php if (isset($result)) { if ($result->colour == "red") { echo "checked"; } } ?>>
                    <label for="green">Green</label><input type="radio" name="option" id="green" value="green" onclick="document.getElementById('colour').innerHTML = 'Green'" <?php if (isset($result)) { if ($result->colour == "green") { echo "checked"; } } ?>>
                </div>
            <?php
            if ($access == true) {
                echo "
                <table>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Contributor</th>
                        <th>Owner</th>
                    </tr>
                ";
                $possibleContributors = $mysqli->query("SELECT * FROM users WHERE userID != $userID");
                while ($contributor = $possibleContributors->fetch_object()) {
                    echo "
                    <tr>
                    <td>$contributor->firstname</td>
                    <td>$contributor->surname</td>
                    <td>$contributor->email</td>
                    <td><input type='checkbox' name='contributors[]' value='$contributor->userID' ";
                    if (isset($_GET['tasklistID'])) {
                        $selectedCheck = $mysqli->query("SELECT * FROM tasklistaccess WHERE userID = $contributor->userID AND taskListID = $taskListID AND owner = 0");
                        if (mysqli_num_rows($selectedCheck) > 0) {
                            echo "checked";
                        }
                    }
                    echo "
                    ></td>
                    <td><input type='checkbox' name='owners[]' value='$contributor->userID' ";
                    if (isset($_GET['tasklistID'])) {
                        $selectedCheck = $mysqli->query("SELECT * FROM tasklistaccess WHERE userID = $contributor->userID AND taskListID = $taskListID AND owner = 1");
                        if (mysqli_num_rows($selectedCheck) > 0) {
                            echo "checked";
                        }
                    }
                    echo "
                    ></td>
                    </tr>
                    ";
                }
            }
            echo "</table>";
                if (!isset($_GET['tasklistID'])) {
                    echo "<button type='sumbit' class='create-new-tasklist-button' name='create'>Create Task List</button>";
                } else {
                    echo "<button type='sumbit' class='update-tasklist-button' name='update'>Update Task List</button>";
                }
            ?>
            </form>
            <?php
            if ($access == true && isset($_GET['tasklistID'])) {
                echo "
                <form method='post' class='delete-tasklist'>
                    <button type='sumbit' class='delete-tasklist-button' name='delete'>Delete Task List</button>
                </form>
                ";
            }
            ?>
        </div>
    </div>
</body>
</html>