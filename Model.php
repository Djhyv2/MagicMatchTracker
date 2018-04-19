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
        
        public function read()
        {
            if($sql->connect_error==null)
            {
                $matches=array();//Array to Store all Matches
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
                
            }//If No Connection Error
            return $matches;//Returns matches
        }//Reads Matches from Database
        
        public function getError()
        {
            return $sql->connect_error;//Returns Connection Error
        }
        
        
        
        
    }
?>