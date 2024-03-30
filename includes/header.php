<div class = "headerContainer">
    <header>
        <nav class="navigation">
            <a href="index.php">Home</a>
        </nav>
        <?php
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