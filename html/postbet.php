<?php require 'db.php';?>
<?php
  if(empty($user)) {
    header("Location: index.php");
    exit;
  }


  $day = $_POST["day"];
  $stmt = $conn->prepare("SELECT GameId FROM Games g WHERE Date = ? AND StartTime > NOW();");
  $stmt->bind_param("s", $day);
  $stmt->execute();
  $result = $stmt->get_result();

  $validGameIds = [];
  while($row = $result->fetch_assoc()) {
    $validGameIds[$row["GameId"]] = 1;;
  }

foreach ($_POST as $key => $val) {
  if (!preg_match("/winner-(\d+)/", $key, $matches)) {
    continue;
  }
  $gameid = $matches[1];
  if(!array_key_exists($gameid, $validGameIds)) {
    continue;
  }
  $stmt = $conn->prepare(
    "INSERT INTO Bets (UserId, GameId, Winner) " .
    "VALUES (?, ?, ?) " .
    "ON DUPLICATE KEY UPDATE Winner = ?;");
  $stmt->bind_param("siii", $user, $gameid, $val, $val);
  $stmt->execute();
}
?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>NHL-bloggens tips</title>
   <link rel="stylesheet" type="text/css" media="screen" href="fringilla.css" />
</head>
<body>

<script>
  function copyToClipboard(elm_id) {
    var copyText = document.getElementById("bettxt");
    var text = copyText.innerText;
    navigator.clipboard.writeText(text);
  }
</script>

<table cellpadding="0" cellspacing="0">
  <tr><td colspan="2" id="top">
    <?php require 'top.php';?>
  </td></tr>
  <tr>
    <td valign="top" id="menu" class="menutext">
      <?php require 'menu.php';?>
    </td>
    <td valign="top" id="main">

<table border=1>
<tr><td id="bettxt">

<?php
  $stmt = $conn->prepare(
    "SELECT h.Code AS Home, a.Code AS Away, b.Winner " .
    "FROM Games g " .
    "JOIN Teams h ON (h.TeamId = g.Home) " .
    "JOIN Teams a ON (a.TeamId = g.Away) " .
    "LEFT OUTER JOIN Bets b ON(b.GameId = g.GameId AND b.UserId = ?)" .
    "WHERE Date = ? " .
    "ORDER BY OrderInDay;");
  $stmt->bind_param("ss", $user, $day);
  $stmt->execute();
  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    $winner = $row["Winner"];
    echo $row["Home"] . "-" . $row["Away"] . " " . (empty($winner) ? "X" : $winner) . "<br/>\n";
  }
  
?>
</td></tr>
</table>
<button onclick="copyToClipboard('bettxt')">Kopiera</button><p/>
Klicka på Kopiera-knappen för att kopiera din tipsrad<br/>
till klippbordet för enkel inklistring i bloggspåret.
<p/>
<a href="bet.php?day=<?php echo $day; ?>">Ändra tipset</a>

</td>
</tr>
</table>
</body>
</html>
