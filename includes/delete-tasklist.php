<?php
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
?>