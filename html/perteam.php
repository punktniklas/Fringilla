<?php require 'db.php';?>
<?php require 'utils.php';?>
<?php $season = $_GET["season"]; ?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Tips per lag</title>
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


    <h1>Tips per lag <?php echo formatSeason($season); ?></h1>
    Hur ofta tips för varje lag är rätt.

    <h2>Allas tips</h2>
    <table>
<?php

  $stmt = $conn->prepare(
    "SELECT TeamId, t.Code, SUM(Correct) Corrects, COUNT(*) Total, SUM(Correct) / COUNT(*) Perc " .
    "FROM " .
    "  ((SELECT g.Home AS TeamId, g.Winner = b.Winner AS Correct " .
    "  FROM Games g JOIN Bets b USING(GameId) " .
    "  WHERE g.Winner IS NOT NULL " .
    "  AND g.SeasonId = ?) " .
    "  UNION ALL " .
    "  (SELECT g.Away AS TeamId, g.Winner = b.Winner AS Correct " .
    "  FROM Games g JOIN Bets b USING(GameId) " .
    "  WHERE g.Winner IS NOT NULL " .
    "  AND g.SeasonId = ?)) u " .
    "JOIN Teams t USING(TeamId) " .
    "GROUP BY TeamId " .
    "ORDER BY Perc DESC;");
  $stmt->bind_param("ss", $season, $season);
  $stmt->execute();
  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    $corrects = $row["Corrects"];
    $total = $row["Total"];
    echo "<tr>";
    echo "<td class='infotablecellwithpad'>" . $row["Code"] . "</td>";
    echo "<td class='infotablecellwithpad'>$corrects / $total</td>";
    echo "<td>" . ((int)($corrects * 100 / $total)) ." %</td>";
    echo "</tr>\n";
  }
?>
</table>

<h2>Dina tips</h2>

<?php
if(empty($user)) {
  echo "Logga in för att se dina personliga tips.\n";
} else {
  echo "<table>\n";
  $stmt = $conn->prepare(
    "SELECT TeamId, t.Code, SUM(Correct) Corrects, COUNT(*) Total, SUM(Correct) / COUNT(*) Perc " .
    "FROM " .
    "  ((SELECT g.Home AS TeamId, g.Winner = b.Winner AS Correct " .
    "  FROM Games g JOIN Bets b USING(GameId) " .
    "  WHERE g.Winner IS NOT NULL " .
    "  AND g.SeasonId = ? " .
    "  AND b.UserId = ?) " .
    "  UNION ALL " .
    "  (SELECT g.Away AS TeamId, g.Winner = b.Winner AS Correct " .
    "  FROM Games g JOIN Bets b USING(GameId) " .
    "  WHERE g.Winner IS NOT NULL " .
    "  AND g.SeasonId = ? " .
    "  AND b.UserId = ?)) u " .
    "JOIN Teams t USING(TeamId) " .
    "GROUP BY TeamId " .
    "ORDER BY Perc DESC;");
  $stmt->bind_param("ssss", $season, $user, $season, $user);
  $stmt->execute();
  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    $corrects = $row["Corrects"];
    $total = $row["Total"];
    echo "<tr>";
    echo "<td class='infotablecellwithpad'>" . $row["Code"] . "</td>";
    echo "<td class='infotablecellwithpad'>$corrects / $total</td>";
    echo "<td>" . ((int)($corrects * 100 / $total)) ." %</td>";
    echo "</tr>\n";
  }
  echo "</table>\n";
}
?>

</td>
</tr>
</table>
</body>
</html>
