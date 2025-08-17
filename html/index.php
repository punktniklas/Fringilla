<?php require 'db.php';?>
<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>NHL-bloggens tips</title>
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

    <h2>Tipsresultat</h2>

<?php
$sql = "SELECT DISTINCT Date FROM Games WHERE Winner IS NOT NULL ORDER BY Date DESC LIMIT 1";
$result = $conn->query($sql);

if($row = $result->fetch_assoc()) {
  echo "Klicka här för <a href='dayresults.php?day=" . $row["Date"] . "'>senaste resultat</a> (" . $row["Date"] . ")<br/>\n";
  echo "Klicka här för <a href='allresults.php'>övriga dagars resultat</a><p/>\n";
} else {
  echo "Inga resultat än.<p/>";
}
?>

<h2>Tippa</h2>

<?php
if(!empty($user)) {
  date_default_timezone_set("America/Los_Angeles");
  $today = date("Y-m-d");
  $sql = "SELECT Date, Date = '$today' AS IsToday FROM Games WHERE Date >= '$today' ORDER BY Date LIMIT 1";
  $result = $conn->query($sql);

  if($row = $result->fetch_assoc()) {
    if($row["IsToday"]) {
      echo "Klicka här för att <a href='bet.php?day=$today'>tippa dagens matcher</a> ($today)<br/>\n";
    } else {
      echo "Klicka här för att <a href='bet.php?day=" . $row["Date"] . "'>tippa närmaste tipsdag</a> (" . $row["Date"] . ")<br/>\n";
    }
  } else {
    echo "Inga kommande matcher att tippa.<br/>\n";
  }
  echo "Klicka här för <a href='allbetdays.php'>övriga tipsdagar</a><p/>\n";
  } else {
    echo "Logga in för att tippa.<p/>";
  }
?>

<h2>Topp 10</h2>
    
<table>
<?php

  $stmt = $conn->prepare(
    "SELECT u.Name, SUM(b.Winner = g.Winner) AS Points " .
    "FROM Bets b " .
    "JOIN Games g USING (GameId) " .
    "JOIN Users u USING (UserId) " .
    "WHERE g.SeasonId = ? AND g.Winner IS NOT NULL " .
    "GROUP BY UserId " .
    "ORDER BY Points DESC " .
    "LIMIT 10;");
  $stmt->bind_param("s", $currentSeason);
  $stmt->execute();
  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    echo "<tr><td class='infotablecellwithpad'>" . $row["Name"] . "</td><td>" . $row["Points"] . "</td></tr>";
  }
?>
</table>

    </td>
  </tr>
</table>
</body>
</html>
