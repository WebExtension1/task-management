<div class = "headerContainer">
    <header>
        <nav class="navigation">
            <a href="index.php">Home</a>
            <?php
                if (isset($_POST['logout'])) {
                    $_SESSION['user_id'] = 0;
                    header("Location: index.php");
                }
                if (isset($user)) {
                    if ($user->admin == 1) {
                        echo "<a href='admin-index.php'>Admin page</a>";
                    }
                }
                if ($userID == 0){
                    echo "<button class='login'>Login</button>";
                } else {
                    echo "
                    <form method='post'>
                    <button class='logout' name='logout' method='post'>Log out</button>
                    </form>
                    ";
                }
                
            ?>
        </nav>
    </header>
</div>