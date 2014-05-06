<?php 
require 'db.php';
require 'passwordhash.php';
session_save_path(dirname(__FILE__) . '/sessions');
session_start();

$message='';
if (isset($_GET['logout'])) {
    //logout request; destroy the session
    logout();
}

function logout() {
    $_SESSION = array();
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
    session_destroy();
	header('Location: http://www.users.miamioh.edu/poncelsc/cse252/semester2project');
}

$admin_nav =<<< EOD
<ul class="nav navbar-nav navbar-left">
        <li class="active"><a href="#">Program Management</a></li>
      </ul>
         <ul class="nav navbar-nav navbar-left">
        <li ><a href="profile.php">User Management</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="?logout">Sign out</a></li>
      </ul>
EOD;

//The success span for when the user account is successfully updated
$requestUpdated = <<<EOD
            <span class="label label-success">Your changes have been applied.</span>
EOD;

$presenterError = <<<EOD
            <span class="label label-danger">Presenters must be distinct.</span>
EOD;

	  if(isset($_POST['submit'])){
		  
		  
		  //Find requests that have changed
		  global $changedRequestNumbers;
		  $changedRequestNumbers=array();
		  foreach($_POST AS $key => $value){
			  if(strpos($key, 'changeRequestNumber') === 0){
			  array_push($changedRequestNumbers, $value);
			  }
			  
			  }
		  if(count($changedRequestNumbers)!=0){
			  $error=FALSE;
			  for($i=0;$i<count($changedRequestNumbers);$i++){
				  $rnumber=$changedRequestNumbers[$i];
				  
				  if($_POST["presenter2ID${rnumber}"]==$_POST["presenter1ID${rnumber}"]){
					  $message.=$presenterError;
					  $error=TRUE;
					  break;
					  }
				//  echo "!!!!Presenter2ID".$_POST["presenter2ID${rnumber}"];
				  //echo "!!!!Presenter1ID".$_POST["presenter1ID${rnumber}"];
				   //change time
                $user_update_query = sprintf("UPDATE requests
                                    SET timeID = '%s'
                                    WHERE requests.requestID = '${rnumber}'", mysql_real_escape_string($_POST["timeID${rnumber}"]));
            $result = mysql_query($user_update_query);
            echo mysql_error();
			
			//change status
                $user_update_query = sprintf("UPDATE requests
                                    SET status = '%s'
                                    WHERE requests.requestID = '${rnumber}'", mysql_real_escape_string($_POST["status${rnumber}"]));
            $result = mysql_query($user_update_query);
            echo mysql_error();
			
			//change presenter1
					
                $user_update_query = sprintf("UPDATE requestPresenter
                                    SET userID = '%s'
									   WHERE requestPresenter.requestID = '${rnumber}' LIMIT 1", mysql_real_escape_string($_POST["presenter1ID${rnumber}"]));
									   $p=$_POST["presenter1ID${rnumber}"];
            $result = mysql_query($user_update_query);
            echo mysql_error();
			
			//change presenter2	
                $user_update_query = sprintf("UPDATE requestPresenter
                                    SET userID = '%s'
									   WHERE requestPresenter.requestID = '${rnumber}' AND requestPresenter.userID !='${p}'", mysql_real_escape_string($_POST["presenter2ID${rnumber}"]));
            $result = mysql_query($user_update_query);
            echo mysql_error();
			
				  }
			  
			  
			  
			  };
			  if(!$error){
			  $message.=$requestUpdated;
			  }
		  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>HAWKS PMS - Admin</title>
<!-- Bootstrap -->
<!--<link href="../semester project/css/overcast/jquery-ui-1.10.4.css" rel="stylesheet">-->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/custom.css" rel="stylesheet">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
</head>
<body>
<div class="container">
  <nav class="navbar navbar-default navbar-inverse" role="navigation">
    <div class="container-fluid">
     <?= $admin_nav?>
    </div>
  </nav>
  
  <!-- Previous requests -->
  <form role="form" action="admin-ui.php" method="post">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Program List</h3>
      <?=$message?>
    </div>
     <? if($_SESSION['typeID']==1) {
		 //First we need to know how many requests there are, and put the ID's in an array. Also, we need to know if the database already contains information re selected time, status and presenters
      $query = "SELECT requests.requestID FROM `requests`";
      if(!$result = mysql_query($query)) {
        die("MySQL error: " . mysql_error());
	  }
	  global $number_of_requests;
	  $number_of_requests = mysql_num_rows($result);
	  
	  $requestIDArray = array();
	  
	   while ($row = mysql_fetch_row($result)) {
            array_push($requestIDArray, $row[0]);
				}
		$query="SELECT requests.requestID, prefTime.timeID,prefTime.dateVal,prefTime.timeVal, requests.status, user.userID, user.firstName, user.lastName
FROM requests JOIN prefTime ON requests.timeID = prefTime.timeID JOIN requestPresenter ON requests.requestID = requestPresenter.requestID JOIN user ON requestPresenter.userID = user.userID";
 if(!$result = mysql_query($query)) {
        die("MySQL error: " . mysql_error());
	  }
	$count=3;
  while ($row = mysql_fetch_row($result)) {
	  $_SESSION["time${row[0]}"]=$row[1];
	  $_SESSION["dateVal${row[0]}"]=$row[2];
	  $_SESSION["timeVal${row[0]}"]=$row[3];
	  $_SESSION["status${row[0]}"]=$row[4];
	  if($count%2==1){
	  $_SESSION["presenter1${row[0]}"]=$row[5];
	  $_SESSION["presenter1firstName${row[0]}"]=$row[6];
	  $_SESSION["presenter1lastName${row[0]}"]=$row[7];
	  }
	  if($count%2==0){
		  $_SESSION["presenter2${row[0]}"]=$row[5];
		   $_SESSION["presenter2firstName${row[0]}"]=$row[6];
	  $_SESSION["presenter2lastName${row[0]}"]=$row[7];
		  }
		  
          $count++;
				}

	  }?>
    
    <div class="panel-body">
      <div class='table-responsive'>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th>Location</th>
              <th>Requester</th>
              <th>Requested Slots</th>
              <th>Status</th>
              <th>Presenter 1</th>
              <th>Presenter 2</th>
              <th>&nbsp;</th>
            </tr>
          </thead>
          <tbody>
          <? 
		  // Let's print the row numbers
		  for($i=0; $i<$number_of_requests; $i++){
			
			   $requestID=$requestIDArray[$i];
			  $number=$i+1;
			  echo"<tr><td>$number</td>\n";
			   //lets get the locations and print them
			   $query = "SELECT location, requestID
 						FROM requests
 						WHERE requestID = '$requestID'";

      if(!$result = mysql_query($query)) {
        die("MySQL error: " . mysql_error());
		}
		$row = mysql_fetch_row($result);
		     echo "<td>${row[0]}<input type='hidden' name='requestID' value='${row[1]}' id='r'></td>\n";
				
			  //lets get the requestor and print him
			   $query = "SELECT user.firstName, user.lastName
 						FROM user JOIN requests ON user.userID = requests.requestorID
 						WHERE requestID = '$requestID'";

      if(!$result = mysql_query($query)) {
        die("MySQL error: " . mysql_error());
		}
		$row = mysql_fetch_row($result);
		     echo "<td>${row[0]} ${row[1]}</td>\n";
			  //lets get the 3 time slots and print them 
			   $query = "SELECT prefTime.timeID, prefTime.dateVal, prefTime.timeVal
 	 FROM requests JOIN prefTime ON requests.requestID = prefTime.requestID
 	 WHERE requests.requestID ='$requestID'";

      if(!$result = mysql_query($query)) {
        die("MySQL error: " . mysql_error());
		}
		echo "<td><select name='timeID${requestID}' class='trigger'>";
		$x=1;
		while ($row = mysql_fetch_row($result)){
			if(!empty($_SESSION["time${requestID}"])&&$x==1){
				$a=$_SESSION["time${requestID}"];
				$b=$_SESSION["dateVal${requestID}"];
				$c=$_SESSION["timeVal${requestID}"];
				echo "<option value='${a}'>$b $c</option>";
				}
            echo "<option value='${row[0]}'>${row[1]} ${row[2]}</option>\n";
			$x++;
		}
			echo"</select></td>";
			 //let's print the statuses
		     echo "<td><select name='status${requestID}' class='trigger'>";
			 if(!empty($_SESSION["status${requestID}"])){
				$a=$_SESSION["status${requestID}"];
				echo "<option value='${a}'>$a</option>";
				}
	echo"<option value='Pending'>Pending</option>
			 <option value='Approved'>Approved</option>
			 <option value='Denied'>Denied</option>
			 <select></td>\n";
			 
			 		  //lets get the Presenters and print them
			   $query = "SELECT user.userID, user.firstName, user.lastName 
 	 FROM user
 	 WHERE user.typeID ='3'";

      if(!$result = mysql_query($query)) {
        die("MySQL error: " . mysql_error());
		}
		echo "<td><select name='presenter1ID${requestID}' class='trigger'>";
		$x=1;
		while ($row = mysql_fetch_row($result)){
			 if(!empty($_SESSION["presenter1${requestID}"])&&$x==1){
					  $a=$_SESSION["presenter1${requestID}"];
				  	$b=$_SESSION["presenter1firstName${requestID}"];
	  				$c=$_SESSION["presenter1lastName${requestID}"];
				echo "<option value='${a}'>$b $c</option>";
				}
            echo "<option  value='${row[0]}'>${row[1]} ${row[2]}</option>\n";
				$x++;
		}
		echo"</select></td>";
		//second presenter
		 if(!$result = mysql_query($query)) {
        die("MySQL error: " . mysql_error());
		}
		echo "<td><select name='presenter2ID${requestID}' class='trigger'>";
		$x=1;
		while ($row = mysql_fetch_row($result)){
			if(!empty($_SESSION["presenter2${requestID}"])&&$x==1){
					  $a=$_SESSION["presenter2${requestID}"];
				  	$b=$_SESSION["presenter2firstName${requestID}"];
	  				$c=$_SESSION["presenter2lastName${requestID}"];
				echo "<option value='${a}'>$b $c</option>";
				}
            echo "<option value='${row[0]}'>${row[1]} ${row[2]}</option>\n";
				$x++;
		}
		echo"</select></td>";
		
		echo"<td><input type='button' value='Details' class='btn btn-info' data-toggle='modal' data-target='#myModal${i}'></td></tr>";	   
		  }  
			  ?>
        
          </tbody>
        </table>
      </div>
    </div>
    <input class=" btn btn-info" type="submit" value="Save changes" name='submit'>
  </div>
  </form>
  <? for($i=0; $i<$number_of_requests; $i++){
	  $requestID=$requestIDArray[$i];
  echo "<div class='modal fade' id='myModal${i}' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog'>
      <div class='modal-content'>
        <div class='modal-header'>
          <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
          <h4 class='modal-title' id='myModalLabel'>Program Details</h4>
        </div>
        <div class='modal-body'>
          <div class='table-responsive'>
            <table class='table table-bordered'>
              <thead>
                <tr>";
				//get info
				$query="SELECT timeStamp, targetAudience, accountNumber, goals, topicRequest
FROM requests
WHERE requestID ='$requestID'";
 if(!$result = mysql_query($query)) {
        die("MySQL error: " . mysql_error());
		}
		$row = mysql_fetch_row($result);
		echo"
		<th>Timestamp of Request</th>
                  <th>Target Audience</th>
                  <th>Organizations Account</th>
                  <th>Goals of Program</th>
                  <th>Toppic Requested</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td >${row[0]}</td>
                  <td>${row[1]}</td>
                  <td>${row[2]}</td>
                  <td>${row[3]}</td>
                  <td>${row[4]}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class='modal-footer'>
	       <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
      </div>
    </div>
  </div>
		";
  
  } ?>
  
</div>
</div>
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="js/bootstrap.min.js"></script>
</body>
<script src="js/jquery-ui-1.10.4.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/ >
<script src="js/jquery.datetimepicker.js"></script>
<script type="text/javascript">
	$( ".datepicker" ).datetimepicker({ minDate: "+14",
timepicker:false,
format:'m.d.Y' });
	$('.timepicker').datetimepicker({
		datepicker:false,
		format:'H:i'
	});
	
$( ".trigger").change(function() {
	
  var a = $(this).parent().parent().find("#r").val();
  $(this).parent().parent().find("#r").append("<input type='hidden' name='changeRequestNumber"+a+"' value='"+a+"'>");
  
});

</script>
</html>