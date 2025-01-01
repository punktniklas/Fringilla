<?php
if(empty($user)) {
?>
  <form action="login.php" method="POST">
  <table>
    <tr><td class="menutext">Login:</td><td><input type="text" name="userid"></td></tr>
    <tr><td class="menutext">Lösen:</td><td><input type="password" name="password"></td></tr>
    <tr><td colspan=2><input type="submit" value="Logga in"></td></tr>
  </table>
  </form>
  
<?php
  if(array_key_exists("badlogin", $_SESSION)) {
    echo "<span class='errortext'>Inloggningen misslyckades.</span><br/>";
    unset($_SESSION["badlogin"]);
  }
  echo "<a href='newuser.php'>Registrera ny användare</a>";
} else {
  echo "Inloggad:<br/>" . $_SESSION["fullname"] . "<br/>";
?>
  <form action="logout.php" method="POST">
    <input type="submit" value="Logga ut">
  </form>
<?php
}
?>

<hr/>

<h3>Topplistor</h3>
<a href="standings.php?season=20242025-2">Totalen</a><br/>
<a href="golds.php?season=20242025-2">Guldligan</a><br/>
<a href="lollipops.php?season=20242025-2">Klubbligan</a><br/>

<?php
  if(array_key_exists("isadmin", $_SESSION)) {
?>
<hr/>
<h3>Admin</h3>
<a href="admin_editbets.php">Ändra användares tips</a><br/>
<a href="admin_editscores.php">Ändra matchresultat</a><br/>
<?php
  }
?>

<hr/>
<a href="index.php">Startsida</a><br/>
