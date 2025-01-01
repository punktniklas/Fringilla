CREATE TABLE Teams (
  TeamId INT NOT NULL AUTO_INCREMENT,
  Code VARCHAR(3) NOT NULL,
  Name VARCHAR(50) NOT NULL,
  PRIMARY KEY (TeamId)
);

INSERT INTO Teams(Code, Name) VALUES('CAR', 'Carolina Hurricanes');
INSERT INTO Teams(Code, Name) VALUES('CBJ', 'Columbus Blue Jackets');
INSERT INTO Teams(Code, Name) VALUES('NJD', 'New Jersey Devils');
INSERT INTO Teams(Code, Name) VALUES('NYI', 'New York Islanders');
INSERT INTO Teams(Code, Name) VALUES('NYR', 'New York Rangers');
INSERT INTO Teams(Code, Name) VALUES('PHI', 'Philadelphia Flyers');
INSERT INTO Teams(Code, Name) VALUES('PIT', 'Pittsburgh Penguins');
INSERT INTO Teams(Code, Name) VALUES('WSH', 'Washington Capitals');
INSERT INTO Teams(Code, Name) VALUES('BOS', 'Boston Bruins');
INSERT INTO Teams(Code, Name) VALUES('BUF', 'Buffalo Sabres');
INSERT INTO Teams(Code, Name) VALUES('DET', 'Detroit Red Wings');
INSERT INTO Teams(Code, Name) VALUES('FLA', 'Florida Panthers');
INSERT INTO Teams(Code, Name) VALUES('MTL', 'Montréal Canadiens');
INSERT INTO Teams(Code, Name) VALUES('OTT', 'Ottawa Senators');
INSERT INTO Teams(Code, Name) VALUES('TBL', 'Tampa Bay Lightning');
INSERT INTO Teams(Code, Name) VALUES('TOR', 'Toronto Maple Leafs');
INSERT INTO Teams(Code, Name) VALUES('CHI', 'Chicago Blackhawks');
INSERT INTO Teams(Code, Name) VALUES('COL', 'Colorado Avalanche');
INSERT INTO Teams(Code, Name) VALUES('DAL', 'Dallas Stars');
INSERT INTO Teams(Code, Name) VALUES('MIN', 'Minnesota Wild');
INSERT INTO Teams(Code, Name) VALUES('NSH', 'Nashville Predators');
INSERT INTO Teams(Code, Name) VALUES('STL', 'St. Louis Blues');
INSERT INTO Teams(Code, Name) VALUES('UTA', 'Utah Hockey Club');
INSERT INTO Teams(Code, Name) VALUES('WPG', 'Winnipeg Jets');
INSERT INTO Teams(Code, Name) VALUES('ANA', 'Anaheim Ducks');
INSERT INTO Teams(Code, Name) VALUES('CGY', 'Calgary Flames');
INSERT INTO Teams(Code, Name) VALUES('EDM', 'Edmonton Oilers');
INSERT INTO Teams(Code, Name) VALUES('LAK', 'Los Angeles Kings');
INSERT INTO Teams(Code, Name) VALUES('SJS', 'San Jose Sharks');
INSERT INTO Teams(Code, Name) VALUES('SEA', 'Seattle Kraken');
INSERT INTO Teams(Code, Name) VALUES('VAN', 'Vancouver Canucks');
INSERT INTO Teams(Code, Name) VALUES('VGK', 'Vegas Golden Knights');

CREATE TABLE Seasons (
  SeasonId VARCHAR(10) NOT NULL,
  SeasonType ENUM('PreSeason', 'Regular', 'PlayOff'),
  PRIMARY KEY (SeasonId)
);

INSERT INTO Seasons(SeasonId, SeasonType) VALUES('20242025-2', 'Regular');

CREATE TABLE Games (
  GameId INT NOT NULL AUTO_INCREMENT,
  SeasonId VARCHAR(10) NOT NULL,
  NhlId VARCHAR(20) NOT NULL,
  Home INT NOT NULL,
  Away INT NOT NULL,
  Date DATE NOT NULL,
  StartTime TIMESTAMP NOT NULL,
  OrderInDay INT NOT NULL,
  Winner INT,
  HomeGoals INT,
  AwayGoals INT,
  GameOutcome ENUM('REG', 'OT', 'SO'),
  PRIMARY KEY (GameId),
  FOREIGN KEY (SeasonId) REFERENCES Seasons(SeasonId),
  FOREIGN KEY (Home) REFERENCES Teams(TeamId),
  FOREIGN KEY (Away) REFERENCES Teams(TeamId)
);
CREATE UNIQUE INDEX GamesNhlId ON Gamess(NhlId);

CREATE TABLE Users (
  UserId VARCHAR(20) NOT NULL,
  Password VARCHAR(256) NOT NULL,
  Name VARCHAR(50) NOT NULL,
  Email VARCHAR(50),
  IsAdmin BOOLEAN,
  ResetCode VARCHAR(50),
  PRIMARY KEY (UserId)
);

CREATE TABLE Bets (
  UserId VARCHAR(20) NOT NULL,
  GameId INT NOT NULL,
  Winner INT NOT NULL,
  CONSTRAINT PK_Bet PRIMARY KEY (UserId, GameId),
  FOREIGN KEY (UserId) REFERENCES Users(UserId) ON DELETE CASCADE,
  FOREIGN KEY (GameId) REFERENCES Games(GameId) ON DELETE CASCADE
);

CREATE VIEW DayResults AS
SELECT
  SeasonId,
  Date,
  UserId,
  Points,
  Points = (MAX(Points) OVER(PARTITION BY Date)) AS Gold,
  (Points = (MIN(Points) OVER(PARTITION BY Date))) AND (Points != (MAX(Points) OVER(PARTITION BY Date)))  AS Lollipop
FROM
  (SELECT SeasonId, Date, UserId, SUM(b.Winner = g.Winner) AS Points
  FROM Bets b
  JOIN Games g USING (GameId)
  WHERE g.Winner IS NOT NULL
  GROUP BY g.SeasonId, g.Date, UserId) AS x
ORDER BY Date, Points DESC;
