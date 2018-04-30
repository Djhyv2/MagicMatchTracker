<?php
    //View controls all HTML
    class View {
        private $stylesheet = 'style.css'; //variable to store stylesheet
        private $pageTitle = 'Magic Match Tracker'; //varable to store page title; useful for changing page title without hardcoding

        public function __construct () {
            
        }//Constructor for View
        
        public function __destruct () {
            
        }//Destructor for View

        public function matchListView ($matches, $message = '')
        {
            $body = "<h1>$this->pageTitle</h1>\n"; //adds the top of the page title to the $body variable to be displayed

            if ($message) { //if a message has been passed in, then it will be displayed
                $body .= "<p class='message'>$message</p>\n";
            }
        
            $body .= "<p><a class='matchButton' href='index.php?page=matchForm'>+ Add a Match</a></p>\n"; //button to redirect to add match form

            if (count($matches) < 1){ //if no matches exist in records, message is displayed and $body is returned to controller
                $body .= "<p>No Matches to Display</p>\n";
                return pages($body);
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
                $tournament = $match['Tournament'];//Gets variables for row
                
                $tournament = ($tournament==1?'&#9745;':'&#9744;');//Sets Tournament to display a checkbox
                $player1DeckCombined = ($player1DeckLink==""?$player1Deck:"<a href='$player1DeckLink'>$player1Deck</a>");//Combines Link with Name
                $player2DeckCombined = ($player2DeckLink==""?$player2Deck:"<a href='$player2DeckLink'>$player2Deck</a>");//Combines Link with Name
                

                $body .= "<tr>";
                $body .= "<td><form action='index.php' method='post'><input type='hidden' name='action' value='delete' /><input type='hidden' name='id' value=$id /><input type='submit' value='Delete'></form></td>";//Delete button
                $body .= "<td><form action='index.php' method='post'><input type='hidden' name='action' value='edit' /><input type='hidden' name='id' value=$id /><input type='submit' value='Edit'></form></td>";//Edit button
                $body .= "<td>$format</td><td>$player1</td><td>$player2</td><td>$player1DeckCombined</td><td>$player2DeckCombined</td><td>$wins</td><td>$losses</td><td>$ties</td><td>$date</td><td>$tournament</td>";
                $body .= "</tr>\n";//Turns variables for row into HTML
            }//For each row

            $body .= "</table>\n"; //end table tag

            return $this->page($body);
        }

        public function matchFormView($match = NULL, $message = '') {
            
            if ($match!=NULL) 
            { 
                $id=($match['ID']!=NULL?$match['ID']:NULL);
                $player1Username=($match['Player1Username']!=NULL?$match['Player1Username']:"");
                $player1DeckName=($match['Player1DeckName']!=NULL?$match['Player1DeckName']:"");
                $player1DeckLink=($match['Player1DeckLink']!=NULL?$match['Player1DeckLink']:"");
                $player2Username=($match['Player2Username']!=NULL?$match['Player2Username']:"");
                $player2DeckName=($match['Player2DeckName']!=NULL?$match['Player2DeckName']:"");
                $player2DeckLink=($match['Player2DeckLink']!=NULL?$match['Player2DeckLink']:"");
                $wins=($match['Wins']!=NULL?$match['Wins']:"");
                $losses=($match['Losses']!=NULL?$match['Losses']:"");
                $ties=($match['Ties']!=""?$match['Ties']:0);//Default 0 Ties if not specified
                $date=($match['Date']!=NULL?$match['Date']:"");
                $tournament=($match['Tournament']!=NULL?'checked':"");//Defaults to unchecked
                $format=($match['Format']!=NULL?$match['Format']:"Modern");
            }//Gets data from argument, sets defaults if data not present
            
            $selectedFormat=array('Modern'=>'','Sealed'=>'','Draft'=>'','Pauper'=>'','Legacy'=>'','No-Banlist Modern'=>'','Standard'=>'','Vintage'=>'','Highlander'=>'');//Array for which format is selected
            $selectedFormat['Format']='selected';//Selects input format
            
            

            $body =  "<h1>Matches</h1>";
            
            if ($message) 
            { 
                $body .= "<p class='message'>$message</p>\n";
            }//If Message to display, display it

            $body .= "<form action='index.php' method='post'>";

            if ($id!=NULL) 
            { 
                $body .= "<input type='hidden' name='action' value='update' />";
                $body .= "<input type='hidden' name='ID' value='{$id}' />";
            }//ID present, then updating a match
            else 
            { 
                $body .= "<input type='hidden' name='action' value='add' />";
            }//Otherwise adding a match

            $body .= 
<<<EOT2
            <p>Player 1: 
            <input type="text" name="Player1UserName" value="$player1Username" placeholder="Player 1" maxlength="255" size="80"></p>

            <p>Player 1 Deck Name: 
            <input type="text" name="Player1DeckName" value="$player1DeckName" placeholder="Player 1 Deck Name" maxlength="255" size="80"></p>
            
            <p>Player 1 Deck Link (Optional): 
            <input type="text" name="Player1DeckLink" value="$player1DeckLink" placeholder="Player 1 Deck Link" maxlength="255" size="80"></p>
                    
            <p>Player 2: 
            <input type="text" name="Player2UserName" value="$player2Username" placeholder="Player 2" maxlength="255" size="80"></p>

            <p>Player 2 Deck Name: 
            <input type="text" name="Player2DeckName" value="$player2DeckName" placeholder="Player 2 Deck Name" maxlength="255" size="80"></p>
            
            <p>Player 2 Deck Link (Optional): 
            <input type="text" name="Player2DeckLink" value="$player2DeckLink" placeholder="Player 2 Deck Link" maxlength="255" size="80"></p>
            
            <p>Wins: 
            <input type="text" name="Wins" value="$wins" placeholder="Wins" maxlength="255" size="80"></p>

            <p>Losses: 
            <input type="text" name="Losses" value="$losses" placeholder="Losses" maxlength="255" size="80"></p>
                    
            <p>Ties: 
            <input type="text" name="Ties" value="$ties" placeholder="Ties" maxlength="255" size="80"></p>
                    
            <p>Date (mm-dd-yy): 
            <input type="text" name="Date" value="$date" placeholder="Date" maxlength="255" size="80"></p>
            
            <p>Was Tournament: 
            <input type="checkbox" name="Wins" value='1' $tournament  maxlength="255" size="80"></p>

            <p>Format: 
            <select name='Format'>
                <option value = "Modern" {$selectedFormat['Modern']}>Modern</option>
                <option value = "Sealed" {$selectedFormat['Sealed']}>Sealed</option>
                <option value = "Draft" {$selectedFormat['Draft']}>Draft</option>                 
                <option value = "Pauper" {$selectedFormat['Pauper']}>Pauper</option>
                <option value = "Legacy" {$selectedFormat['Legacy']}>Legacy</option>
                <option value = "No-Banlist Modern" {$selectedFormat['No-Banlist Modern']}>No-Banlist Modern</option>
                <option value = "Standard" {$selectedFormat['Standard']}>Standard</option>
                <option value = "Vintage" {$selectedFormat['Vintage']}>Vintage</option>
                <option value = "Highlander" {$selectedFormat['Highlander']}>Highlander</option>
            </select>
                    
            <input type="submit" name="submit" value="Submit">
            <input type="submit" name="cancel" value="Cancel">
            </form>
            </body>
            </html>
EOT2;
            //Adds Form Elements

            
            
            return page($body);
        }

        public function errorView($message) 
        {
                $body = "<h1>Error</h1>\n";
                $body .= "<p>$message</p>\n";
                return $this->page($body);
        }//Displays a simple error html page
		
        private function page($body) 
        {
            $html = 
<<<EOT
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
            //Adds requisite HTML Header and Such to a body html
            return $html;
        }
    }  
?>