# README

### Creators
Magic Match Tracker was created during the Spring 2018 semester. Magic Match Tracker was created by Dustin Hengel, Mercy Housh, Connor Fitzmaurice, and Scott Watkins.

### About the application
This application is used to keep track of games played in Magic: The Gathering. It can be used for someone to review what decks they have used in what circumstances and how they might change playstyles in the future. It also could be used to see what cards you should buy for your decks by reviewing the deck list of someone you have played against. It provides you with a history of games you have played which you could use for data such as win rates, weaknesses of your decks, and what strengths you have.

### Schema
Matches: ID (PK), Wins, Losses, Ties, Date, Tournament, Format  
MatchParts: MatchID (PK), Username (PK), DeckName (PK), DeckLink (PK), OrderedFirst

### Entity Relationship Diagram
![ERD](https://github.com/Djhyv2/MagicMatchTracker/blob/master/MagicMatchUML.png)

### CRUD
The app creates when you click +Add A Match and you enter your data. It reads when you go on the main page of the site and it shows you a list of all the matches there are in the system. It updates when you click Edit on a task and change data. It deletes when you click Delete on a task and remove it from the database.

### Video Demonstration
[Youtube Video](https://www.youtube.com/watch?v=NsHr0KPEpnw&feature=youtu.be)
