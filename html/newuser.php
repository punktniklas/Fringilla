<?php require 'db.php';?>
<?php
$userid = $dispname = $email = $password1 = $password2 = "";
$useridErr = $dispnameErr = $emailErr = $password1Err = $password2Err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $userid = sanitizeInput($_POST["userid"]);
  $dispname = sanitizeInput($_POST["dispname"]);
  $email = sanitizeInput($_POST["email"]);
  $password1 = sanitizeInput($_POST["password1"]);
  $password2 = sanitizeInput($_POST["password2"]);

  if(empty($userid)) {
    $useridErr = "Användarnamn måste anges.";
  } else if(strlen($userid) > 20) {
    $useridErr = "Användarnamn får max vara 20 bokstäver.";
  } else if(!preg_match("/^\w+$/", $userid)) {
    $useridErr = "Användarnamn får bara innehålla a-z och 0-9.";
  } else if(isUserIdTaken($userid, $conn)) {
    $useridErr = "Användarnamnet är redan taget.";
  }

  if(empty($dispname)) {
    $dispnameErr = "Visingsnamn måste anges.";
  } else if(strlen($dispname) > 50) {
    $dispnameErr = "Visningsnamn får max vara 50 bokstäver.";
  }

  if(empty($email)) {
    $emailErr = "E-post måste anges.";
  } else if(strlen($email) > 50) {
    $emailErr = "E-post får max vara 50 tecken.";
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $emailErr = "Ogiltig E-post-adress.";
  }

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

  if(empty($useridErr)
    && empty($dispnameErr)
    && empty($emailErr)
    && empty($password1Err)
    && empty($password2Err)) {
      $pwdhash = password_hash($password1, PASSWORD_DEFAULT);
      $stmt = $conn->prepare(
        "INSERT INTO Users (UserId, Password, Name, Email) " .
        "VALUES (?, ?, ?, ?);");
      $stmt->bind_param("ssss", $userid, $pwdhash, $dispname, $email);
      $stmt->execute();

      $_SESSION["user"] = $userid;
      $_SESSION["fullname"] = $dispname;
      header("Location: index.php");
      exit;
  }
}

function sanitizeInput($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function isUserIdTaken($id, $conn) {
  $stmt = $conn->prepare("SELECT 1 FROM Users WHERE UserId = ?;");
  $stmt->bind_param("s", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->num_rows > 0;
}
?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Registrera ny användare</title>
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

    <h1>Registrera ny användare</h1>
    
    <form action="newuser.php" method="POST">
    <table>
    <tr><td>Användarnamn</td><td><input type="text" name="userid" maxlength=20 value="<?php echo $userid;?>"/></td><td class="errortext"><?php echo $useridErr;?></td></tr>
    <tr><td>Visningsnamn</td><td><input type="text" name="dispname" maxlength=50 value="<?php echo $dispname;?>"/></td><td class="errortext"><?php echo $dispnameErr;?></td></tr>
    <tr><td>E-post</td><td><input type="text" name="email" maxlength=50 value="<?php echo $email;?>"/></td><td class="errortext"><?php echo $emailErr;?></td></tr>
    <tr><td>Lösen</td><td><input type="password" name="password1" maxlength=30 value="<?php echo $password1;?>"/></td><td class="errortext"><?php echo $password1Err;?></td></tr>
    <tr><td>Bekräfta lösen</td><td><input type="password" name="password2" maxlength=30 value="<?php echo $password2;?>"/></td><td class="errortext"><?php echo $password2Err;?></td></tr>
    </table>
    <input type='submit' value='Registrera'/>
    </form>

</td>
</tr>
</table>
</body>
</html>
