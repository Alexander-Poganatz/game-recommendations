CREATE TABLE games
(
	ID INT NOT NULL AUTO_INCREMENT,
	Title VARCHAR(75) NOT NULL,
	Developer VARCHAR(30) NOT NULL,
	Publisher VARCHAR(30) NOT NULL,
	Score INT NOT NULL,
	Comment VARCHAR(128),
	PRIMARY KEY(ID)
)
