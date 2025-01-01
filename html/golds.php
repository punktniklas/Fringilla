<?php require 'db.php';?>
<?php require 'utils.php';?>
<?php $season = $_GET["season"]; ?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Guldligan</title>
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


    <h1>&#x1f947 Guldligan <?php echo formatSeason($season); ?></h1>
    
    <table>
<?php

  $stmt = $conn->prepare(
    "SELECT u.Name, SUM(dr.Gold) AS Golds " .
    "FROM DayResults dr " .
    "JOIN Users u USING (UserId) " .
    "WHERE dr.SeasonId = ? " .
    "GROUP BY UserId " .
    "ORDER BY Golds DESC");
  $stmt->bind_param("s", $season);
  $stmt->execute();
  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row["Name"] . "</td><td>" . $row["Golds"] . "</td></tr>";
  }
?>
</table>

</td>
</tr>
</table>
</body>
</html>
