<?php
    //Model controls all data
    class Model
    {
        private $sql;
        
        public function __construct()
        {
            require("Credentials.php");//Required Database Credentials
            $sql = new mysqli($server,$user,$password,$database);//Connects to Database
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
            $error="";//Holds error string
            if($sql->connect_error==null)
            {
                $results = $sql->$query('
                    SELECT Player1.FirstName AS "First Name", Player1.LastName AS "Last Name", Player1Deck.Name AS "Deck", Player2.FirstName AS "Opponent First Name",Player2.LastName AS "Opponent Last Name", Player2Deck.Name AS "Opponent Deck", Matches.Wins ,Matches.Losses, Matches.Ties, Matches.Date,Matches.Tournament 
                    FROM Matches
                    JOIN Players AS Player1 ON Matches.Player1ID = Player1.ID
                    JOIN Players AS Player2 ON Matches.Player2ID = Player2.ID
                    JOIN Decks AS Player1Deck ON Matches.Player1DeckID = Player1Deck.ID
                    JOIN Decks AS Player2Deck ON Matches.Player2DeckID = Player2Deck.ID;');//Queries SQL Database to Read all Matches
                if($results!=null)
                {
                    if($result->num_rows > 0)
                    {
                        for($record=$results->fetch_assoc();$record!=null;$record=$results->fetch_assoc())
                        {
                            array_push($matches,$record);//Adds record to output
                        }//For each record
                    }//If result has atleast 1 record
                    $results->close();//Closes results
                }//If Query had Results
                else
                {
                    $error=$sql->error;
                }//Else Query Errored
            }//If No Connection Error
            else
            {
                $error=$sql->connect_error;
            }//Else Connection Error
            return array($matches,$error);//Returns matches and possible error string
        }//Reads Matches from Database
        
        public function readMatch($id)
        {
            $match=NULL;//Holds Match to be Returned
            $error="";//Holds error string
            if($id!=null)
            {
                if($sql->connect_error==null)
                {
                    $preparedStatement = $sql->prepare('
                        SELECT Player1.FirstName AS "First Name", Player1.LastName AS "Last Name", Player1Deck.Name AS "Deck", Player2.FirstName AS "Opponent First Name",Player2.LastName AS "Opponent Last Name", Player2Deck.Name AS "Opponent Deck", Matches.Wins ,Matches.Losses, Matches.Ties, Matches.Date,Matches.Tournament 
                        FROM Matches
                        JOIN Players AS Player1 ON Matches.Player1ID = Player1.ID
                        JOIN Players AS Player2 ON Matches.Player2ID = Player2.ID
                        JOIN Decks AS Player1Deck ON Matches.Player1DeckID = Player1Deck.ID
                        JOIN Decks AS Player2Deck ON Matches.Player2DeckID = Player2Deck.ID
                        WHERE Matches.ID = ?;');//Prepares statement to inject ID into
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
                else
                {
                    $error=$sql->connect_error;
                }//Else Connection Error
            }//If ID Provided
            else
            {
                $error="No ID Provided";
            }//Else No ID
            return array($match,$error);//Returns matches and possible error string   
        }
        
        public function getError()
        {
            return $sql->connect_error;//Returns Connection Error
        }
        
        
        
        
    }
?>