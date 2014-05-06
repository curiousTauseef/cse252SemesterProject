<?php 
require 'db.php';
require 'passwordhash.php';
session_save_path(dirname(__FILE__) . '/sessions');
session_start();
$message='';
$location="Select Your Location";
$request = <<<EOD
            <span class="label label-success">Your request was submitted. You will be contacted soon. Thank you!</span>
EOD;


if (isset($_GET['logout'])) {
    //logout request; destroy the session
    logout();
} else if (isset($_POST['submitRequest'])) {
	
	//Check if topic, preferred dates, location, target audience and account info have been provided
	$output_form = true;
	if (empty($_POST["topic"]))
     {$topicErr = "You must provide a topic description.";
	 $output_form = false;
	 }
   else
     {$topic = test_input($_POST["topic"]);}
   
   if (empty($_POST["Date1"]))
     {$date1Err = "Date1 is required";
	 $output_form = false;}
   else
     {$date1 = test_input($_POST["Date1"]);}
	 
	  if (empty($_POST["Date2"]))
     {$date2Err = "Date2 is required";
	 $output_form = false;}
   else
     {$date2 = test_input($_POST["Date2"]);}
	 
	  if (empty($_POST["Date3"]))
     {$date3Err = "Date3 is required";
	 $output_form = false;}
   else
     {$date3 = test_input($_POST["Date3"]);}
	 
	    if (empty($_POST["Time1"]))
     {$time1Err = "Time1 is required";
	 $output_form = false;}
   else
     {$time1 = test_input($_POST["Time1"]);}
	 
	  if (empty($_POST["Time2"]))
     {$time2Err = "Time2 is required";
	 $output_form = false;}
   else
     {$time2 = test_input($_POST["Time2"]);}
	 
	  if (empty($_POST["Time3"]))
     {$time3Err = "Time3 is required";
	 $output_form = false;}
   else
     {$time3 = test_input($_POST["Time3"]);}

   if (empty($_POST["goals"]))
     {$goalsErr= "Goals are required";
	 $output_form = false;}
   else
     {$goals = test_input($_POST["goals"]);}
	 
	if ($_POST["Location"]=='Select Your Location')
     {$locationErr= "Location is required";
	 $output_form = false;}
   else
     {$location = test_input($_POST["Location"]);}
	 
	   if (empty($_POST["audience"]))
     {$audienceErr= "Target Audience is required";
	 $output_form = false;}
   else
     {$audience = test_input($_POST["audience"]);}
	 
	 	   if (empty($_POST["Account"]))
     {$accountErr= "Account Number is required";
	 $output_form = false;}
   else
     {$account = test_input($_POST["Account"]);}

	if($output_form){
    createRequest($topic, $date1, $date2, $date3, $time1, $time2, $time3, $goals, $location,$audience,$account);
	 $message.=$request;}
}

	//Make user input safe
	function test_input($data)
{
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return $data;
}


function createRequest($topic, $date1, $date2, $date3, $time1, $time2, $time3, $goals, $location, $audience,$account) {
	$p=105;
 $query = sprintf("INSERT INTO `chaudha`.`requests` (`topicRequest`, `goals`, `location`, `targetAudience`, `accountNumber`, `requestorID`) VALUES ('%s', '%s', '%s', '%s', '%s', '${_SESSION['userID']}')", mysql_real_escape_string($topic), mysql_real_escape_string($goals),mysql_real_escape_string($location), mysql_real_escape_string($audience),mysql_real_escape_string($account));

  mysql_query($query);
  $lastRequestID = mysql_insert_id();
  
  
  $timeIndex=array();
  $dateIndex=array();
  if(!empty($time1)){
  array_push($timeIndex,$time1);
  array_push($dateIndex,$date1);}
  if(!empty($time2)){
   array_push($timeIndex,$time2);
   array_push($dateIndex,$date2);}
  if(!empty($time3)){
   array_push($timeIndex,$time3);
   array_push($dateIndex,$date3);
  }
  for($i=0;$i < count($timeIndex); $i++){
	  
$query = sprintf("INSERT INTO `chaudha`.`prefTime` (`requestID`, `dateVal`, `timeVal`) VALUES ('${lastRequestID}', '%s', '%s')", mysql_real_escape_string($dateIndex[$i]), mysql_real_escape_string($timeIndex[$i]));

 mysql_query($query);
	  
	  }
	  
	  //set default presenters
	  
$query ="INSERT INTO requestPresenter (`userID`, `requestID`) VALUES ('24', '${lastRequestID}')";

 mysql_query($query);
 echo mysql_error();
$query ="INSERT INTO requestPresenter (`userID`, `requestID`) VALUES ('28', '${lastRequestID}')";

mysql_query($query);
echo mysql_error();	  

 
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>HAWKS PMS - Request Form</title>
<!-- Bootstrap -->
<!--<link href="../semester2project/css/overcast/jquery-ui-1.10.4.css" rel="stylesheet">-->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/custom.css" rel="stylesheet">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

</head>
<body>
<div class="container">
  <nav class="navbar navbar-default navbar-inverse" role="navigation">
    <div class="container-fluid">
      <ul class="nav navbar-nav navbar-left">
        <li class="active"><a href="#">My Requests</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="profile.php">My Account</a></li>
        <li><a href="?logout">Sign out</a></li>
      </ul>
    </div>
  </nav>
  
  <!-- Previous requests -->
  
  <?php
  $userI = $_SESSION['userID'];
      $query = $query = sprintf("SELECT requests.requestorID, requests.timestamp, requests.location, requests.status, requests.timeID, prefTime.dateVal, prefTime.timeVal
                FROM requests JOIN prefTime ON requests.timeID = prefTime.timeID
                WHERE requests.requestorID = '%s'", mysql_real_escape_string($userI));
      if(!$result = mysql_query($query)) {
        die("MySQL error: " . mysql_error());
    }
        echo   "<div class='panel panel-default'>
    <div class='panel-heading'>
      <h3 class='panel-title'>My Requests</h3>
    </div>
    <div class='panel-body'>
     <div class='table-responsiv'> <table class='table table-bordered'>
        <thead>
          <tr>
            <th>#</th>
            <th>Timestamp of Request</th>
            <th>Location</th>
            <th>Date and Time</th>
            <th>Status</th>
          </tr>";
          $i = 1;
        while(list($rqID, $timestamp, $location, $status, $timeID, $date, $time) = mysql_fetch_row($result)) {
          if($status == "pending"){
          echo "<tr>
                  <td>$i</td>
                  <td>$timestamp</td>
                  <td>$location</td>
                  <td>pending</td>
                  <td>$status</td>

                </tr>";
              }
              else {
                echo "<tr>
                  <td>$i</td>
                  <td>$timestamp</td>
                  <td>$location</td>
                  <td>$date, $time</td>
                  <td>$status</td>

                </tr>";
              }
                $i++;
        }
        echo "</table></div></div></div>";
    

  

    ?>


  <!-- Place a new request -->
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Place a new request</h3>
      <?=$message?>
    </div>
    <div class="panel-body">
      <form id="request" role="form" action="request-program.php" method="POST">
        <div class="form-group">
          <label for="topic">Topic Requested</label><span class="label alert-danger">* <?php echo $topicErr;?></span><br/>
          <textarea class="form-control" id="topic" form="request" name = "topic" placeholder="Please describe the topic that you want to be addressed by our program."><?php echo $topic;?></textarea>
        </div>
        <div class="form-group">
          <div class="calendar center-block">
			  <div class="row">
				  <div class="col-md-8">
            <label for="Date1">Preferred Date 1 : </label><input name="Date1" type="text" required="required" class="datepicker" value="<?php echo $date1;?>"><label for="Time1">Start Time : </label><input name="Time1" type="text" required="required" class="timepicker" value="<?php echo $time1;?>"><span class="label alert-danger">* <?php echo $date1Err;?><?php echo $time1Err;?></span><br/>
			 <label for="Date2">Preferred Date 2 : </label><input name="Date2" type="text" class="datepicker" value="<?php echo $date2;?>"><label for="Time2">Start Time : </label><input type="text" name="Time2" class="timepicker" value="<?php echo $time2;?>"><span class="label alert-danger">* <?php echo $date2Err;?><?php echo $time2Err;?></span><br/>
			  <label for="Date3">Preferred Date 3 : </label><input name="Date3" type="text" class="datepicker" value="<?php echo $date3;?>"><label for="Time3">Start Time : </label><input type="text" name="Time3" class="timepicker" value="<?php echo $time3;?>"><span class="label alert-danger">* <?php echo $date3Err;?><?php echo $time3Err;?></span>
		  		</div>
		  <div class="col-md-4" ><h4 class="bg-warning">Please note that requests need to be made 14 days in advance.</h4>

		  </div>
          </div>
        </div>
        </div>
        <div class="form-group">
          <label for="goals">Goals of the Program</label><span class="label alert-danger">* <?php echo $goalsErr;?></span><br/>
          <textarea form="request" class="form-control" id="goals" name = "goals" placeholder="Goal(s) of the Program: What would you like the audience to learn or experience?"><?php echo $goals;?></textarea>
        </div>
        <div class="form-group" id="trigger">
          <label for="location">Location</label><span class="label alert-danger">* <?php echo $locationErr;?></span><br/>
          <select id="Location" name = "Location" form="request">
            <option ><?php echo $location;?></option>
          </select>
        </div>
        <div class="form-group">
          <label for="target">Target Audience</label><span class="label alert-danger">* <?php echo $audienceErr;?></span><br/>
          <input type="text" id="audience" form="request" name="audience" placeholder="Target Audience" value="<?php echo $audience;?>">
        </div>
        <div class="form-group">
          <label for="account">Organization Account Number</label><span class="label alert-danger">* <?php echo $accountErr;?></span><br/>
          <small>(to be charged if the program is cancelled—see Cancellation Policy below)</small><br />
          <input name="Account" type="text" id="account" form="request" placeholder="Account Number" value="<?php echo $account;?>">
        </div>
        <input class=" btn btn-info" type="submit" name = 'submitRequest' value="Submit Request">
        <div class="bg-warning">
          <h3>Cancellation Policy:</h3>
          <small>We will call you 48 hours in advance of the program to confirm details. We retain the right to charge a cancellation fee if you cancel less than 48 hours in advance of the scheduled, or if at the scheduled day and time of the program, either the program organizer or agreed upon audience number does show up (“No Show Policy”). The Cancellation Fee is $25.00 and is charged at our discretion unless extenuating circumstances can be shown.”</small> </div>
      </form>
    </div>
  </div>
</div>
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="js/bootstrap.min.js"></script>

</body>
<script src="js/jquery-ui-1.10.4.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css"/>
<script src="js/jquery.datetimepicker.js"></script>
<script type="text/javascript">
	$( ".datepicker" ).datetimepicker({ minDate: "+14",
timepicker:false,
format:'m.d.Y' });
	$('.timepicker').datetimepicker({
		datepicker:false,
		format:'H:i'
	});
	
</script>
<!-- EVENTHANDLER ********************************** --> 
<script>
        		var x=1;
		$( "#trigger").mouseenter(function() {
			if(x ==1){
				x++;
			
			//function(event){
			//alert("hi");
			var urlId = "/poncelsc/cse252/semester2project/service/server.php/locations";
<!-- AJAX CALL ********************************** -->			
			$.ajax({
  			url: urlId,
			})
  			.done(function( data ) {
			for(var i = 0; i < data.length; i++ ) {
				$("#Location").append(new Option(data[i], data[i]));
				
			}
  			});
			//}	
			}
		});
		</script>
</html>