<?php require 'db.php';?>
<?php require 'assertadmin.php';?>

<?php
$edituser = $_POST["edituser"];
foreach ($_POST as $key => $val) {
  if (!preg_match("/winner-(\d+)/", $key, $matches)) {
    continue;
  }
  $gameid = $matches[1];
  if($val == 0) {
    $stmt = $conn->prepare("DELETE FROM Bets WHERE UserId = ? AND GameId = ?;");
    $stmt->bind_param("si", $edituser, $gameid);
    $stmt->execute();
  } else {
    $stmt = $conn->prepare(
      "INSERT INTO Bets (UserId, GameId, Winner) " .
      "VALUES (?, ?, ?) " .
      "ON DUPLICATE KEY UPDATE Winner = ?;");
    $stmt->bind_param("siii", $edituser, $gameid, $val, $val);
    $stmt->execute();
  }
}
header("Location: admin_editbets.php");
exit;
?>
