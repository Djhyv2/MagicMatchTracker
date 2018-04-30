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
            $body = "<h1>$this->pageTitle</h1>\n"; //adds the top of the page title to the $body variable to be displayed

            if ($message) { //if a message has been passed in, then it will be displayed
                $body .= "<p class='message'>$message</p>\n";
            }
        
            $body .= "<p><a class='matchButton' href='index.php?page=matchForm'>+ Add a Match</a></p>\n"; //button to redirect to add match form

            if (count($matches) < 1){ //if no matches exist in records, message is displayed and $body is returned to controller
                $body .= "<p>No matches to display!</p>\n";
                return $body;
            }

            $body .= "<table>\n"; //open table tag
            
            $body .= "<tr><th></th><th></th><th>Format</th><th>Player 1</th><th>Player 2</th><th>Player 1 Deck</th><th>Player 2 Deck</th><th>Wins</th><th>Losses</th><th>Ties</th><th>Date</th><th>Tournament</th></tr>"; //column headers
            

            //fill match data into columns
            foreach ($matches as $match) {
                $id = $match['ID'];
                $format = $match['Format'];
                $player1 = $match['Player1'];
                $player2 = $match['Player2'];
                $player1Deck = $match['Player1Deck'];
                $player1DeckLink=$match['Player1DeckLink'];
                $player2Deck = $match['Player2Deck'];
                $player2DeckLink= $match['Player2DeckLink'];
                $wins = $match['Wins'];
                $losses = $match['Losses'];
                $ties = $match['Ties'];
                $date = $match['Date'];
                $tournament = $match['Tournament'];
                
                $player1DeckCombined = ($player1DeckLink==""?$player1Deck:"<a href='$player1DeckLink'>$player1Deck</a>");//Combines Link with Name
                $player2DeckCombined = ($player2DeckLink==""?$player2Deck:"<a href='$player2DeckLink'>$player2Deck</a>");//Combines Link with Name
                

                $body .= "<tr>";
                $body .= "<td><form action='index.php' method='post'><input type='hidden' name='action' value='delete' /><input type='hidden' name='id' value='$id' /><input type='submit' value='Delete'></form></td>";
                $body .= "<td><form action='index.php' method='post'><input type='hidden' name='action' value='edit' /><input type='hidden' name='id' value='$id' /><input type='submit' value='Edit'></form></td>";
                $body .= "<td>$format</td><td>$player1</td><td>$player2</td><td>$player1DeckCombined</td><td>$player2DeckCombined</td><td>$wins</td><td>$losses</td><td>$ties</td><td>$date</td><td>$tournament</td>";
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
<p>Created by: Connor Fitzmaurice, Dustin Hengel, Mercy Housh, and Scott Watkins</p>
</body>
</html>
EOT;
			return $html;
		}
    }  
?>