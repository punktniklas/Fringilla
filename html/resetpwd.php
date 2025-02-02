<?php require 'db.php';?>
<?php
$password1 = $password2 = "";
$password1Err = $password2Err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if(!array_key_exists("code", $_POST)) {
    echo "No code";
    exit;
  }
  $code = sanitizeInput($_POST["code"]);

  $password1 = sanitizeInput($_POST["password1"]);
  $password2 = sanitizeInput($_POST["password2"]);

  if(empty($password1)) {
    $password1Err = "Lösenord måste anges.";
  } else if($password1 != $_POST["password1"]) {
    $password1Err = "Lösenordet innehåller ogiltiga tecken.";
  }

  if(empty($password2)) {
    $password2Err = "Lösenord måste anges.";
  } else if($password1 != $password2) {
    $password2Err = "Lösenorden matchar inte.";
  }

  if(empty($password1Err) && empty($password2Err)) {
      $pwdhash = password_hash($password1, PASSWORD_DEFAULT);
      $stmt = $conn->prepare(
        "UPDATE Users SET Password = ?, ResetCode = NULL " .
        "WHERE ResetCode = ?;");
      $stmt->bind_param("ss", $pwdhash, $code);
      $stmt->execute();

      header("Location: resetpwd_success.php");
      exit;
  }
} else { // GET
  if(!array_key_exists("code", $_GET)) {
    echo "No code";
    exit;
  }
  $code = sanitizeInput($_GET["code"]);
}

function sanitizeInput($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function doesResetCodeExist($code, $conn) {
  $stmt = $conn->prepare("SELECT 1 FROM Users WHERE ResetCode = ?;");
  $stmt->bind_param("s", $code);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->num_rows > 0;
}
?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Nytt lösenord</title>
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

<?php
if(!doesResetCodeExist($code, $conn)) {
?>
<p class="errortext">Ogiltig kod. Har du använt en gammal länk?</p>

<?php
} else {
?>
    <h1>Nytt lösenord</h1>
    
    <form action="resetpwd.php" method="POST">
    <table>
    <tr><td>Lösen</td><td><input type="password" name="password1" maxlength=30 value="<?php echo $password1;?>"/></td><td class="errortext"><?php echo $password1Err;?></td></tr>
    <tr><td>Bekräfta lösen</td><td><input type="password" name="password2" maxlength=30 value="<?php echo $password2;?>"/></td><td class="errortext"><?php echo $password2Err;?></td></tr>
    </table>
    <input type='hidden' name='code' value='<?php echo $code;?>' />
    <input type='submit' value='Ändra'/>
    </form>
<?php
}
?>

</td>
</tr>
</table>
</body>
</html>
