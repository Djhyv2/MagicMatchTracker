<?php
    //Model controls all data
    class Model
    {
        private $sql;
        public function __construct()
        {
            require("Credentials.php");//Required Database Credentials
            $sql = new mysqli($server,$user,$password,$database);//Connects to Database
            if($sql->connect_error)
            {
                echo "\n<br>" . $sql->connect_error;//Displays Error
            }//If Failed to Connect
        }//Constructor for Model
        
        public function __destruct()
        {
            $sql=null;
        }//Destructor for Model
        
        
    }
?>