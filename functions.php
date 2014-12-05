<?
include_once("../includes/mysql.php");
function connectDB(){
  connect("databasename");
}

function runQuery($query){
  $results = mysql_query($query);
  echo "<BR>".mysql_error()."<BR>";
  while($dummy = mysql_fetch_row($results)){
    if(!empty($dummy)){
      $return[] = $dummy;
    }
  }
  echo "<BR>".mysql_error()."<BR>";
  return $return;
}

function runShortQuery($query){
  $results = mysql_query($query);
  if(mysql_error())
  	echo "<BR>".mysql_error()."<BR>";
  while($dummy = mysql_fetch_row($results)){
    if(!empty($dummy)){
      $return = $dummy;
    }
  }
  if(mysql_error())
  	$return = 'No Such Student';
  return $return;
}
?>
