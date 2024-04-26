<?php
require_once("includes/config.php");
session_start();

$userID = 0;
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $firstname = $_POST['fname'];
    $surname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passworcCheck = $_POST['confirm-password'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    if ($password == $passworcCheck) {
        $query = $mysqli->prepare("INSERT INTO users (firstname, surname, email, psword) VALUES (?, ?, ?, ?)");
        $query->bind_param('ssss', $firstname, $surname, $email, $passwordHash);
        $query->execute();
    
        header("Location: index.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign up</title>
        <link rel="stylesheet" href="css/mobile.css" />
        <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 792px)"/>
        <script src="js/main.js" defer></script>
        <script src="js/login.js" defer></script>
    </head>
    <body>
        <?php include("includes/header.php") ?>
        <div class = "container">
            <div class="signup-container">
                <div class="signup-box">
                    <h2>Sign Up</h2>
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
                                <input type="text" name="fname" required>
                                <input type="text" name="lname" required>
                                <input type="email" name="email" required>
                                <input type="password" name="password" required>
                                <input type="password" name="confirm-password" required>
                            </div>
                        </div>
                        <div class="flex-column">
                            <button type="sumbit" class="signup-submit">Sign Up</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>