<?php require 'db.php';?>
<?php
  if(empty($user)) {
    header("Location: index.php");
    exit;
  }
  require 'assertadmin.php';


  $editday = $_POST["editday"];
  $edituser = $_POST["edituser"];

  $result = $conn->query("SELECT TeamId, Code, Name FROM Teams;");
  while($row = $result->fetch_assoc()) {
    $codes[$row["Code"]] = $row["TeamId"];
    $names[$row["Name"]] = $row["TeamId"];
  }

  $bjurmanTeams = [
    "Carolina" => "CAR",
    "Columbus" => "CBJ",
    "New Jersey" => "NJD",
    "NY Islanders" => "NYI",
    "NY Rangers" => "NYR",
    "Philadelphia" => "PHI",
    "Pittsburgh" => "PIT",
    "Washington" => "WSH",
    "Boston" => "BOS",
    "Buffalo" => "BUF",
    "Detroit" => "DET",
    "Florida" => "FLA",
    "Montreal" => "MTL",
    "Ottawa" => "OTT",
    "Tampa" => "TBL",
    "Toronto" => "TOR",
    "Chicago" => "CHI",
    "Colorado" => "COL",
    "Dallas" => "DAL",
    "Minnesota" => "MIN",
    "Nashville" => "NSH",
    "St Louis" => "STL",
    "Utah" => "UTA",
    "Winnipeg" => "WPG",
    "Anaheim" => "ANA",
    "Calgary" => "CGY",
    "Edmonton" => "EDM",
    "LA" => "LAK",
    "San Jose" => "SJS",
    "Seattle" => "SEA",
    "Vancouver" => "VAN",
    "Vegas" => "VGK"
  ];

  $betstr = $_POST["betstr"];
  $invalid = array();
  foreach(preg_split("/\n/", $betstr) as $line) {
    $line = trim($line);
    if(empty($line)) {
      continue;
    }
    if(preg_match("/\W*([\w\s]+)[-\x{2013}]([\w\s]+)(1|2).*/u", $line, $matches)) {
      $home = trim($matches[1]);
      $away = trim($matches[2]);
      $winner = $matches[3];
    } else {
      array_push($invalid, $line);
      continue;
    }
    $homeId = null;
    $awayId = null;
    if(array_key_exists($home, $codes)) {
      $homeId = $codes[$home];
    } else if(array_key_exists($home, $names)) {
      $homeId = $names[$home];
    } else if(array_key_exists($home, $bjurmanTeams)) {
      $homeId = $codes[$bjurmanTeams[$home]];
    }

    if(array_key_exists($away, $codes)) {
      $awayId = $codes[$away];
    } else if(array_key_exists($away, $names)) {
      $awayId = $names[$away];
    } else if(array_key_exists($away, $bjurmanTeams)) {
      $awayId = $codes[$bjurmanTeams[$away]];
    }

    if(empty($homeId) || empty($awayId)) {
      array_push($invalid, $line);
      continue;
    }
    $teamResult[$homeId] = $winner == 1 ? 'W' : 'L';
    $teamResult[$awayId] = $winner == 2 ? 'W' : 'L';
}

$stmt = $conn->prepare("SELECT GameId, Home, Away FROM Games g WHERE Date = ?;");
$stmt->bind_param("s", $editday);
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_assoc()) {
  $gameId = $row["GameId"];
  $home = $row["Home"];
  $away = $row["Away"];
  if(empty($teamResult[$home]) || empty($teamResult[$away])) {
    continue;
  }
  $winner = $teamResult[$home] == 'W' ? 1 : 2;
  $stmt = $conn->prepare(
    "INSERT INTO Bets (UserId, GameId, Winner) " .
    "VALUES (?, ?, ?) " .
    "ON DUPLICATE KEY UPDATE Winner = ?;");
  $stmt->bind_param("siii", $edituser, $gameId, $winner, $winner);
  $stmt->execute();
}

$invalidstr = urlencode(join("#", $invalid));
header("Location: admin_editbet.php?edituser=$edituser&editday=$editday&invalidlines=$invalidstr");
exit;
?>
