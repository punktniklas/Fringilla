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
    </ul>

<?php
  if(!empty($user)) {
?>

    <h2>Tippa</h2>

<?php
date_default_timezone_set("America/Los_Angeles");
$today = date("Y-m-d");
$sql = "SELECT 1 FROM Games WHERE Date = '$today' LIMIT 1";
$result = $conn->query($sql);

if($row = $result->fetch_assoc()) {
  echo "Klicka här för att <a href='bet.php?day=$today'>tippa dagens matcher</a> ($today)<br/>\n";
} else {
  echo "Inga matcher att tippa idag ($today)<br/>\n";
}
echo "Klicka här för <a href='allbetdays.php'>övriga tipsdagar</a><p/>\n";
?>
    </ul>
<?php
  }
?>

    </td>
  </tr>
</table>
</body>
</html>
