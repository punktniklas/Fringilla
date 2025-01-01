<?php require 'db.php';?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Alla tipsdagar</title>
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

    <h1>Alla tipsdagar</h1>

    <ul>

<?php
$sql = "SELECT DISTINCT Date FROM Games ORDER BY Date";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
  echo "<li><a href='bet.php?day=" . $row["Date"] . "'>" . $row["Date"] . "</a></li>\n";
}
?>
    </ul>

</td>
</tr>
</table>
</body>
</html>
