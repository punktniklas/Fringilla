<?php require 'db.php';?>
<?php require 'assertadmin.php';?>
<?php
  $edituser = $_GET["edituser"];
  $editday = $_GET["editday"];
?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Ändra tips <?php echo $editday; ?> för <?php echo $edituser; ?></title>
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

    <h1>Ändra tips <?php echo $editday; ?> för <?php echo $edituser; ?></h1>
    
    <h2>Matcher</h2>
    <form action="admin_postbet.php" method="POST">
    <input type="hidden" name="edituser" value="<?php echo $edituser;?>"/>
    <table>
      <tr><th colspan=2>Hemma</th><th></th><th colspan=2>Borta</th><th></th><th>Radera</th></tr>
<?php

  $stmt = $conn->prepare(
    "SELECT g.GameId, h.Code AS Home, a.Code AS Away, b.Winner, g.StartTime < NOW() AS TooLate " .
    "FROM Games g " .
    "JOIN Teams h ON (h.TeamId = g.Home) " .
    "JOIN Teams a ON (a.TeamId = g.Away) " .
    "LEFT OUTER JOIN Bets b ON(b.GameId = g.GameId AND b.UserId = ?)" .
    "WHERE Date = ? " .
    "ORDER BY OrderInDay;");
  $stmt->bind_param("ss", $edituser, $editday);
  $stmt->execute();
  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    $gameid = $row["GameId"];
    $winner = $row["Winner"];
    echo "<tr>\n";
    echo "<td>" . $row["Home"] . "</td>\n";
    echo "<td><input type='radio' name='winner-$gameid' value='1'" . ($winner == 1 ? " checked=true" : "") . "/></td>\n";
    echo "<td>-</td>\n";    
    echo "<td><input type='radio' name='winner-$gameid' value='2'" . ($winner == 2 ? " checked=true" : "") . "/></td>\n";
    echo "<td>" . $row["Away"] . "</td>\n";
    echo "<td>|</td>\n";    
    echo "<td style='text-align: center; vertical-align: middle;'><input type='radio' name='winner-$gameid' value='0'/></td>\n";
    echo "</tr>\n";
  }
?>
    </table>
    <input type='submit' value='Ändra'>
    </form>

<?php
  $invalidlines = array_key_exists("invalidlines", $_GET) ? $_GET["invalidlines"] : "";

  if(!empty($invalidlines)) {
    echo "<p class='errortext'>Kunde inte tolka följande rader:</p>\n";
    echo "<ul class='errortext'>\n";
    foreach(explode('#', $invalidlines) as $line) {
      echo "<li>$line</li/>\n";
    }
    echo "</ul>\n";
  }
?>

    <p/>
    <h3>Importera tips från bloggen</h3>
    <form action="admin_importbet.php" method="POST">
      <input type="hidden" name="edituser" value="<?php echo $edituser;?>"/>
      <input type="hidden" name="editday" value="<?php echo $editday;?>"/>
      <textarea name="betstr" rows="20" cols="50"></textarea><br/>
      <input type='submit' value='Importera'>
    </form>

</td>
</tr>
</table>
</body>
</html>
