<?php require 'db.php';?>
<?php require 'assertadmin.php';?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Ändra användares tips</title>
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

    <h1>Ändra användares tips</h1>

    <form action="admin_editbet.php" method="GET">
      Välj användare och tipsdag.<p/>
      <select name="edituser">
<?php
  $result = $conn->query("SELECT UserId, Name FROM Users ORDER BY Name;");
  while($row = $result->fetch_assoc()) {
    echo "<option value='" . $row["UserId"] . "'>" . $row["Name"] . "  (" . $row["UserId"] . ")</option>\n";
  }
?>
      </select><p/>
      <select name="editday">
<?php
  $result = $conn->query("SELECT DISTINCT Date FROM Games ORDER BY Date;");
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
