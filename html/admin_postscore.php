<?php require 'db.php';?>
<?php require 'assertadmin.php';?>

<?php
$gameId = $_POST["gameId"];
$homeGoals = $_POST["homeGoals"];
$awayGoals = $_POST["awayGoals"];
$gameOutcome = $_POST["gameOutcome"];
$winner = $homeGoals > $awayGoals ? 1 : 2;

$stmt = $conn->prepare(
  "UPDATE Games SET HomeGoals = ?, AwayGoals = ?, Winner = ?, GameOutcome = ? " .
  "WHERE GameId = ?;");
$stmt->bind_param("iiisi", $homeGoals, $awayGoals, $winner, $gameOutcome, $gameId);
$stmt->execute();
header("Location: admin_editscores.php");
exit;
?>
