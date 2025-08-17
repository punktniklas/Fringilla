<?php
  require 'db.php';

  $_SESSION["selectedSeason"] = $_POST["seasonId"];
  header("Location: index.php");
  exit;
?>
