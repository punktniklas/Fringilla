<?php require 'db.php';?>
<?php require 'assertadmin.php';?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Ändra matchresultat</title>
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

    <h1>Ändra matchresultat</h1>

    <form action="admin_editscoreforday.php" method="GET">
      Välj matchdag.<p/>
      <select name="editday">
<?php
  $stmt = $conn->prepare("SELECT DISTINCT Date FROM Games WHERE SeasonId = ? ORDER BY Date;");
  $stmt->bind_param("s", $selectedSeason);
  $stmt->execute();
  $result = $stmt->get_result();
  while($row = $result->fetch_assoc()) {
    echo "<option value='" . $row["Date"] . "'>" . $row["Date"] . "</option>\n";
  }
?>
      </select><p/>
      <input type="submit" value="Välj">
    </form>

</td>
</tr>
</table>
</body>
</html>
