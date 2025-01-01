<?php
session_start();
$user = array_key_exists("user", $_SESSION) ? $_SESSION["user"] : "";
$currentSeason = "20242025-2";

require 'dbpw.php';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
