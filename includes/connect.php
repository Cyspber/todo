<?php
    $user = 'root';
    $password = 'Admin@123';
    $db = 'todo';
    $host = 'localhost';
    $port = 8888;

    $conn = mysqli_connect($host, $user, $password, $db);
   
/*    if(!$conn){
			die("数据库连接错误" . mysqli_connect_error());
        echo "connect failed";
	} else {
			echo"数据库连接成功";
	}*/
?> 
