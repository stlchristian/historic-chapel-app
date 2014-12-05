<?php
// Get some things set up to start off with, get our functions, set our hashed password
include('functions.php');
$check = '49334f35efb5f24b1d36e06b6b93ce9f';
$loggedIn = false;

// Set the index page
$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';

// Check the password entered by the user against the hash in $check. If they are the same, set the cookie
if(!empty($_POST['login'])){
  $password = $_POST['password'];
  $password = md5($password);
  $salt = sha1(md5($password));
  $password = md5($salt.$password.$salt);
  if($password == md5($salt.$check.$salt)){
    $loggedIn = true;
    setcookie('loggedIn', 'true');
  } else {
    $loggedIn = false;
  }
}

// If the loggedIn cookie is not set, redirect them to index.php
if(empty($_COOKIE['loggedIn'])){
  header("Location: " . $home_url);
}
?>
<HTML>
<HEAD>
 <TITLE>Chapel Attendance</TITLE>
</HEAD>
<BODY>
<center>
<?php 
  // Check if the user is logged in before allowing them to select an option
  if($_COOKIE['loggedIn'] == 'true'){
?>
<h3>Choose what you'd like to do.</h3>
<form action="scan.php" method="post">
<input type="hidden" name="what_to_do" value="new" />
<input type="submit" value="New Chapel Service" />
</form>
<form action="scan.php" method="post">
<input type="hidden" name="what_to_do" value="resume" />
<input type="submit" value="Resume Today's Chapel" />
</form>
<form action="reports.php" method="post">
<input type="hidden" name="what_to_do" value="reports" />
<input type="submit" value="View Chapel Reports" />
</form>
<?php } ?>
</center>
</BODY>
</HTML>