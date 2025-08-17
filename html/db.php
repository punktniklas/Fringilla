<?php
session_start();
$user = array_key_exists("user", $_SESSION) ? $_SESSION["user"] : "";
$currentSeason = "20252026-2";
$selectedSeason = array_key_exists("selectedSeason", $_SESSION) ? $_SESSION["selectedSeason"] : $currentSeason;

require 'dbpw.php';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
