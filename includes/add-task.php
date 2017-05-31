<?php 
    $task = strip_tags( $_POST['task'] );
    $date = date('Y-m-d');
    $time = date('H:i:s');

    require("connect.php");

    $conn->query("INSERT INTO todo VALUES (0, '$task', '$date', '$time', 0)");
?>
