<?php
require_once("includes/config.php");
session_start();

$userID = 0;
$loginException = "continue";
try {
    $userID = $_SESSION["user_id"];
    $resultTaskLists = $mysqli->query("SELECT * FROM tasklists, tasklistaccess WHERE tasklists.taskListID = tasklistaccess.taskListID AND tasklistaccess.userID = $userID");
    $userDetailsQuery = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
    $userDetails = $userDetailsQuery -> fetch_object();
}
catch (Exception $loginException) {
    echo '<script>console.log("User not signed in.");</script>';
}
?>
<?php
$taskException = "continue";
try {
    $taskID = $_GET["task_id"];
}
catch (Exception $taskException) {
    echo '<script>console.log("No task selected.");</script>';
}
ob_clean();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    
    if (isset($_POST['new_comment'])){
        $commentText = $_POST['comment-text'];
        $query = $mysqli->prepare("INSERT INTO taskcomment (text, taskID, userID) VALUES (?, ?, ?);");
        $query->bind_param('sss', $_POST['comment-text'], $taskID, $userID);
        $query->execute();
    
        header("Location: index.php?task_id=$taskID");
    }
    else if (isset($_POST['logout'])) {
        $_SESSION['user_id'] = 0;
        header("Location: index.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home</title>
        <link rel="stylesheet" href="css/mobile.css" />
        <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 792px)"/>
        <script src="js/main.js" defer></script>
    </head>
    <body>
        <?php include("includes/header.php") ?>
        <div class = "container">
            <div class = "flex-row">
                <div class = "task-list">
                <?php
                    if ($userID == 0) {
                        header("Location: login.php");
                    }
                    else {
                        while ($obj = $resultTaskLists -> fetch_object()) {
                            echo "<a href='tasklist-details.php?tasklistID=$obj->taskListID' class='edit-tasklist'>Edit</a>";
                            echo "<p class='task-list-title' style='border-color: $obj->colour'>{$obj->name}</p>";
                            echo "<div class='task-list-tasks' style='border-color: $obj->colour'>";
                            $queryTasks = "SELECT tasks.name, tasks.taskID FROM tasks, taskListtasks WHERE tasklisttasks.taskListID = $obj->taskListID AND tasks.taskID = tasklisttasks.taskID";
                            $resultTasks = $mysqli->query( $queryTasks );
                            while ($obj2 = $resultTasks -> fetch_object()) {
                                echo "<p class='task-title'> <a href=\"index.php?task_id={$obj2->taskID}\">{$obj2->name}</a></p>";
                            }
                            echo "</div>";
                            echo "<p class='add-new-task'><a href='task-details.php?tasklist_id=$obj->taskListID'>+ Add New</a></p>";
                        }
                        echo "<p class='add-new-tasklist'><a href='tasklist-details.php'>+ Add New</a></p>";
                    }
                ?>
                </div>
                <div class = "task-details">
                    <?php
                        try {
                            $taskDetails = $mysqli -> query("SELECT * FROM tasks WHERE taskID = $taskID");
                            $comments = $mysqli -> query("SELECT * FROM taskcomment WHERE taskID = $taskID");
                            $obj = $taskDetails -> fetch_object();
                            $checkOwner = $mysqli -> query("SELECT tasklistaccess.owner FROM tasklisttasks, tasklistaccess WHERE $obj->taskID = tasklisttasks.taskID AND tasklisttasks.tasklistID = tasklistaccess.tasklistID");
                            $access = $checkOwner -> fetch_object();
                            if ($access->owner == 1) {
                                echo "<a href='task-details.php?taskID=$obj->taskID' class='edit-task'>Edit</a>";
                            }
                            echo "<h1 class=\"task-name\">$obj->name</h1>";
                            
                            echo "<h2 class=\"task-description\">$obj->description</h2>";
                            while ($obj2 = $comments -> fetch_object()) {
                                echo "<div class='comment-box'>";
                                $poster = $mysqli -> query("SELECT firstname, surname FROM users WHERE userID = $obj2->userID");
                                $obj3 = $poster -> fetch_object();
                                echo "<p class='poster'>$obj3->firstname $obj3->surname</p>";
                                echo "<p class='comment'>$obj2->text</p>";
                                echo "<p class='timestamp'>$obj2->timestamp</p>";
                                echo "</div>";
                            }
                            echo "<div class='new-comment-box'>";
                            echo "<form class='comment-form' method='post'>";
                            echo "<p class='poster'>Commenting As $userDetails->firstname $userDetails->surname</p>";
                            echo "<input name='comment-text' class='new-comment' required>";
                            echo "<button type='add-comment' class='add-comment' name='new_comment'>Post Comment</button>";
                            echo "</form>";
                            echo "</div>";
                        }
                        catch (Exception $e) {
                            echo "<h1>Please select a task</h1>";
                        }
                    ?>
                </div>
            </div>
        </div>
        <?php include("includes/footer.php") ?>
    </body>
</html>