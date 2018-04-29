<?php
    //View controls all HTML
    class View {
        private $stylesheet = 'style.css'; //variable to store stylesheet
        private $pageTitle = 'Magic Match Tracker'; //varable to store page title; useful for changing page title without hardcoding

        public function __construct () {
            
        }//Constructor for View
        
        public function __destruct () {
            
        }//Destructor for View

        public function matchListView ($matches, $message = ''){
            $body = "<h1>Magic Match Tracker</h1>\n"; //adds the top of the page title to the $body variable to be displayed

            if ($message) { //if a message has been passed in, then it will be displayed
                $body .= "<p class='message'>$message</p>\n";
            }
        
            $body .= "<p><a class='matchButton' href='index.php?page=matchform'>+ Add a Match</a></p>\n"; //button to redirect to add match form

            if (count($matches) < 1){ //if no matches exist in records, message is displayed and $body is returned to controller
                $body .= "<p>No matches to display!</p>\n";
                return $body;
            }

            $body .= "<table>\n"; //open table tag
            
            //delete edit player1.firstname player1.lastname player2.firstname player2.lastname
            //player1.deck player2.deck wins losses ties dates tournament
            $body .= "<tr><th>Delete</th><th>Edit</th><th>Player 1 Name</th><th>Player 2 Name</th><th>Player 1 Deck</th><th>Player 2 Deck</th><th>Wins</th><th>Losses</th><th>Ties</th><th>Dates</th><th>Tournament</th><th></tr>"; //column headers
            
            //block to generate table dynamically for sorting
            /* $columns = array (
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
            } */

            //fill match data into columns
            foreach ($matches as $match) {
                $id = $match['id'];
                $player1 = $match['firstName'] . " " . $match['lastName'];
                $player2 = $match['opponentFirstName'] . " " . $match['opponentLastName'];
                $player1Deck = $match['player1Deck'];
                $player2Deck = $match['player2Deck'];
                $wins = $match['wins'];
                $losses = $match['losses'];
                $ties = $match['ties'];
                $dates = $match['dates'];
                $tournament = $match['tournament'];

                $body .= "<tr>";
				$body .= "<td><form action='index.php' method='post'><input type='hidden' name='action' value='delete' /><input type='hidden' name='id' value='$id' /><input type='submit' value='Delete'></form></td>";
				$body .= "<td><form action='index.php' method='post'><input type='hidden' name='action' value='edit' /><input type='hidden' name='id' value='$id' /><input type='submit' value='Edit'></form></td>";
				$body .= "<td>$player1</td><td>$player2</td><td>$player1Deck</td><td>$player2Deck</td><td>$wins</td><td>$losses</td><td>$ties</td><td>$dates</td><td>$tournament</td>";
				$body .= "</tr>\n";
            }

            $body .= "</table>\n"; //end table tag

            return $this->page($body);
        }

        public function matchFormView($data = null, $message = '') {
            $firstName = '';
            $lastName = '';
            $deck = '';

            if ($data) { //if data already exists, fill data into fields for editing 
                $firstName = $data['firstName'];
                $lastName = $data['lastName'];
                $deck = $data['deck'];
            }

            $html = <<<EOT1
<!DOCTYPE html>
<html>
<head>
<title>{$this->pageTitle}</title>
<link rel="stylesheet" type="text/css" href="{$this->stylesheet}">
</head>
<body>
<h1>Matches</h1>
EOT1;
            //display any messages that are set
            if ($message) { 
                $html .= "<p class='message'>$message</p>\n";
            }

            $html .= "<form action='index.php' method='post'>";

            if ($data['id']) { //if data is already present, then existing data is updated in DB
                $html .= "<input type='hidden' name='action' value='update' />";
				$html .= "<input type='hidden' name='id' value='{$data['id']}' />";
            } else { //otherwise add data to DB
                $html .= "<input type='hidden' name='action' value='add' />";
            }

            $html .= <<<EOT2
  <p>First Name<br />
  <input type="text" name="firstName" value="$firstName" placeholder="first name" maxlength="255" size="80"></p>

  <p>Last Name<br />
  <input type="text" name="lastName" value="$lastName" placeholder="last name" maxlength="255" size="80"></p>

  <p>Deck<br />
  <input type="text" name="deck" value="$deck" placeholder="deck" maxlength="255" size="80"></p>
  <input type="submit" name='submit' value="Submit"> <input type="submit" name='cancel' value="Cancel">
</form>
</body>
</html>
EOT2;

            print $html;
        }

        public function errorView($message) { //display any error messages, if set
			$body = "<h1>Matches</h1>\n";
			$body .= "<p>$message</p>\n";
			
			return $this->page($body);
		}
		
		private function page($body) {
			$html = <<<EOT
<!DOCTYPE html>
<html>
<head>
<title>{$this->pageTitle}</title>
<link rel="stylesheet" type="text/css" href="{$this->stylesheet}">
</head>
<body>
$body
<p>Created by: Dustin Hengel, Mercy Housh, Connor Fitzmaurice, and Scott Watkins</p>
</body>
</html>
EOT;
			return $html;
		}
    }  
?>