<?php
  // Only show this form if the user is logged in
  if($_COOKIE['loggedIn'] == 'true'){
?>
<HTML>

<HEAD>
<!-- A little javascript magic to always be focused on the barcode text box. Thanks, Adam!  -->
<script type="text/javascript">
      function focusing(){
      document.getElementById('barcode').focus();
    }
</script>

<TITLE>Chapel Attendance</TITLE>
</HEAD>

<BODY onLoad="javascript:focusing();">
<a href="start.php">Back to Start-Page</a>
<center>
  <form id="scanForm" action="scan.php" method="post">
    <b>Scan ID:</b><br /><input type="text" id="barcode" name="barcode">
  </form>
  <b><font color="red"><?php echo $alert; ?></font></b>
</center>
</BODY>

</HTML>
<?php
  }
?>