<?php
    echo "If you see this you index works!";
    
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
        echo "If you see this I can access your database!";
    }
    
    $controller=new Controller();
    $controller->run();
?>

