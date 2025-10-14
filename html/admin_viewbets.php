<?php require 'db.php';?>
<?php require 'assertadmin.php';?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Lagda tips</title>
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

    <h1>Lagda tips</h1>

<?php
  date_default_timezone_set("America/Los_Angeles");
  $today = date("Y-m-d");
  $stmt = $conn->prepare(
    "SELECT g.Date, u.Name, COUNT(*) AS NumBets " .
    "FROM Games g " .
    "JOIN Bets b ON(g.GameId = b.GameId) " .
    "JOIN Users u USING(UserId) " .
    "WHERE g.Date >= ? GROUP BY Date, Name ORDER BY Date, Name;");
  $stmt->bind_param("s", $today);
  $stmt->execute();
  $result = $stmt->get_result();

  $curday = "";
  while($row = $result->fetch_assoc()) {
    if($curday != $row["Date"]) {
      if($curday != "") {
        echo "</table>\n";
      }
      $curday = $row["Date"];
      echo "<h2>" . $curday . "</h2>\n";
      echo "<table>\n";
    }
    echo "<tr><td>" . $row["Name"] . "</td><td>&nbsp;-&nbsp;</td><td>" . $row["NumBets"] . "</td></tr>\n";
  }
  if($curday != "") {
    echo "</table>\n";
  }
?>

</td>
</tr>
</table>
</body>
</html>
