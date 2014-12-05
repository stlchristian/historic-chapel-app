<?php
  // Link to the start page. Creating it this way, to make application as portable as possible.
  $start_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/start.php';
  
  // Check to see if the user is already logged in. If so, send them to the start page
  if($_COOKIE['loggedIn'] == 'true'){
    header("Location: " . $start_url);
  }
?>
<HTML>
<HEAD>
 <TITLE>Chapel Attendance</TITLE>
</HEAD>
<BODY>
<center><b>Welcome! Please login.</b></center>
<center>
<BR><BR>
<form name="login" action="start.php" method="post">
<table>
  <tr>
    <td>
      Password:
    </td>
    <td>
      <input type="password" name="password"><BR>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type='submit' value="Login" name="login">
    </td>
  </tr>
</table>
</form>
</center>
</BODY>
</HTML>