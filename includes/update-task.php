<?php 
    $task_id = strip_tags( $_POST['task_id'] );
    $title = strip_tags($_POST['title']);
    $completed = strip_tags($_POST['completed']);

    require("connect.php");

    $conn->query("UPDATE todo SET title = '$title', completed = '$completed' WHERE id='$task_id'");
?>