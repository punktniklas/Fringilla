<?php
  session_start();
  unset($_SESSION["user"]);
  unset($_SESSION["fullname"]);
  unset($_SESSION["isadmin"]);
  header("Location: index.php");
  exit;
?>
