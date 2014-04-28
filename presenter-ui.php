<?php 
require 'db.php';
require 'passwordhash.php';
session_save_path(dirname(__FILE__) . '/sessions');
session_start();

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

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>HAWKS PMS - Presenter</title>
<!-- Bootstrap -->
<link href="../semester project/css/overcast/jquery-ui-1.10.4.css" rel="stylesheet">
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
        <li class="active"><a href="#">Program Management</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#">My Account</a></li>
        <li><a href="?logout">Sign out</a></li>
      </ul>
    </div>
  </nav>
  
  <!-- Previous requests -->
  
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Program List</h3>
    </div>
    <div class="panel-body">
      <div class='table-responsive'>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th>Location</th>
              <th>Requester</th>
              <th>Date and Time</th>
              <th>Presenter 1</th>
              <th>Presenter 2</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td >1</td>
              <td>Shriver MPR</td>
              <td>Jane Doe</td>
              <td>04/19/2014 19:00</td>
              <td>Name 1</td>
              <td>Name 2</td>
              <!-- This button trigger a modal showing more info abou the program-->
              <td><input type="button" value="Details" class="btn btn-info" data-toggle="modal" data-target="#myModal"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <!-- Modal -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Program Details</h4>
        </div>
        <div class="modal-body">
        <div class="table-responsive"> <table class="table table-bordered">
        <thead>
          <tr>
            <th>Target Audience</th>
            <th>Goals of Program</th>
            <th>Toppic Requested</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>50</td>
            <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</td>
            <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</td>
          </tr>
        </tbody>
      </table></div>
    </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
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

</script>
</html>