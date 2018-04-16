<?php
    echo "Magic Match Tracker\n<br>"
. " Created by: Dustin Hengel, Mercy Housh, Connor Fitzmaurice, and Scott";
    require('credentials.php');
    $mysqli = new mysqli($server,$user,$password,$database);
    if($mysqli->connect_error)
    {
        echo $mysqli->connect_error;
    }
    
?>

