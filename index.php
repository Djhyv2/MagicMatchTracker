<?php
    echo "If you see this the index works!<br><br>";
    
    require('Credentials.php');
    require("Controller.php");

    $mysqli = new mysqli($server,$user,$password,$database);
    if($mysqli->connect_error)
    {
        echo $mysqli->connect_error;
    }
    else
    {
        echo mysqli_stat($mysqli);
        echo "<br><br>If you see this I can access your database!<br><br>";
    }
    
    $controller=new Controller();
    $controller->run();
?>

