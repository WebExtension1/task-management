<?php
require_once("includes/config.php");
session_start();

$userID = 0;
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $query = $mysqli->prepare('SELECT * FROM users WHERE email = ?'); 
    $query->bind_param('s', $email); 
    $query->execute();
    $results = $query->get_result();

    if ($results->num_rows > 0){ 
        $result = $results->fetch_object();
        if (password_verify($password, $result->psword)) {
            $_SESSION['user_id'] = $result->userID;
            header("Location: index.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="css/mobile.css" />
        <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 792px)"/>
        <script src="js/main.js" defer></script>
        <script src="js/login.js" defer></script>
    </head>
    <body>
        <?php include("includes/header.php") ?>
        <div class = "container">
            <div class="login-container">
                <div class="login-box">
                    <h2>Login</h2>
                    <form class="login-form" method="post">
                        <div class="flex-row">
                            <div class="flex-column">
                                <label>Email</label>
                                <label>Password</label>
                            </div>
                            <div class="flex-column">
                                <input name="email" required>
                                <input type="password" name="password" required>
                            </div>
                        </div>
                        <div class="flex-column">
                            <button type="sumbit" class="login-submit">Login</button>
                            <p class="signup-prompt"><a href="#">Don't have an account? Sign up!</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>