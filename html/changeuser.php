<?php require 'db.php';?>
<?php

if(empty($user)) {
  header("Location: index.php");
  exit;
}

$dispname = $email = "";
$dispnameErr = $emailErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $dispname = sanitizeInput($_POST["dispname"]);
  $email = sanitizeInput($_POST["email"]);

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

if(empty($dispnameErr) && empty($emailErr)) {
      $stmt = $conn->prepare("UPDATE Users SET Name = ?, Email = ? WHERE UserId = ?");
      $stmt->bind_param("sss", $dispname, $email, $user);
      $stmt->execute();
      $_SESSION["fullname"] = $dispname;

      header("Location: index.php");
      exit;
  }
} else {
  $stmt = $conn->prepare("SELECT Name, Email FROM Users WHERE UserId = ?");
  $stmt->bind_param("s", $user);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $dispname = $row["Name"];
  $email = $row["Email"];
}

function sanitizeInput($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>

<html xmlns="http://www.w3.org/1999/xhtm">
  <head>
    <title>Ändra användare <?php echo $user; ?></title>
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

    <h1>Ändra uppgifter för <?php echo $user; ?></h1>

    <form action="changeuser.php" method="POST">
    <table>
    <tr><td>Visningsnamn</td><td><input type="text" name="dispname" maxlength=50 value="<?php echo $dispname;?>"/></td><td class="errortext"><?php echo $dispnameErr;?></td></tr>
    <tr><td>E-post</td><td><input type="text" name="email" maxlength=50 value="<?php echo $email;?>"/></td><td class="errortext"><?php echo $emailErr;?></td></tr>
    </table>
    <input type='submit' value='Ändra'/>
    </form>

</td>
</tr>
</table>
</body>
</html>
