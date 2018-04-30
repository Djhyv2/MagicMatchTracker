<?php
    //Model controls all data
    class Model
    {
        private $sql;
        private $error=NULL;
        
        public function __construct()
        {
            require("Credentials.php");//Required Database Credentials
            $this->sql = new mysqli($server,$user,$password,$database);//Connects to Database
            $this->error = $this->sql->connect_error;//Sets Error
            if($this->sql!=NULL)
            {
                $this->sql->report_mode = MYSQLI_REPORT_ALL;//Turns on Error Reporting
            }
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
            
            if($this->sql==NULL)
            {
                $this->error="No Database Connection";
                return array($matches,$this->error);
            }//If No Connection to Database
            
            
            if($this->sql->connect_error!=null)
            {
                $this->error=$this->sql->connect_error;
                return array($matches,$this->error);//Returns Empty matches and Error
            }//If Connection Error
            
            $results = $this->sql->query('
                    SELECT Matches.ID, Matches.Format, Player1Table.Username AS "Player1", Player2Table.Username AS "Player2", Player1Table.DeckName AS "Player1Deck", Player1Table.DeckLink AS "Player1DeckLink", Player2Table.DeckName AS "Player2Deck", Player2Table.DeckLink AS "Player2DeckLink", Matches.Wins ,Matches.Losses, Matches.Ties, Matches.Date, Matches.Tournament 
                    FROM Matches
                    JOIN MatchParts AS Player1Table ON Player1Table.MatchID = Matches.ID AND Player1Table.OrderedFirst = 1
                    JOIN MatchParts AS Player2Table ON Player2Table.MatchID = Matches.ID AND Player2Table.OrderedFirst = 0;');//Queries SQL Database to Read all Matches
            
            if($results==null)
            {
                $this->error=$this->sql->error;
                return array($matches,$this->error);//Returns Empty matches and Error
            }//If Query Errored
            
            if($results->num_rows > 0)
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
            
            if($this->sql==NULL)
            {
                $this->error="No Database Connection";
                return array($match,$this->error);
            }//If No Connection to Database
            
            if($this->sql->connect_error!=null)
            {
                $this->error=$this->sql->connect_error;
                return array($match,$this->error);//Returns empty match and error string
            }//If Connection Error
            
                
            $preparedStatement = $this->sql->prepare('
                    SELECT Matches.ID, Matches.Format, Player1Table.Username AS "Player1", Player2Table.Username AS "Player2", Player1Table.DeckName AS "Player1Deck", Player1Table.DeckLink AS "Player1DeckLink", Player2Table.DeckName AS "Player2Deck", Player2Table.DeckLink AS "Player2DeckLink", Matches.Wins ,Matches.Losses, Matches.Ties, Matches.Date, Matches.Tournament 
                    FROM Matches
                    JOIN MatchParts AS Player1Table ON Player1Table.MatchID = Matches.ID AND Player1Table.OrderedFirst = 1
                    JOIN MatchParts AS Player2Table ON Player2Table.MatchID = Matches.ID AND Player2Table.OrderedFirst = 0
                    WHERE Matches.ID = ?;');//Prepares statement to inject ID into
  
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
            if($this->sql==NULL)
            {
                $this->error="No Database Connection";
                return $this->error;
            }//If No Connection to Database
            
            if($this->sql->connect_error!=null)
            {
                $this->error=$this->sql->connect_error;
                return $this->error;
            }//If Connection Error
            
            $player1Username=$match['Player1Username'];
            $player1DeckName=$match['Player1DeckName'];
            $player1DeckLink=$match['Player1DeckLink'];
            $player2Username=$match['Player2Username'];
            $player2DeckName=$match['Player2DeckName'];
            $player2DeckLink=$match['Player2DeckLink'];
            $wins=$match['Wins'];
            $losses=$match['Losses'];
            $ties=($match['Ties']!=""?$match['Ties']:0);//Default 0 Ties is not specified
            $date=$match['Date'];
            $tournament=$match['Tournament'];
            $format=$match['Format'];

            if($player1Username==NULL)
            {
                $this->error="Missing Player 1 Username";
                return $this->error;
            }

            if($player1DeckName==NULL)
            {
                $this->error="Missing Player 1 Deck Name";
                return $this->error;
            }
            
            if($player1DeckLink==NULL)
            {
                $this->error="Missing Player 1 Deck Link";
                return $this->error;
            }

            if($player2Username==NULL)
            {
                $this->error="Missing Player 2 Username";
                return $this->error;
            }

            if($player2DeckName==NULL)
            {
                $this->error="Missing Player 2 Deck Name";
                return $this->error;
            }
            
            if($player2DeckLink==NULL)
            {
                $this->error="Missing Player 2 Deck Link";
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
            
            if($tournament==NULL)
            {
                $this->error="Missing Tournament";
                return $this->error;
            }
            
            if($format==NULL)
            {
                $this->error="Missing Format";
                return $this->error;
            }
            //Returns Error if Missing Critical Info
            
            $preparedStatement = $this->sql->prepare('
                    BEGIN;
                    INSERT INTO Matches (Wins,Losses,Ties,Date,Tournament,Format) VALUES (?,?,?,STR_TO_DATE(?,"%m-%d-%y"),?,?);
                    INSERT INTO MatchParts (MatchID,Username,DeckName,DeckLink,OrderedFirst) VALUES (LAST_INSERT_ID(),?,?,?,1);
                    INSERT INTO MatchParts (MatchID,Username,DeckName,DeckLink,OrderedFirst) VALUES (LAST_INSERT_ID(),?,?,?,0);
                    COMMIT;');//Prepares statement to inject ID into
  
            if($preparedStatement->bind_param("iiisssssssss",$wins,$losses,$ties,$date,$tournament,$format,$player1Username,$player1DeckName,$player1DeckLink,$player2Username,$player2DeckName,$player2DeckLink)==false)
            {
                $this->error = $this->sql->error;
                return $this->error;//Returns empty match and error string
            }//If didn't bind parameter
            
            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return $this->error;//Returns empty match and error string
            }//If Failed to Execute Query
            
            $preparedStatement->close();//Closes Statement
            
            return $this->error;//Returns empty error if successful
        }
        
        
        public function updateMatch($match)
        {
            if($this->sql==NULL)
            {
                $this->error="No Database Connection";
                return $this->error;
            }//If No Connection to Database
            
            if($this->sql->connect_error!=null)
            {
                $this->error=$this->sql->connect_error;
                
                return $this->error;
            }//If Connection Error
            
            $id=$match['ID'];
            $player1Username=$match['Player1Username'];
            $player1DeckName=$match['Player1DeckName'];
            $player1DeckLink=$match['Player1DeckLink'];
            $player2Username=$match['Player2Username'];
            $player2DeckName=$match['Player2DeckName'];
            $player2DeckLink=$match['Player2DeckLink'];
            $wins=$match['Wins'];
            $losses=$match['Losses'];
            $ties=($match['Ties']!=""?$match['Ties']:0);//Default 0 Ties is not specified
            $date=$match['Date'];
            $tournament=$match['Tournament'];
            $format=$match['Format'];

            if($id==NULL)
            {
                $this->error="Missing Match ID";
                return $this->error;
            }
            
            if($player1Username==NULL)
            {
                $this->error="Missing Player 1 Username";
                return $this->error;
            }

            if($player1DeckName==NULL)
            {
                $this->error="Missing Player 1 Deck Name";
                return $this->error;
            }
            
            if($player1DeckLink==NULL)
            {
                $this->error="Missing Player 1 Deck Link";
                return $this->error;
            }

            if($player2Username==NULL)
            {
                $this->error="Missing Player 2 Username";
                return $this->error;
            }

            if($player2DeckName==NULL)
            {
                $this->error="Missing Player 2 Deck Name";
                return $this->error;
            }
            
            if($player2DeckLink==NULL)
            {
                $this->error="Missing Player 2 Deck Link";
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
            
            if($tournament==NULL)
            {
                $this->error="Missing Tournament";
                return $this->error;
            }
            
            if($format==NULL)
            {
                $this->error="Missing Format";
                return $this->error;
            }
            //Returns Error if Missing Critical Info
            
            $preparedStatement = $this->sql->prepare('
                    BEGIN;
                    UPDATE Matches SET Wins=?,Losses=?,Ties=?,Date=STR_TO_DATE(?,"%m-%d-%y"),Tournament=?,Format=? WHERE ID=?;
                    UPDATE MatchParts SET Username=?,DeckName=?,DeckLink=? WHERE MatchID=? AND OrderedFirst=1;
                    UPDATE MatchParts SET Username=?,DeckName=?,DeckLink=? WHERE MatchID=? AND OrderedFirst=0;
                    COMMIT;');//Prepares statement to inject ID into
  
            if($preparedStatement->bind_param("iiisssisssisssi",$wins,$losses,$ties,$date,$tournament,$format,$id,$player1Username,$player1DeckName,$player1DeckLink,$id,$player2Username,$player2DeckName,$player2DeckLink,$id)==false)
            {
                $this->error = $this->sql->error;
                return $this->error;//Returns empty match and error string
            }//If didn't bind parameter
            
            if($preparedStatement->execute()==false)
            {
                $this->error=$preparedStatement->error;
                return $this->error;//Returns empty match and error string
            }//If Failed to Execute Query
            
            $preparedStatement->close();//Closes Statement
            
            return $this->error;//Returns empty error if successful
        }
        
        public function deleteMatch($id)
        {
            if($id==NULL)
            {
                $this->error="Invalid ID to Delete";
                return $this->error;
            }//If No ID Supplied
            
            if($this->sql==NULL)
            {
                $this->error="No Database Connection";
                return $this->error;
            }//If No Connection to Database
            
            if($this->sql->connect_error!=null)
            {
                $this->error=$this->sql->connect_error;
                return $this->error;
            }//If Connection Error
            
            if($this->sql->begin_transaction()==false)
            {     
                $this->error=$this->sql->error;
                return $this->error;
            }//If Failed to Begin Transaction
            
            $preparedStatement1 = $this->sql->prepare("DELETE FROM Matches WHERE ID=?;");//Prepares statement to inject ID into
            
            if($preparedStatement1->bind_param("i",$id)==false)
            {
                $this->error = $this->sql->error;
                return $this->error;//Returns empty match and error string
            }//If didn't bind parameter
            
            if($preparedStatement1->execute()==false)
            {
                $this->error=$preparedStatement1->error;
                return $this->error;//Returns empty match and error string
            }//If Failed to Execute Query
            
            $preparedStatement2 = $this->sql->prepare("DELETE FROM MatchParts WHERE MatchID=?;");//Prepares statement to inject ID into
            
            if($preparedStatement2->bind_param("i",$id)==false)
            {
                $this->error = $this->sql->error;
                return $this->error;//Returns empty match and error string
            }//If didn't bind parameter
            
            if($preparedStatement2->execute()==false)
            {
                $this->error=$preparedStatement2->error;
                return $this->error;//Returns empty match and error string
            }//If Failed to Execute Query
            
            if($this->sql->commit()==false);
            {
                $this->error=$this->sql->error;
                return $this->error;
            }//If Failed to End Transaction
            
            $preparedStatement1->close();//Closes Statement
            $preparedStatement2->close();//Closes Statement
            
            return $this->error;//Returns empty error if successful
        }
        
    }
?>