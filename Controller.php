<?php
    //Controller links model and view
    
    require("Model.php");//Required Model Component Code
    require("View.php");//Required View Component Code
    class Controller
    {
        private $model;//Model Component
        private $view;//View Component

        private $action = '';
        private $message = '';
        private $data = array();

        public function __construct()
        {
            $this->model= new Model();
            $this->view= new View();//Creates Model and View
            $this->view = $_GET['view'] ? $_GET['view'] : 'gameList';
            this->action = $_POST['action'];
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

            $this->processOrderBy();

            switch($this->action) {
                case 'delete':
                    $this->handleDelete();
                    break;
                case 'add':
                    $this->handleAddGame();
                    break;
                case 'edit':
                    $this->handleEditGame();
                    break;
                case 'update':
                    $this->handleUpdateGame();
                    break;
            }

            switch($this->view) {
                case 'gameForm':
                    print $this->view->gameFormView($this->data, $this->message);
                    break;
                 default:
                    list($orderBy, $orderDirection) = $this->model->getOrdering();
                    if($error) {
                        $this->message = $error;
                    }
                    print $this->view->gameListView($games, $orderBy, $orderDirction, $this->message);
            }

        }//Main Function for Controller, Ran at Beginning of Program

        private function processOrderBy() {
            if($_GET['orderby']) {
                $this->model->toggleOrder($_GET['orderby']);
            }
        }

        private function handleDelete() {
            if($ettot = $this->model->deleteGame($_POST['id'])) {
                $this->message = $error;
            }
            $this->view = 'gameList';
        }

        private function handleAddGame() {
            if($_POST['cancel']) {
                $this->view = 'gameList';
                return;
            }
            $error = $this->model->addGame($_POST);
            if($error) {
                $this->message = $error;
                $this->view = 'gameForm';
                $this->data= $_POST;
            }
        }

        private function handleEditGame() {
            list($task, $error) = $this->model->getGame($_POST['id']);
            if($error) {
                $this->message = $error;
                $this->view = 'gameList';
                return;
            }
            $this->data = $game;
            $this->view = 'gameForm';
        }

        private function handleUpdateGame() {
            if($_POST['cancel']) {
                $this->view = 'gameList';
                return;
            }
            if($error = $this->model->updateGame($-POST)) {
                $this->message = $error;
                $this->view = 'gameForm';
                $this->data = $_POST;
                return;
            }
            $this->view = 'gameList';
        }
    }
?>

