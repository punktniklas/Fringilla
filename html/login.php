<?php
  require 'db.php';

  $stmt = $conn->prepare("SELECT Name, Password, IsAdmin FROM Users WHERE UserId = ?");
  $stmt->bind_param("s", $userid);
  $userid = $_POST["userid"];
  $password = $_POST["password"];
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $pwdhash = $row["Password"];
  } else {
    $pwdhash = "";
  }
  if(password_verify($password, $pwdhash)) {
    $_SESSION["user"] = $userid;
    $_SESSION["fullname"] = $row["Name"];
    if($row["IsAdmin"]) {
      $_SESSION["isadmin"] = TRUE;
    }
  } else {
    $_SESSION["badlogin"] = TRUE;
  }
  header("Location: index.php");
  exit;
?>