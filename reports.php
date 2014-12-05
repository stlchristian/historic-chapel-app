<?php
// Include the necessary functions and connect to the 'tech' db
include('functions.php');
connectDB();

// Select all the chapel services that are in the database, store as $result (should this be limited, could grow to be cumbersome?)
$all_chapel_services = "SELECT chapelId, chapelDate FROM chapel ORDER BY chapelDate DESC";
$result = mysql_query($all_chapel_services)
  or die("Error selecting chapel services from database, line " . __LINE__ . ": " . mysql_error());

// If a user has selected a chapel service to see a report, get the date and id from the url via $_GET
// Then, output the first half of the HTML page, lines ~40-89
if(isset($_GET['chapelId']) && isset($_GET['chapelDate'])){
  $chapelId = $_GET['chapelId'];
  $chapelDate = $_GET['chapelDate'];
  
  // Selects all student's first/lastname who attended the selected chapel service, and store them
  // Had to use the stupid INNER JOIN command, because MySQL 4 doesn't support sub-selects
  $select_present_student_names = "SELECT firstname, lastname
                                   FROM students
                                   INNER JOIN studentchapel ON students.studentId = studentchapel.studentId
                                   WHERE studentchapel.chapelId = '$chapelId'
                                   ORDER BY students.lastname ASC";
  $present_student_names = mysql_query($select_present_student_names)
    or die("Error selecting names from database, line " . __LINE__ . ": " . mysql_error());
  $num_present_students = mysql_num_rows($present_student_names);
    
  // Selects all student's first/lastnames who didn't attend the selected chapel service, and store them
  // Had to use the even more stupid LEFT JOIN command, because MySQL 4 doesn't support sub-selects
  $select_absent_student_names = "SELECT firstname, lastname
                                  FROM students
                                  LEFT JOIN studentchapel ON students.studentId = studentchapel.studentId
                                                          AND studentchapel.chapelId = '$chapelId'
                                  WHERE students.type = 'day'
                                  AND studentchapel.studentId IS NULL
                                  ORDER BY students.lastname ASC";
  $absent_student_names = mysql_query($select_absent_student_names)
    or die("Error selecting names from database, line " . __LINE__ . ": " . mysql_error());
  $num_absent_students = mysql_num_rows($absent_student_names);

?>
<HTML>

<HEAD>
<TITLE>Chapel Attendance</TITLE>
<HTML>

<BODY>
<a href="start.php">Back to Start-Page</a><br />
<a href="reports.php">Back to Reports</a>

<center>
<h2><u>Chapel Attendance :: <?php print $chapelDate; ?></u></h2>
<table border="1" cellspacing="0" cellpadding="5">
  <tr valign="top">
    <td width="50%">
      <table width="100%" border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td><b><u>Present :: <?php echo $num_present_students; ?> Students</u></b></td>
        </tr>
        <?php
          // Iterate through the present students, putting their names in an HTML table
          while($present_row = mysql_fetch_array($present_student_names)){
        ?>
        <tr>
          <td><?php print $present_row['lastname'] . ", " . $present_row['firstname']; ?></td>
        </tr>
        <?php
          }
        ?>
      </table>
    </td>
    <td width="50%">
      <table width="100%" border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td><b><u>Absent :: <?php echo $num_absent_students; ?> Students</u></b></td>
        </tr>
        <?php
          // Iterate through the absent students, putting their names in an HTML table
          while($absent_row = mysql_fetch_array($absent_student_names)){
        ?>
        <tr>
          <td><?php print $absent_row['lastname'] . ", " . $absent_row['firstname']; ?></td>
        </tr>
        <?php
          }
        ?>
      </table>
    </td>
  </tr>
</table>
<?php } else { ?>
<HEAD>
<TITLE>Chapel Attendance</TITLE>
</HEAD>

<BODY>
<a href="start.php">Back to Start-Page</a>
<center>
<h2>Previous Chapel Services</h2>
<table>
<?php
  while($row = mysql_fetch_array($result)){
?>
  <tr>
    <td>
      <?php print '<a href="reports.php?chapelId=' . $row['chapelId'] . '&chapelDate=' . $row['chapelDate'] . '">' . $row['chapelDate'] . '</a>'?>
    </td>
  <tr>
<?php
  }
?>
</table>
<?php } ?>
</center>
</BODY>

</HTML>