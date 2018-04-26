<?php
    //Model controls all data
    class Model
    {
        private $sql;
        private $error;
        
        public function __construct()
        {
            require("Credentials.php");//Required Database Credentials
            $sql = new mysqli($server,$user,$password,$database);//Connects to Database
            $error = $sql->connect_error;//Sets Error
        }//Constructor for Model
        
        public function __destruct()
        {
            if($sql)
            {
                $sql->close();//Closes connection
            }//If SQL Connection exists
            $sql=null;
        }//Destructor for Model
        
        public function readMatches()
        {
            $matches=array();//Array to Store all Matches
            if($sql->connect_error!=null)
            {
                $error=$sql->connect_error;
                return array($matches,$error);//Returns Empty matches and Error
            }//If Connection Error
            
            $results = $sql->$query('
                    SELECT Matches.ID, Matches.Format, Player1.FirstName AS "First Name", Player1.LastName AS "Last Name", Player2.FirstName AS "Opponent First Name",Player2.LastName AS "Opponent Last Name", Player1Deck.Name AS "Deck", Player2Deck.Name AS "Opponent Deck", Matches.Wins ,Matches.Losses, Matches.Ties, Matches.Date,Matches.Tournament 
                    FROM Matches
                    JOIN Players AS Player1 ON Matches.Player1ID = Player1.ID
                    JOIN Players AS Player2 ON Matches.Player2ID = Player2.ID
                    JOIN Decks AS Player1Deck ON Matches.Player1DeckID = Player1Deck.ID
                    JOIN Decks AS Player2Deck ON Matches.Player2DeckID = Player2Deck.ID; ');//Queries SQL Database to Read all Matches
            
            if($results==null)
            {
                $error=$sql->error;
                return array($matches,$error);//Returns Empty matches and Error
            }//If Query Errored
            
            if($result->num_rows > 0)
            {
                for($record=$results->fetch_assoc();$record!=null;$record=$results->fetch_assoc())
                {
                    array_push($matches,$record);//Adds record to output
                }//For each record
            }//If result has atleast 1 record
            
            $results->close();//Closes results
            
            return array($matches,$error);//Returns matches and possible error string
        }//Reads Matches from Database
        
        public function readMatch($id)
        {
            $match=NULL;//Holds Match to be Returned
            if($id==null)
            {
                $error="No ID Provided";
                return array($match,$error);//Returns empty match and error string
            }//If No ID
            
            if($sql->connect_error!=null)
            {
                $error=$sql->connect_error;
                return array($match,$error);//Returns empty match and error string
            }//If Connection Error
            
                
                $preparedStatement = $sql->prepare('
                        SELECT Matches.Format, Player1.FirstName AS "First Name", Player1.LastName AS "Last Name", Player2.FirstName AS "Opponent First Name",Player2.LastName AS "Opponent Last Name", Player1Deck.Name AS "Deck", Player2Deck.Name AS "Opponent Deck", Matches.Wins ,Matches.Losses, Matches.Ties, Matches.Date,Matches.Tournament 
                        FROM Matches
                        JOIN Players AS Player1 ON Matches.Player1ID = Player1.ID
                        JOIN Players AS Player2 ON Matches.Player2ID = Player2.ID
                        JOIN Decks AS Player1Deck ON Matches.Player1DeckID = Player1Deck.ID
                        JOIN Decks AS Player2Deck ON Matches.Player2DeckID = Player2Deck.ID
                        WHERE Matches.ID = ?; ');//Prepares statement to inject ID into
                    if($preparedStatement->bind_param("i",$id)==true)
                    {
                        if($preparedStatement->execute()==true)
                        {
                            $result=$preparedStatement->get_result();//Gets Result from Query
                            if($result!=null)
                            {
                                if($result->num_rows==1)
                                {
                                    $match=$result->fetch_assoc();//Gets Match with given ID
                                }//If Exactly One Result
                                $preparedStatement->close();//Closes Statement
                            }//If Result Retrieved
                            else
                            {
                                $error=$preparedStatement->error;
                            }//Else Failed to Retrieve Result   
                        }//If Successfully Executed Query
                        else
                        {
                            $error=$preparedStatement->error;
                        }//Else Failed to Execute Query
                    }//If Successfully Bound Parameter to Prepared Statement
                    else
                    {
                        $error = $sql->error;
                    }//Else didn't bind parameter
                }//If No Connection Error
            
            
            
            return array($match,$error);//Returns matches and possible error string   
        }
        
        public function getError()
        {
            return $error;//Returns Connection Error
        }
        
        public function addMatch($match)
        {
            if($sql->connect_error!=null)
            {
                $error=$sql->connect_error;
                return $error;
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
                $error="Missing Player 1 First Name";
                return $error;
            }

            if($player1DeckName==NULL)
            {
                $error="Missing Player 1 Deck Name";
                return $error;
            }

            if($player2DeckName==NULL)
            {
                $error="Missing Player 2 Deck Name";
                return $error;
            }

            if($wins==NULL)
            {
                $error="Missing Wins";
                return $error;
            }

            if($losses==NULL)
            {
                $error="Missing Losses";
                return $error;
            }

            if($date==NULL)
            {
                $error="Missing Date";
                return $error;
            }
            //Returns Error if Missing Critical Info
            
            
            
            
            
            
        }
    }
?>