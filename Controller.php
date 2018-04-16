<?php
    //Controller links model and view
    
    require("Model.php");//Required Model Component Code
    require("View.php");//Required View Component Code
    class Controller
    {
        private $model;//Model Component
        private $view;//View Component
        public function __construct()
        {
            $model= new Model();
            $view= new View();//Creates Model and View
        }//Constructor for Controller

        public function __destruct() 
        {
             $model=null;
             $view=null;//Deletes Model and View
        }//Desructor for Controller

        public function run()
        {

        }//Main Function for Controller, Ran at Beginning of Program
    }
?>

