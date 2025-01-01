<?php require 'db.php';?>
<?php $day = $_GET["day"]; ?>
<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Resultat <?php echo $day; ?></title>
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

    <h1>Resultat <?php echo $day; ?></h1>
    
    <h2>Matcher</h2>
    <table class="infotable">
    <tr><th colspan=3>Lag</th><th colspan=4>Resultat</th><th colspan=4>Alla tips</th><?php echo !empty($user) ? "<th>Ditt tips</th>" : "" ?></tr>

<?php
  $stmt = $conn->prepare(
    "SELECT " .
    "h.Code AS Home, " .
    "a.Code AS Away, " .
    "g.Winner AS GameWinner, " .
    "b.Winner AS BetWinner, " .
    "g.HomeGoals, " .
    "g.AwayGoals, " .
    "g.GameOutcome, " .
    "(SELECT COUNT(*) FROM Bets WHERE GameId = g.GameId) AS NumBetsOnGame, " .
    "(SELECT COUNT(*) FROM Bets WHERE GameId = g.GameId AND Winner = g.Winner) AS CorrectBetsOnGame " .
    "FROM Games g " .
    "JOIN Teams h ON (h.TeamId = g.Home) " .
    "JOIN Teams a ON (a.TeamId = g.Away) " .
    "LEFT OUTER JOIN Bets b ON(b.GameId = g.GameId AND b.UserId = ?)" .
    "WHERE Date = ? " .
    "ORDER BY OrderInDay;");
  $stmt->bind_param("ss", $user, $day);
  $stmt->execute();
  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    $numBetsOnGame = $row["NumBetsOnGame"];
    $correctBetsOnGame = $row["CorrectBetsOnGame"];
    echo "<tr><td>" . $row["Home"] . "</td>";
    echo "<td>-</td>";
    echo "<td class='infotablecellwithpad'>" . $row["Away"] . "</td>";
    echo "<td>" . $row["HomeGoals"] . "</td>";
    echo "<td>-</td>";
    echo "<td>" . $row["AwayGoals"] . "</td>";
    echo "<td class='infotablecellwithpad'>" . ($row["GameOutcome"] != "REG" ? $row["GameOutcome"] : "") . "</td>";
    echo "<td>$correctBetsOnGame</td>";
    echo "<td>/</td>";
    echo "<td>$numBetsOnGame</td>";
    echo "<td class='infotablecellwithpad'>(" . ((int)($correctBetsOnGame * 100 / $numBetsOnGame)) . "%)</td>";
    if(!empty($user) && $row["GameWinner"] != NULL) {
      if($row["BetWinner"] != NULL) {
        echo "<td>" . ($row["GameWinner"] == $row["BetWinner"] ? "&#x2705" : "&#x274c") . "</td>";
      } else {
        echo "<td>&#x2796</td>";
      }
    }
    echo "</tr>\n";
  }
?>
</table>

    <h2>Tippning</h2>
    <table>
<?php

  $stmt = $conn->prepare(
    "SELECT u.Name, dr.Points, dr.Gold, dr.Lollipop " .
    "FROM DayResults dr JOIN Users u USING (UserId) " .
    "WHERE dr.Date = ? " .
    "ORDER BY Date, Points DESC;");
  $stmt->bind_param("s", $day);
  $stmt->execute();
  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row["Name"] . "</td><td>" . $row["Points"] . "</td>";
    if($row["Gold"]) {
      echo "<td>&#x1f947</td>";
    }
    if($row["Lollipop"]) {
      echo "<td>&#x1f36d</td>";
    }
    echo "</tr>\n";
  }
?>
    </table>
</td>
</tr>
</table>
</body>
</html>
