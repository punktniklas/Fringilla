<?php require 'db.php';?>
<?php require 'assertadmin.php';?>
<?php $editday = $_GET["editday"]; ?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Ändra matchresultat <?php echo $editday; ?></title>
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

    <h1>Ändra matchresultat <?php echo $editday; ?></h1>
    
    <h2>Matcher</h2>
    <table>
<?php

  $stmt = $conn->prepare(
    "SELECT g.GameId, h.Code AS Home, a.Code AS Away, g.HomeGoals, g.AwayGoals, g.GameOutcome " .
    "FROM Games g " .
    "JOIN Teams h ON (h.TeamId = g.Home) " .
    "JOIN Teams a ON (a.TeamId = g.Away) " .
    "WHERE Date = ? " .
    "ORDER BY OrderInDay;");
  $stmt->bind_param("s", $editday);
  $stmt->execute();
  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    $gameId = $row["GameId"];
    $homeGoals = $row["HomeGoals"];
    $awayGoals = $row["AwayGoals"];
    $gameOutcome = $row["GameOutcome"];
    echo "<tr><form action='admin_postscore.php' method='POST'>\n";
    echo "<input type='hidden' name='gameId' value='$gameId' />\n";
    echo "<td>" . $row["Home"] . "</td>\n";
    echo "<td><input type='text' name='homeGoals' value='$homeGoals'/></td>\n";
    echo "<td>-</td>\n";    
    echo "<td><input type='text' name='awayGoals' value='$awayGoals'/></td>\n";
    echo "<td>" . $row["Away"] . "</td>\n";
    echo "<td><select name='gameOutcome'>\n";
    echo "<option value='REG'" . ($gameOutcome == "REG" ? " selected=true" : "") . ">REG</option>\n";
    echo "<option value='OT'" . ($gameOutcome == "OT" ? " selected=true" : "") . ">OT</option>\n";
    echo "<option value='SO'" . ($gameOutcome == "SO" ? " selected=true" : "") . ">SO</option>\n";
    echo "</select></td>\n";
    echo "<td><input type='submit' value='Ändra'/></td>\n";
    echo "</form></tr>\n";
  }
?>
    </table>

</td>
</tr>
</table>
</body>
</html>
