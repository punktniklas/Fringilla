<?php require 'db.php';?>
<?php require 'utils.php';?>
<?php $season = $_GET["season"]; ?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Totalen</title>
   <link rel="stylesheet" type="text/css" media="screen" href="fringilla.css" />
</head>
<body>
<table cellpadding="0" cellspacing="0">
  <tr><td colspan="2" id="top">
    <?php require 'top.php';?>
  </td></tr>
  <tr>
    <td valign="top" id="menu" class="menutext">
      <?php require 'menu.php';?>
    </td>
    <td valign="top" id="main">

<h1>Totalen <?php echo formatSeason($season); ?></h1>
    
<table class="infotable">
<?php

  $stmt = $conn->prepare(
    "SELECT u.Name, SUM(b.Winner = g.Winner) AS Points, COUNT(*) AS Bets, SUM(b.Winner = g.Winner) / COUNT(*) AS BetRatio " .
    "FROM Bets b " .
    "JOIN Games g USING (GameId) " .
    "JOIN Users u USING (UserId) " .
    "WHERE g.SeasonId = ? AND g.Winner IS NOT NULL " .
    "GROUP BY UserId " .
    "ORDER BY Points DESC");
  $stmt->bind_param("s", $season);
  $stmt->execute();
  $result = $stmt->get_result();

  echo "<tr><th>Namn</th><th>Antal rätt&nbsp;</th><th>Tippade matcher&nbsp;</th><th>Andel rätt</th></tr>\n";
  while($row = $result->fetch_assoc()) {
    echo "<tr><td class='infotablecellwithpad'>" . $row["Name"] . "</td><td>" . $row["Points"] . "</td><td>" . $row["Bets"] . "</td><td>" . ((int)($row["BetRatio"] * 100)) . "%</td></tr>\n";
  }
?>
</table>

<h2>Sammanlagt</h2>
    
<table class="infotable">
<?php

  $stmt = $conn->prepare(
    "SELECT SUM(b.Winner = g.Winner) AS Points, COUNT(*) AS Bets, SUM(b.Winner = g.Winner) / COUNT(*) AS BetRatio " .
    "FROM Bets b " .
    "JOIN Games g USING (GameId) " .
    "WHERE g.SeasonId = ? AND g.Winner IS NOT NULL ");
  $stmt->bind_param("s", $season);
  $stmt->execute();
  $result = $stmt->get_result();

  echo "<tr><th>Antal rätt&nbsp;</th><th>Tippade matcher&nbsp;</th><th>Andel rätt</th></tr>\n";
  while($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row["Points"] . "</td><td>" . $row["Bets"] . "</td><td>" . ((int)($row["BetRatio"] * 100)) . "%</td></tr>\n";
  }
?>
</table>

</td>
</tr>
</table>
</body>
</html>
