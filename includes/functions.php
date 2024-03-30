<?php
function check_login($mysqli) {
    if (isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];
        $query = "select *"
    }
}