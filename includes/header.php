<div class = "headerContainer">
    <header>
        <nav class="navigation">
            <a href="index.php">Home</a>
        </nav>
        <?php
            if (isset($_POST['logout'])) {
                $_SESSION['user_id'] = 0;
                header("Location: index.php");
            }
            if ($userID == 0){
                echo "<button class='login'>Login</button>";
            } else {
                echo "<form method='post'>";
                echo "<button class='logout' name='logout' method='post'>Log out</button>";
                echo "</form>";
            }
        ?>
    </header>
</div>