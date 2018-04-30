<?php
    //Controller links model and view
    
    require("Model.php");//Required Model Component Code
    require("View.php");//Required View Component Code
    class Controller
    {
        private $model;//Model Component
        private $view;//View Component

        private $page = '';
        private $action = '';
        private $message = '';
        private $data = array();

        public function __construct()
        {
            $this->model = new Model();
            $this->view = new View();//Creates Model and View
            $this->page = $_GET['page'] ? $_GET['page'] : 'matchList';
            $this->action = $_POST['action'];
        }//Constructor for Controller

        public function __destruct() 
        {
             $this->model = null;
             $this->view = null;//Deletes Model and View
        }//Desructor for Controller

        public function run()
        {
            if($error = $this->model->getError()) {
                print $view->errorView($error);
                exit;
            }


            switch($this->action) {
                case 'delete':
                    $this->handleDeleteMatch();
                    break;
                case 'add':
                    $this->handleAddMatch();
                    break;
                case 'edit':
                    $this->handleEditMatch();
                    break;
                case 'update':
                    $this->handleUpdateMatch();
                    break;
            }

            switch($this->page) {
                case 'matchForm':
                    print $this->view->matchFormView($this->data, $this->message);
                    break;
                 default: // 'matchList'
                    list($matches, $error) = $this->model->readMatches();
                    if($error) {
                        $this->message = $error;
                    }
                    print $this->view->matchListView($matches, $this->message);
            }

        }//Main Function for Controller, Ran at Beginning of Program

        private function handleDeleteMatch() {
            if($ettot = $this->model->deleteMatch($_POST['id'])) {
                $this->message = $error;
            }
            $this->page = 'matchList';
        } //this function pulls data from the Model when the case from the switch is == handleDeleteMatch and gets it ready for the View.

        private function handleAddMatch() {
            if($_POST['cancel']) {
                $this->page = 'matchList';
                return;
            }
            $error = $this->model->addMatch($_POST);
            if($error) {
                $this->message = $error;
                $this->page = 'matchForm';
                $this->data= $_POST;
            }
        }

        private function handleEditMatch() {
            list($task, $error) = $this->model->readMatches($_POST['id']);
            if($error) {
                $this->message = $error;
                $this->page = 'matchList';
                return;
            }
            $this->data = $match;
            $this->page = 'matchForm';
        }

        private function handleUpdateMatch() {
            if($_POST['cancel']) {
                $this->page = 'matchList';
                return;
            }
            if($error = $this->model->updateMatch($_POST)) {
                $this->message = $error;
                $this->page = 'matchForm';
                $this->data = $_POST;
                return;
            }
            $this->page = 'matchList';
        }
    }
?>

