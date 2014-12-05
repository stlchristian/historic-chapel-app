<?php
// Set today's date
$today = date("Y-m-d");
// Include the necessary functions, and connect to the 'tech' db (in the function)
include('functions.php');
connectDB();

// If a barcode has been $_POSTed, remember it
if(isset($_POST['barcode'])){
  $studentId = $_POST['barcode'];
}

// Set some url variables, to keep things handy and easy to remember
$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
$start_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/start.php';

// Query the most recent chapel service stored in the database
$most_recent_query = "SELECT chapelId, chapelDate FROM chapel ORDER BY chapelDate DESC LIMIT 1";
// Creat a new chapel service and store it in the database
$start_new_query = "INSERT INTO chapel (chapelDate) VALUES (NOW())";
// Insert a new student into the database to log their presence -- NOT WORKING
$insert_student_query = 'INSERT INTO studentchapel (chapelId, studentId) VALUES (' . intval($_COOKIE['chapelId']) . ', ' . intval($studentId) . ')';

// See if they've selected something from start.php, if so store it as $choice
// This section is just querying the database for some starting information
if(isset($_POST['what_to_do'])){
  $choice = $_POST['what_to_do'];
  // If we're trying to create a new service:
  if($choice == 'new'){
    // query the most recent service
    $result = mysql_query($most_recent_query)
      or die("Error querying database for most recent chapel, line " . __LINE__ . ": " . mysql_error());
    $data = mysql_fetch_array($result);
    // Is there already a chapel service started for today? If so, force a resume
    if($data['chapelDate'] == $today){
      echo '<center>' .
        "<font color='red'>The most recent chapel was today.<br />Please resume that chapel</font><br /><br />" .
        "</form>" .
        '<form action="scan.php" method="post">' . 
        '<input type="hidden" name="what_to_do" value="resume" />' .
        '<input type="submit" value="Resume Today\'s Chapel" />' .
        '</form>' .
        '</center>';
    }
    // Is the most recent chapel on a day other than today? If so, start a new chapel
    if($data['chapelDate'] != $today){
      mysql_query($start_new_query)
        or die("Error querying database to start a new chapel, line " . __LINE__ . ": " . mysql_error()
          . "<br />Contact Technical Services 314.837.6777 x1250");
      // query a the most recent chapel (^^ the one you just created ^^), and set cookies (chapelId, chapelDate)
      $result = mysql_query($most_recent_query)
        or die("Error querying database for most recent chapel, line " . __LINE__ . ": " . mysql_error()
          . "<br />Contact Technical Services 314.837.6777 x1250");
      $data = mysql_fetch_array($result);
      setcookie('chapelId', $data['chapelId']);
      setcookie('chapelDate', $data['chapelDate']);
      // Place the scan form here
      include('scan_form.php');
    }
  }
  // If we're trying to resume, check to see if the most recent chapel was today
  elseif($choice == 'resume'){
    $result = mysql_query($most_recent_query)
      or die("Error querying database to resume chapel, line " . __LINE__ . ": " . mysql_error()
        . "<br />Contact Technical Services 314.837.6777 x1250");
    $data = mysql_fetch_array($result);
    // Most recent chapel is from today, set the cookies (What if they're already set. Is that problematic?)
    if($data['chapelDate'] == $today){
      setcookie('chapelId', $data['chapelId']);
      setcookie('chapelDate', $data['chapelDate']);
      include('scan_form.php');
    }
    // Most recent chapel was not today. Force a new service
    elseif($data['chapelDate'] != $today){
      echo '<center>' .
        "<font color='red'>The most recent chapel was not today.<br />Please start a new chapel.</font><br /><br />" .
        "</form>" .
        '<form action="scan.php" method="post">' . 
        '<input type="hidden" name="what_to_do" value="new" />' .
        '<input type="submit" value="New Chapel Service" />' .
        '</form>' .
        '</center>';
    }
  }
}

// They've not selected anything from start.php, they're coming back to this form without going through index.php
// Check if all the cookies are set and set the corresponding variables
elseif(isset($_COOKIE['chapelId'])){
  if($_COOKIE['chapelDate'] == $today){
    if($_COOKIE['loggedIn'] == 'true'){
      $chapelId = $_COOKIE['chapelId'];
      $chapelDate = $_COOKIE['chapelDate'];
      
      // Get the student's name and picture from the database and directory, respectively
      $sql = "SELECT lastname, firstname FROM students WHERE studentId = '$studentId' limit 1";
      $results = mysql_query($sql)
        or die("Error selecting from database, line " . __LINE__ . ": " . mysql_error()
          . "<br />Contact Technical Services 314.837.6777 x1250");
      // Store the resulting array as $name
      $name = mysql_fetch_array($results);
      
      // Look in the directory for the right picture; if found, set $pic
      $dir = opendir("../studentpics");
	  $pic = '';
	  while($file = readdir($dir)){
	     $found = strpos($file, $studentId);
		 if($found !== false){
		   $pic = "../studentpics/".$file;
		   break;
		 }
	  }
      // Close the directory, so everyone's happy
	  closedir($dir);
      // We couldn't find a picture, give the "No image" picture
	  if($pic == ''){
	    $pic = "../studentpics/noImageAvailable.jpg";
      }
      
      // If the student is not found in the database, give an alert
      if(empty($name)){
        $alert = "STUDENT NOT FOUND";
      }
      // If the student is found:
      elseif(!empty($name)){
        // Query to see if the student has already been logged as present. If so, throw an alert
        $double_check_query = "SELECT studentchapel.tranId FROM studentchapel
                               JOIN students ON (students.studentId = studentchapel.studentId)
                               WHERE students.studentId = '$studentId' and studentchapel.chapelId = '$chapelId'";
        $attended_chapel = runShortQuery($double_check_query);
        if(!empty($attended_chapel)){
          $alert = "The student number " . $studentId . " (" . $name['lastname'] . ", " . $name['firstname'] . ") has already been logged as present.";
        }
        // If they weren't present, Insert the studentId and chapelId into the 'studentchapel' table for recording
        else{
          $result = mysql_query($insert_student_query)
            or die("Error inserting student into database, line " . __LINE__ . ": " . mysql_error()
              . "<br />Contact Technical Services 314.837.6777 x1250");
          // Output the student's name and picture to make things look fancy
          $alert = "Enjoy Chapel! Attendance has been logged." . "<BR>" . "<font size='+3'>" . $name['firstname'] . " " . $name['lastname'] . "</font><br><!--<img src='../studentpics/" . $pic . "' --><!--height='350px' width='350px-->";
        }
      }
      include('scan_form.php');
    }
  }
}

?>