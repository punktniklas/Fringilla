<?php require 'db.php';?>
<?php

  $userid = trim($_POST["userid"]);
  $email = trim($_POST["email"]);

  if(empty($userid) && empty($email)) {
    header("Location: forgotpwd.php?errorType=noinput");
    exit;
  }

  $stmt = $conn->prepare(
    "SELECT UserId, Email FROM Users " .
    (!empty($userid) ? "WHERE UserId = ? " : "WHERE Email = ? ") .
    "LIMIT 1;");
  if(!empty($userid)) {
    $stmt->bind_param("s", $userid);
  } else {
    $stmt->bind_param("s", $email);
  }
  $stmt->execute();
  $result = $stmt->get_result();

  $row = $result->fetch_assoc();
  if(!$row) {
    header("Location: forgotpwd.php?errorType=" . (!empty($userid) ? "userid" : "email"));
    exit;  
  }
  $dbuserid = $row["UserId"];
  $dbemail = $row["Email"];
  $code = md5(random_int(PHP_INT_MIN, PHP_INT_MAX));

  $stmt = $conn->prepare("UPDATE Users SET ResetCode = ? WHERE UserId = ?;");
  $stmt->bind_param("ss", $code, $dbuserid);
  $stmt->execute();

$url = $baseUrl . "resetpwd.php?code=$code";

$subject = "NHL-tipset password";
$txt =
  "Hej!\r\n\r\n" .
  "Någon, förhoppningsvis du, har klickat på länken för glömt lösenord för NHL-tipset.\r\n" .
  "Användarnamn: $dbuserid\r\n" .
  "Klicka på denna länk för att ange ett nytt lösenord.\r\n" .
  "$url\r\n";
$headers =
  "From: webmaster@nilin.se\r\n" .
  "Content-Type: text/plain; charset=utf-8\r\n" .
  "Content-Transfer-Encoding: quoted-printable";

mail($dbemail, $subject, quoted_printable_encode($txt), $headers);
logString("Reset code requested. User '" . $dbuserid . "' Code '" . $code . "'");
header("Location: pwdmailsent.php");
exit;  
?>
