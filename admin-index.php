<?php
require_once("includes/config.php");
session_start();
$errors = array();

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
    $mysqli->query("UPDATE users SET email = '', psword = '' WHERE userID = $deleteID");
}
if (isset($_POST['fname'])) {
    $firstname = $_POST['fname'];
    $surname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passworcCheck = $_POST['confirm-password'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    if ($password != $passworcCheck) {
        array_push($errors, "Passwords don't match");
    }

    $existsQuery = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $existsQuery->bind_param("s", $email);
    $existsQuery->execute();
    $existsResult = $existsQuery->get_result();
    if (mysqli_num_rows($existsResult) != 0) {
        array_push($errors, "Email is taken");
    }

    if (strlen($password) < 8 || strlen($passworcCheck) < 8) {
        array_push($errors, "The password needs to be at least 8 characters");
    }

    if (count($errors) == 0) {
        $query = $mysqli->prepare("INSERT INTO users (firstname, surname, email, psword, admin) VALUES (?, ?, ?, ?, 1)");
        $query->bind_param('ssss', $firstname, $surname, $email, $passwordHash);
        $query->execute();
    
        header("Location: admin-index.php");
    }
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
        <div class='create-admin-account'>
            <h1>New Admin Account</h1>
            <form class="login-form" method="post">
                <div class="flex-row">
                    <div class="flex-column">
                        <label for="fname">First name</label>
                        <label for="lname">Surname</label>
                        <label for="email">Email</label>
                        <label for="password">Password</label>
                        <label for="confirm-password">Confirm Password</label>
                    </div>
                    <div class="flex-column">
                        <input type="text" name="fname" class="admin-detail" required>
                        <input type="text" name="lname" class="admin-detail" required>
                        <input type="email" name="email" class="admin-detail" required>
                        <input type="password" name="password" class="admin-detail" required>
                        <input type="password" name="confirm-password" class="admin-detail" required>
                    </div>
                </div>
                <div class="flex-column">
                    <button type="sumbit" class="signup-submit">Sign Up</button>
                </div>
                <?php
                if (count($errors) > 0) {
                    echo "<div class='errors'>";
                    foreach ($errors as $error) {
                        echo "<p class='error-message' style='text-align: center;'>$error</p>";
                    }
                    echo "</div>";
                }
                ?>
            </form>
        </div>
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