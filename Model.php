<?php
    //Model controls all data
    class Model
    {
        private $sql;
        private $error;
        
        public function __construct()
        {
            require("Credentials.php");//Required Database Credentials
            $this->sql = new mysqli($server,$user,$password,$database);//Connects to Database
            $this->error = $this->sql->connect_error;//Sets Error
        }//Constructor for Model
        
        public function __destruct()
        {
            if($this->sql)
            {
                $this->sql->close();//Closes connection
            }//If SQL Connection exists
            $this->sql=null;
        }//Destructor for Model
        
        public function readMatches()
        {
            $matches=array();//Array to Store all Matches
            if($this->sql->connect_error!=null)
            {
                $this->error=$this->sql->connect_error;
                return array($matches,$this->error);//Returns Empty matches and Error
            }//If Connection Error
            
            $results = $this->sql->query('
                    SELECT Matches.ID, Matches.Format, Player1.FirstName AS "First Name", Player1.LastName AS "Last Name", Player2.FirstName AS "Opponent First Name",Player2.LastName AS "Opponent Last Name", Player1Deck.Name AS "Deck", Player2Deck.Name AS "Opponent Deck", Matches.Wins ,Matches.Losses, Matches.Ties, Matches.Date,Matches.Tournament 
                    FROM Matches
                    JOIN Players AS Player1 ON Matches.Player1ID = Player1.ID
                    JOIN Players AS Player2 ON Matches.Player2ID = Player2.ID
                    JOIN Decks AS Player1Deck ON Matches.Player1DeckID = Player1Deck.ID
                    JOIN Decks AS Player2Deck ON Matches.Player2DeckID = Player2Deck.ID; ');//Queries SQL Database to Read all Matches
            
            if($results==null)
            {
                $this->error=$this->sql->error;
                return array($matches,$this->error);//Returns Empty matches and Error
            }//If Query Errored
            
            if($result->num_rows > 0)
            {
                for($record=$results->fetch_assoc();$record!=null;$record=$results->fetch_assoc())
                {
                    array_push($matches,$record);//Adds record to output
                }//For each record
            }//If result has atleast 1 record
            
            $results->close();//Closes results
            
            return array($matches,$this->error);//Returns matches and possible error string
        }//Reads Matches from Database
        
        public function readMatch($id)
        {
            $match=NULL;//Holds Match to be Returned
            if($id==null)
            {
                $this->error="No ID Provided";
                return array($match,$this->error);//Returns empty match and error string
            }//If No ID
            
            if($this->sql->connect_error!=null)
            {
                $this->error=$this->sql->connect_error;
                return array($match,$this->error);//Returns empty match and error string
            }//If Connection Error
            
                
            $preparedStatement = $this->sql->prepare('
                    SELECT Matches.ID, Matches.Player1ID AS Player1ID, Matches.Player2ID AS Player2ID, Matches.Player1DeckID AS Player1DeckID, Matches.Player2DeckID AS Player2DeckID, Format, Player1.FirstName AS "First Name", Player1.LastName AS "Last Name", Player2.FirstName AS "Opponent First Name",Player2.LastName AS "Opponent Last Name", Player1Deck.Name AS "Deck", Player2Deck.Name AS "Opponent Deck", Matches.Wins ,Matches.Losses, Matches.Ties, Matches.Date,Matches.Tournament 
                    FROM Matches
                    JOIN Players AS Player1 ON Matches.Player1ID = Player1.ID
                    JOIN Players AS Player2 ON Matches.Player2ID = Player2.ID
                    JOIN Decks AS Player1Deck ON Matches.Player1DeckID = Player1Deck.ID
                    JOIN Decks AS Player2Deck ON Matches.Player2DeckID = Player2Deck.ID');//Prepares statement to inject ID into
  
            if($preparedStatement->bind_param("i",$id)==false)
            {
                $this->error = $this->sql->error;
                return array($match,$this->error);//Returns empty match and error string
            }//If didn't bind parameter
            
            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return array($match,$this->error);//Returns empty match and error string
            }//If Failed to Execute Query
            
            $result=$preparedStatement->get_result();//Gets Result from Query
            if($result==null)
            {
                $this->error=$preparedStatement->error;
                return array($match,$this->error);//Returns empty match and error string
            }//If Failed to Retrieve Result
            
            if($result->num_rows!=1)
            {   
                $this->error="Duplicate Match IDs";
                return array($match,$this->error);//Returns empty match and error string
            }//If Not Exactly One Result
            
            $match=$result->fetch_assoc();//Gets Match with given ID
            
            $preparedStatement->close();//Closes Statement

            return array($match,$this->error);//Returns matches and possible error string   
        }
        
        public function getError()
        {
            return $this->error;//Returns Connection Error
        }
        
        public function addMatch($match)
        {
            if($this->sql->connect_error!=null)
            {
                $this->error=$this->sql->connect_error;
                return $this->error;
            }//If Connection Error
            
            $player1FirstName=$match['player1FirstName'];
            $player1LastName=($match['player1LastName']!=NULL?$match['player1LastName']:"");//Default Blank if Not Specified
            $player1DeckName=$match['player1DeckName'];
            $player1MainBoard=($match['player1MainBoard']!=NULL?$match['player1MainBoard']:"");//Default Blank if Not Specified
            $player1SideBoard=($match['player1SideBoard']!=NULL?$match['player1SideBoard']:"");//Default Blank if Not Specified
            $player2FirstName=($match['player2FirstName']!=NULL?$match['player2LastName']:"");//Default Blank if Not Specified
            $player2LastName=($match['player1LastName']!=NULL?$match['player2LastName']:"");//Default Blank if Not Specified
            $player2DeckName=$match['player2DeckName'];
            $player2MainBoard=($match['player2MainBoard']!=NULL?$match['player2MainBoard']:"");//Default Blank if Not Specified
            $player2SideBoard=($match['player2SideBoard']!=NULL?$match['player2SideBoard']:"");//Default Blank if Not Specified
            $wins=$match['wins'];
            $losses=$match['losses'];
            $ties=($match['ties']!=NULL?$match['ties']:0);//Default 0 Ties is not specified
            $date=$match['date'];
            $tournament=($match['tournament']!=NULL?$match['tournament']:0);//Default Not a Tournament if not specified
            $format=($match['format']!=NULL?match['format']:'Modern');//Default Modern if not specified, Gets Data input from Parameter

            if($player1FirstName==NULL)
            {
                $this->error="Missing Player 1 First Name";
                return $this->error;
            }

            if($player1DeckName==NULL)
            {
                $this->error="Missing Player 1 Deck Name";
                return $this->error;
            }

            if($player2DeckName==NULL)
            {
                $this->error="Missing Player 2 Deck Name";
                return $this->error;
            }

            if($wins==NULL)
            {
                $this->error="Missing Wins";
                return $this->error;
            }

            if($losses==NULL)
            {
                $this->error="Missing Losses";
                return $this->error;
            }

            if($date==NULL)
            {
                $this->error="Missing Date";
                return $this->error;
            }
            //Returns Error if Missing Critical Info
            
            
            $player1ID=addPlayer($player1FirstName,$player1LastName);//Adds Player1 or Gets Player1ID
            if($this->error!=NULL)
            {
                return $this->error;
            }//If addPlayer errored
            $player2ID=NULL;//Default Player2ID to NULL
            if($player2FirstName!=""||$player2LastName!="")
            {
                $player2ID=addPlayer($player2FirstName,$player2LastName);//Adds Player2 or Gets Player2ID
                if($this->error!=NULL)
                {
                    return $this->error;
                }//If addPlayer errored
            }//If Player2 Information Exists
            
            
            $player1DeckID=addDeck($player1DeckName,$player1MainBoard,$player1SideBoard);//Adds Player1Deck or Gets Player1DeckID
            if($this->error!=NULL)
            {
                return $this->error;
            }//If addDeck errored
            $player2DeckID=addDeck($player2DeckName,$player2MainBoard,$player2SideBoard);//Adds Player2Deck or Gets Player2DeckID
            if($this->error!=NULL)
            {
                return $this->error;
            }//If addDeck errored

            $preparedStatement=$this->sql->prepare('INSERT INTO Matches (Player1ID,Player2ID,Wins,Losses,Ties,Player1DeckID,Player2DeckID,Date,Tournament,Format) VALUES (?,?,?,?,?,?,STR_TO_DATE(?,"%m-%d-%y"),?,?)');//Prepares Match Insert
            if($preparedStatement->bind_param("iiiiiiisis",$player1ID,$player2ID,$wins,$losses,$ties,$player1DeckID,$player2DeckID,$date,$tournament,$format)==false)
            {
                $this->error = $this->sql->error;
                return $this->error;//Returns error
            }//If didn't bind parameter
            
            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return $this->error;
            }//If Failed to Execute Query
            
            $preparedStatement->close();//Closes Statement
            
            return $this->error;//Returns empty error if successful
        }
        
        private function addPlayer($firstName,$lastName)
        {
            $preparedStatement=$this->sql->prepare('INSERT IGNORE INTO Players (FirstName,LastName) VALUES (?,?)');//Inserts if Not Duplicate Player
            if($preparedStatement->bind_param("ss",$firstName,$lastName)==false)
            {
                $this->error = $this->sql->error;
                return -1;//Returns -1 if errored
            }//If didn't bind parameter
            
            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Execute Query
            
            $preparedStatement->close();//Closes Statement
            
            
            
            $preparedStatement=$this->sql->prepare('SELECT ID FROM Players WHERE FirstName = ? AND LastName = ?');//Gets ID
            if($preparedStatement->bind_param("ss",$firstName,$lastName)==false)
            {
                $this->error = $this->sql->error;
                return -1;//Returns -1 if errored
            }//If didn't bind parameter
            
            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Execute Query
            
            $result=$preparedStatement->get_result();//Gets Result from Query
            if($result==null)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Retrieve Result
            
            if($result->num_rows!=1)
            {
                $this->error = "Duplicate Players Detected";
                return -1;//Returns -1 if errored
            }//If Exactly One Result
            
            $id=$result->fetch_assoc()['ID'];//Gets ID with given Name
            
            $preparedStatement->close();//Closes Statement

            return $id;//Returns ID    
        }
        
        private function addDeck($name,$mainBoard,$sideBoard)
        {
            $preparedStatement=$this->sql->prepare('SELECT ID FROM Decks WHERE Name = ? AND Mainboard = ? AND Sideboard = ?');//Selects Deck ID if Exists
            if($preparedStatement->bind_param("sss",$name,$mainBoard,$sideBoard)==false)
            {
                $this->error = $this->sql->error;
                return -1;//Returns -1 if errored
            }//If didn't bind parameter
            
            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Execute Query
            
            $result=$preparedStatement->get_result();//Gets Result from Query
            if($result==null)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Retrieve Result
            
            if($result->num_rows==1)
            {
                $id=$result->fetch_assoc()['ID'];//Gets ID with given Name
                return $id;//Returns ID
            }//If Deck was in Database
            
            
            $preparedStatement=$this->sql->prepare('INSERT INTO Decks (Name,Mainboard,Sideboard) VALUES (?,?,?);');//Inserts if DeckID didn't Exist
            if($preparedStatement->bind_param("sss",$name,$mainBoard,$sideBoard)==false)
            {
                $this->error = $this->sql->error;
                return -1;//Returns -1 if errored
            }//If didn't bind parameter
            
            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Execute Query
            
            $preparedStatement->close();//Closes Statement
            
            
            $result = $this->sql->query('SELECT LAST_INSERT_ID()');//Gets Newly Inserted ID
            
            if($result==null)
            {
                $this->error=$this->sql->error;
                return -1;//Returns -1 if errored
            }//If Failed to Retrieve Result
            
            if($result->num_rows!=1)
            {   
                $this->error="LAST_INSERT_ID() ERROR";
                return -1;//Returns -1 if errored
            }//If Not Exactly One Result
            
            $id=$result->fetch_assoc();//Gets Match with given ID
            
            $result->close();//Closes result
            
            return $id;//Returns Deck ID
        }
        
        public function updateMatch($match)
        {
            if($this->sql->connect_error!=null)
            {
                $this->error=$this->sql->connect_error;
                return $this->error;
            }//If Connection Error
            
            $player1FirstName=$match['player1FirstName'];
            $player1LastName=($match['player1LastName']!=NULL?$match['player1LastName']:"");//Default Blank if Not Specified
            $player1DeckName=$match['player1DeckName'];
            $player1MainBoard=($match['player1MainBoard']!=NULL?$match['player1MainBoard']:"");//Default Blank if Not Specified
            $player1SideBoard=($match['player1SideBoard']!=NULL?$match['player1SideBoard']:"");//Default Blank if Not Specified
            $player2FirstName=($match['player2FirstName']!=NULL?$match['player2LastName']:"");//Default Blank if Not Specified
            $player2LastName=($match['player1LastName']!=NULL?$match['player2LastName']:"");//Default Blank if Not Specified
            $player2DeckName=$match['player2DeckName'];
            $player2MainBoard=($match['player2MainBoard']!=NULL?$match['player2MainBoard']:"");//Default Blank if Not Specified
            $player2SideBoard=($match['player2SideBoard']!=NULL?$match['player2SideBoard']:"");//Default Blank if Not Specified
            $wins=$match['wins'];
            $losses=$match['losses'];
            $ties=($match['ties']!=NULL?$match['ties']:0);//Default 0 Ties is not specified
            $date=$match['date'];
            $tournament=($match['tournament']!=NULL?$match['tournament']:0);//Default Not a Tournament if not specified
            $format=($match['format']!=NULL?match['format']:'Modern');//Default Modern if not specified, Gets Data input from Parameter
            $player1ID=$match['player1ID'];
            $player2ID=($match['player2ID']!=NULL?$match['player2ID']:NULL);//Default Blank if Not Specified
            $player1DeckID=$match['player1DeckID'];
            $player2DeckID=$match['player2DeckID'];
            $matchID=$match['matchID'];

            if($matchID==null)
            {
                $this->error="No MatchID";
                return $this->error;
            }//If No MatchID
            
            if($player1FirstName==NULL)
            {
                $this->error="Missing Player 1 First Name";
                return $this->error;
            }

            if($player1DeckName==NULL)
            {
                $this->error="Missing Player 1 Deck Name";
                return $this->error;
            }

            if($player2DeckName==NULL)
            {
                $this->error="Missing Player 2 Deck Name";
                return $this->error;
            }

            if($wins==NULL)
            {
                $this->error="Missing Wins";
                return $this->error;
            }

            if($losses==NULL)
            {
                $this->error="Missing Losses";
                return $this->error;
            }

            if($date==NULL)
            {
                $this->error="Missing Date";
                return $this->error;
            }
            
            if($player1ID==NULL)
            {
                $this->error="Missing Player 1 ID";
                return $this->error;
            }
            
            if($player1DeckID==NULL)
            {
                $this->error="Missing Player 1 DeckID";
                return $this->error;
            }
            
            if($player2DeckID==NULL)
            {
                $this->error="Missing Player 2 DeckID";
                return $this->error;
            }
            //Returns Error if Missing Critical Info
            
            $player1ID=updatePlayer($player1ID,$player1FirstName,$player1LastName);//Updates Player1
            if($this->error!=NULL)
            {
                return $this->error;
            }//If updatePlayer Errored
            if($player2FirstName!=""||$player2LastName!="")
            {
                $player2ID=updatePlayer($player2ID,$player2FirstName,$player2LastName);//Updates Player2
                if($this->error!=NULL)
                {
                    return $this->error;
                }//If updatePlayer Errored
            }//If Player2 Exists
            $player1DeckID=updateDeck($player1DeckID,$player1DeckName,$player1MainBoard,$player1SideBoard);//Update Player1Deck
            if($this->error!=NULL)
            {
                return $this->error;
            }//If updateDeck Errored
            $player2DeckID=updateDeck($player2DeckID,$player2DeckName,$player2MainBoard,$player2SideBoard);//Update Player2Deck
            if($this->error!=NULL)
            {
                return $this->error;
            }//If updateDeck Errored
            
            
            $preparedStatement=$this->sql->prepare('UPDATE Matches SET Player1ID=?,Player2ID=?,Wins=?,Losses=?,Ties=?,Player1DeckID=?,Player2DeckID=?,Date=STR_TO_DATE(?,"%m-%d-%y"),Tournament=?,Format=?) WHERE ID=?');//Prepares Match Insert
            if($preparedStatement->bind_param("iiiiiiisisi",$player1ID,$player2ID,$wins,$losses,$ties,$player1DeckID,$player2DeckID,$date,$tournament,$format,$matchID)==false)
            {
                $this->error = $this->sql->error;
                return $this->error;//Returns error
            }//If didn't bind parameter
            
            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return $this->error;
            }//If Failed to Execute Query
            
            $preparedStatement->close();//Closes Statement
            
            return $this->error;//Returns empty error if successful     
            
        }
        
        private function updatePlayer($id,$firstName,$lastName)
        {
            if($id==NULL)
            {
                return addPlayer($firstName,$lastName);//Creates Player if Previously NULL
            }//If New Player 2
            
            $count=countPlayerUses($id);//Counts Player Uses
            
            if($count==1)
            {
                $preparedStatement=$this->sql->prepare('UPDATE Players SET FirstName = ?, LastName = ? WHERE ID = ?;');//Updates Player
                if($preparedStatement->bind_param("sss",$firstName,$lastName,$id)==false)
                {
                    $this->error = $this->sql->error;
                    return -1;//Returns -1 if errored
                }//If didn't bind parameter

                if($preparedStatement->execute()==false)
                {
                    $this->error=$preparedStatement->error;
                    return -1;//Returns -1 if errored
                }//If Failed to Execute Query

                $preparedStatement->close();//Closes Statement
                return $id;//Returns unchanged ID
            }//If Only 1 Instance to Update
            else
            {
                $id=addPlayer($player1FirstName,$player1LastName);//Adds Player1 or Gets Player1ID
                if($this->error!=NULL)
                {
                    return -1;//Returns -1 if Errored
                }//If addPlayer errored
                return $id;//Returns new ID
            }//If Multiple occurrences 
        }
        
        private function countPlayerUses($id)
        {
            $preparedStatement=$this->sql->prepare('SELECT COUNT(*) FROM Matches WHERE Player1ID = ? OR Player2ID = ?');//Checks how many instances of playerID exist
            if($preparedStatement->bind_param("i",$id)==false)
            {
                $this->error = $this->sql->error;
                return -1;//Returns -1 if errored
            }//If didn't bind parameter
            
            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Execute Query
            
            $result=$preparedStatement->get_result();//Gets Result from Query
            if($result==null)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Retrieve Result
            
            if($result->num_rows!=1)
            {
                $this->error="COUNT PlayerID ERROR";
                return -1;//Returns -1 if errored
            }//If Deck was in Database
            
            $count=$result->fetch_assoc()['Count'];//Gets count of uses of playerID
            
            $result->close();//Closes result
            
            return $count;//Returns Count
        }
        
        public function updateDeck($id,$deckName,$mainboard,$sideboard)
        {
            $count=countDeckUses($id);//Counts Deck Uses
            
            if($count==1)
            {
                $preparedStatement=$this->sql->prepare('UPDATE Decks SET Name = ?, Mainboard = ?, Sideboard = ? WHERE ID = ?;');//Updates Deck
                if($preparedStatement->bind_param("ssss",$deckName,$mainboard,$sideboard,$id)==false)
                {
                    $this->error = $this->sql->error;
                    return -1;//Returns -1 if errored
                }//If didn't bind parameter

                if($preparedStatement->execute()==false)
                {
                    $this->error=$preparedStatement->error;
                    return -1;//Returns -1 if errored
                }//If Failed to Execute Query

                $preparedStatement->close();//Closes Statement
                return $id;//Returns unchanged ID
            }//If Only 1 Instance to Update
            else
            {
                $id=addDeck($deckName,$mainboard,$sideboard);//Adds Player1 or Gets Player1ID
                if($this->error!=NULL)
                {
                    return -1;//Returns -1 if Errored
                }//If addDeck errored
                return $id;//Returns new ID
            }//If Multiple occurrences 
        }
        
        public function countDeckUses($id)
        {
            $preparedStatement=$this->sql->prepare('SELECT COUNT(*) FROM Matches WHERE Player1DeckID = ? OR Player2DeckID = ?');//Checks how many instances of deckID exist
            if($preparedStatement->bind_param("i",$id)==false)
            {
                $this->error = $this->sql->error;
                return -1;//Returns -1 if errored
            }//If didn't bind parameter
            
            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Execute Query
            
            $result=$preparedStatement->get_result();//Gets Result from Query
            if($result==null)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Retrieve Result
            
            if($result->num_rows!=1)
            {
                $this->error="COUNT DeckID ERROR";
                return -1;//Returns -1 if errored
            }//If Deck was in Database
            
            $count=$result->fetch_assoc()['Count'];//Gets count of uses of playerID
            
            $result->close();//Closes result
            
            return $count;//Returns Count
        }
        
        public function deleteMatch($id)
        {
            if($id==NULL)
            {
                $this->error="Invalid ID to Delete";
                return $this->error;
            }//If No ID Supplied
            
            
            $preparedStatement=$this->sql->prepare('SELECT Player1ID,Player2ID,Player1DeckID,Player2DeckID FROM Matches WHERE ID=?');//Checks gets Deck and PlayerIDs
            if($preparedStatement->bind_param("i",$id)==false)
            {
                $this->error = $this->sql->error;
                return -1;//Returns -1 if errored
            }//If didn't bind parameter
            
            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Execute Query
            
            $result=$preparedStatement->get_result();//Gets Result from Query
            if($result==null)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Retrieve Result
            
            if($result->num_rows!=1)
            {
                $this->error="COUNT DeckID ERROR";
                return -1;//Returns -1 if errored
            }//If Deck was in Database
            
            $IDs=$result->fetch_assoc();//Gets array of IDs
            
            $result->close();//Closes result
            
            $Player1DeckID=$IDs['Player1DeckID'];
            $Player2DeckID=$IDs['Player2DeckID'];
            $player1ID=$IDs['Player1ID'];
            $Player2ID=($IDs['Player2ID']!=NULL?$match['Player2ID']:NULL);//Gets IDs from Row
            
            deletePlayer($player1ID);//Deletes Player1
            if($this->error!=NULL)
            {
                return $this->error;
            }//If deletePlayer Errored
            if($player2ID!=NULL)
            {
                deletePlayer($player2ID);//Deletes Player2
                if($this->error!=NULL)
                {
                    return $this->error;
                }//If deletePlayer Errored
            }//If Player2 Exists
            deleteDeck($player1DeckID);//Delete Player1Deck
            if($this->error!=NULL)
            {
                return $this->error;
            }//If deleteDeck Errored
            deleteDeck($player2DeckID);//Deletes Player2Deck
            if($this->error!=NULL)
            {
                return $this->error;
            }//If deleteDeck Errored
            
            $preparedStatement=$this->sql->prepare('DELETE FROM Matches WHERE ID = ?');//Deletes Match
            if($preparedStatement->bind_param("s",$id)==false)
            {
                $this->error = $this->sql->error;
                return $this->error;//Returns if errored
            }//If didn't bind parameter

            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return $this->error;//Returns if errored
            }//If Failed to Execute Query

            $preparedStatement->close();//Closes Statement
            
            return $this->error;//Returns empty error if successful
        }
        
        private function deletePlayer($id)
        {
            $count=countPlayerUses($id);
            if($count!=1)
            {
                return;
            }//If Used More than Once
            
            $preparedStatement=$this->sql->prepare('DELETE FROM Players WHERE ID = ?');//Deletes Player
            if($preparedStatement->bind_param("s",$id)==false)
            {
                $this->error = $this->sql->error;
                return -1;//Returns -1 if errored
            }//If didn't bind parameter

            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Execute Query

            $preparedStatement->close();//Closes Statement
            return;//Returns nothing
        }
        
        private function deleteDeck($id)
        {
            $count=countDeckUses($id);
            if($count!=1)
            {
                return;
            }//If Used More than Once
            
            $preparedStatement=$this->sql->prepare('DELETE FROM Decks WHERE ID = ?');//Deletes Deck
            if($preparedStatement->bind_param("s",$id)==false)
            {
                $this->error = $this->sql->error;
                return -1;//Returns -1 if errored
            }//If didn't bind parameter

            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return -1;//Returns -1 if errored
            }//If Failed to Execute Query

            $preparedStatement->close();//Closes Statement
            return;//Returns nothing
        } 
        
    }
?>