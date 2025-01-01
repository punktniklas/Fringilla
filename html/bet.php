<?php require 'db.php';?>
<?php $day = $_GET["day"]; ?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Tippa <?php echo $day; ?></title>
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

    <h1>Tippa <?php echo $day; ?></h1>
    
    <h2>Matcher</h2>
    <form action="postbet.php" method="POST">
    <input type="hidden" name="day" value="<?php echo $day;?>"/>
    <table>
      <tr><th colspan=2>Hemma</th><th></th><th colspan=2>Borta</th><th></th><th>Tid kvar att tippa</th></tr>
<?php

  $stmt = $conn->prepare(
    "SELECT g.GameId, h.Code AS Home, a.Code AS Away, b.Winner, g.StartTime < NOW() AS TooLate, TIMESTAMPDIFF(SECOND, NOW(), g.StartTime) AS SecondsLeft " .
    "FROM Games g " .
    "JOIN Teams h ON (h.TeamId = g.Home) " .
    "JOIN Teams a ON (a.TeamId = g.Away) " .
    "LEFT OUTER JOIN Bets b ON(b.GameId = g.GameId AND b.UserId = ?)" .
    "WHERE Date = ? " .
    "ORDER BY OrderInDay;");
  $stmt->bind_param("ss", $user, $day);
  $stmt->execute();
  $result = $stmt->get_result();

  $showButton = FALSE;
  while($row = $result->fetch_assoc()) {
    $gameid = $row["GameId"];
    $winner = $row["Winner"];
    $toolate = $row["TooLate"];
    if(!$toolate) {
        $showButton = TRUE;
    }
    echo "<tr>\n";
    echo "<td>" . $row["Home"] . "</td>\n";
    echo "<td><input type='radio' name='winner-$gameid' value='1'" . ($winner == 1 ? " checked=true" : "") . ($toolate ? " disabled=true" : "") . "/></td>\n";
    echo "<td>-</td>\n";    
    echo "<td><input type='radio' name='winner-$gameid' value='2'" . ($winner == 2 ? " checked=true" : "") . ($toolate ? " disabled=true" : "") . "/></td>\n";
    echo "<td>" . $row["Away"] . "</td>\n";
    echo "<td>:</td>\n";    
    echo "<td>&nbsp;" . ($toolate ? "För sent" : formatTimeLeft($row["SecondsLeft"])) . "</td>\n";
    echo "</tr>\n";
  }

function formatTimeLeft($seconds) {
  if($seconds < 60) {
    return "$seconds s";
  }
  $minutes = (int)($seconds / 60);
  $seconds = $seconds % 60;
  if($minutes < 60) {
    return "$minutes m $seconds s";
  }
  $hours = (int)($minutes / 60);
  $minutes = $minutes % 60;
  if($hours < 24) {
    return "$hours h $minutes m";
  }
  $days = (int)($hours / 24);
  $hours = ($hours % 24);
  return "$days d $hours h";
}
?>
    </table>

<?php
  if($showButton && !empty($user)) {
    echo "<input type='submit' value='Tippa'>\n";
  }
?>

    </form>

</td>
</tr>
</table>
</body>
</html>
