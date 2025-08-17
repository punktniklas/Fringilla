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
header("Location: showbet.php?day=$day");
?>
