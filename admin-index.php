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
    header("Location: login.php");
}
$userQuery = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $userQuery->fetch_object();
if ($user->admin == 0) {
    header("Location: index.php");
}
if (isset($_GET['delete'])) {
    $allTasklistQuery = $mysqli->query("SELECT * FROM tasklists");
    while ($tasklist = $allTasklistQuery->fetch_object()) {
        $tasklistQuery = $mysqli->query("SELECT * FROM tasklistaccess WHERE taskListID = $tasklist->taskListID");
        if (mysqli_num_rows($tasklistQuery) == 1) {
            if ($tasklistQuery->fetch_object()->userID = $_GET['delete']) {
                $taskListID = $tasklist->taskListID;
                include("includes/delete-tasklist.php");
            }
        }
    }
    $deleteID = $_GET['delete'];
    $mysqli->query("DELETE FROM tasklistaccess WHERE userID = $deleteID");
    $mysqli->query("UPDATE users SET email = '' WHERE userID = $deleteID");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 792px)"/>
    <script src="js/main.js" defer></script>
</head>
<body>
    <?php include("includes/header.php") ?>
    <div class="admin-container">
        <table>
            <tr>
                <th>Firstname</th>
                <th>Surname</th>
                <th>Email</th>
                <th>Delete Account</th>
            </tr>
            <?php
                $query = $mysqli->query("SELECT * FROM users WHERE admin = 0 AND email != ''");
                while ($user = $query->fetch_object()) {
                    $userIDTable = $user->userID;
                    echo "
                    <tr>
                        <td>$user->firstname</td>
                        <td>$user->surname</td>
                        <td>$user->email</td>
                        <td><a href='admin-index.php?delete=$userIDTable'>Delete account</a></td>
                    </tr>
                    ";
                }
            ?>
        </table>
    </div>
</body>
</html>