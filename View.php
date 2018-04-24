<?php
    //View controls all HTML
    class View {
        private $stylesheet = 'style.css'; //variable to store stylesheet
        private $pageTitle = 'Magic Match Tracker'; //varable to store page title; useful for changing page title without hardcoding

        public function __construct () {
            echo "Magic Match Tracker\n<br>Created by: Dustin Hengel, Mercy Housh, Connor Fitzmaurice, and Scott Watkins";//Prints to Standard Out
        }//Constructor for View
        
        public function __destruct () {
            
        }//Destructor for View

        public function matchListView ($matches, $orderby = 'wins', $orderDirection = 'desc', $message = ''){ //default order by wins descending
            $body = "<h1>Magic Match Tracker</h1>\n"; //adds the top of the page title to the $body variable to be displayed

            if ($message) { //if a message has been passed in, then it will be displayed
                $body .= "<p class='message'>$message</p>\n";
            }
        
            $body .= "<p><a class='matchButton' href='index.php?view=matchform'>+ Add a Match</a></p>\n"; //button to redirect to add match form

            if (count($matches) < 1){ //if no matches exist in records, message is displayed and $body is returned to controller
                $body .= "<p>No matches to display!</p>\n";
                return $body;
            }

            $body .= "<table>\n"; //open table tag
            $body .= "<tr><th>delete</th><th>edit</th>"; //html for opening row tag, as well as delete and edit columns
            //delete edit player1.firstname player1.lastname player2.firstname player2.lastname
            //player1.deck player2.deck wins losses ties dates tournament

            $columns = array (
                array ('name' => 'player1', 'label' => 'Player 1'),
                array ('name' => 'player2', 'label' => 'Player 2'),
                array ('name' => 'wins', 'label' => 'Wins'),
                array ('name' => 'losses', 'label' => 'Losses'),
                array ('name' => 'ties', 'label' => 'Ties')
            );

            foreach ($columns as $column) {
                $name = $column['name'];
                $label = $column['label'];
                
                if ($name == $orderby) {
                    if ($orderDirection == 'asc') {
                        $label .= " &#x25BC;"; // ▼                    
                    } else {
                        $label .= " &#x25B2;"; // ▲
                    }
                }
                
                $body .= "<th><a class='order' href='index.php?orderby=$name'>$label</a></th>";
            }

            foreach ($matches as $match) { //need a better idea of table schema and desired data to be displayed?
                $id = $match['id'];

            }
        }
    }  
?>