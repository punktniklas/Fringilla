<?php require 'db.php';?>
<?php
$errorType = array_key_exists("errorType", $_GET) ? $_GET["errorType"] : "";

?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Glömt lösenord</title>
   <link rel="stylesheet" type="text/css" media="screen" href="fringilla.css" />
</head>
<body>
<table cellpadding="0" cellspacing="0">
  <tr><td colspan="2" id="top">
    <?php require 'top.php';?>
  </td></tr>
  <tr>
    <td valign="top" id="menu" class="menutext">
      <?php require 'menu.php';?>
    </td>
    <td valign="top" id="main">

    <h1>Glömt lösenord</h1>

    Ange antingen ditt login/användarnamn eller E-postadress. Om det finns en användare med någon av dessa
    kommer ett email att skickas till den E-postadress som angavs vid registrering med en länk för att
    kunna välja ett nytt lösenord.<p/>

<?php
if(!empty($errorType)) {
  echo "<p class='errortext'>";
  if($errorType == "userid") {
    echo "Finns ingen användare med det användarnamnet.";
  } else if($errorType == "email") {
    echo "Finns ingen användare med den E-postadressen.";
  } else if($errorType == "noinput") {
    echo "Ange antingen användarnamn eller E-postadress.";
  } else {
    echo "Okänt fel...";
  }
  echo "</p>\n";
}
?>

    <form action="postforgotpwd.php" method="POST">
    <table>
    <tr><td>Användarnamn</td><td><input type="text" name="userid" maxlength=20/></tr>
    <tr><td>E-post</td><td><input type="text" name="email" maxlength=50"/></td></tr>
    </table>
    <input type='submit' value='Skicka'>
    </form>

</td>
</tr>
</table>
</body>
</html>
