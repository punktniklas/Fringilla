<?php require 'db.php';?>
<?php require 'utils.php';?>
<?php $season = $_GET["season"]; ?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Flest tippade matcher</title>
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

<h1>Flest tippade matcher <?php echo formatSeason($season); ?></h1>
    
<table class="infotable">
<?php

  $stmt = $conn->prepare(
    "SELECT u.Name, COUNT(*) AS NumBets, (SELECT COUNT(*) FROM Games WHERE SeasonId = ? AND Winner IS NOT NULL) AS NumGames " .
    "FROM Bets b " .
    "JOIN Games g USING (GameId) " .
    "JOIN Users u USING (UserId) " .
    "WHERE g.SeasonId = ? AND g.Winner IS NOT NULL " .
    "GROUP BY UserId " .
    "ORDER BY NumBets DESC");
  $stmt->bind_param("ss", $season, $season);
  $stmt->execute();
  $result = $stmt->get_result();

  echo "<tr><th>Namn</th><th>Antal tips&nbsp;</th><th>Andel</th></tr>\n";
  while($row = $result->fetch_assoc()) {
    echo "<tr><td class='infotablecellwithpad'>" . $row["Name"] . "</td><td>" . $row["NumBets"] . "</td><td>" . ((int)($row["NumBets"] * 100 / $row["NumGames"])) . "%</td></tr>\n";
  }
?>
</table>

</td>
</tr>
</table>
</body>
</html>
