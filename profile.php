<?php 
require 'db.php';
require 'passwordhash.php';
session_save_path(dirname(__FILE__) . '/sessions');
session_start();

//The error span for when passwords do not match on the register form
$passwordMatchError = <<<EOD
            <span class="label label-danger">The provided passwords do not match.</span>
EOD;

//The error span for when a username from the register form already exists
$usernameTakenError = <<<EOD
            <span class="label label-danger">The username is already taken.</span>
EOD;

//The error span for any generic errors that occur when the INSERT fails
$registrationError = <<<EOD
            <span class='label label-danger'>There was an error registering the user. Please try again.</span>
EOD;

//The error span for any a login error
$loginError = <<<EOD
            <span class="label label-danger">The provided credentials are incorrect.</span>
EOD;

//The success span for when the user account is successfully created
$userCreated = <<<EOD
            <span class="label label-success">You have successfully created a new user account.</span>
EOD;



if (isset($_GET['logout'])) {
    //logout request; destroy the session
    logout();
}

if (isset($_POST['signup'])) {
    //Attempt to create a new user if the Register button was clicked

    //some simple validation checks
    $errorFlag = FALSE;

    //check to see if both passwords from the registration form match
    if ($_POST['password'] != $_POST['password2']) {
        $errorFlag = TRUE;
        $area .= $passwordMatchError;
    }

    //check to see if the username from the registration form is already taken
    if (isUsernameTaken($_POST['email'])) {
        $errorFlag = TRUE;
        $area .= $usernameTakenError;
    }

    if($errorFlag) {
        
    } else {
        //attempt to create the user
        if(createUser($_POST['email'], $_POST['password'], $_POST['firstName'], $_POST['lastName'], $_POST['position'], $_POST['typeID'])) {
            //user creation successful; show the login form
            $area .= $userCreated;
        } else {
            //user creation failed for some reason; show the register form
            $area .= $registrationError;
        }

    }
}
function isUsernameTaken($email) {
    //this query gets a count of users who already have the provided username
    $query = sprintf("SELECT COUNT(*) FROM user WHERE email = '%s'", mysql_real_escape_string($email));

    //return TRUE if there was a query error; this makes it seem like the user exists when it might now
    if(! $result = mysql_query($query)) {
        return TRUE;
    }

    $count = mysql_result($result, 0);

    if ($count > 0)
        return TRUE;
    else
        return FALSE;
}

function createUser($email, $password, $firstName, $lastName, $position, $typeID) {
	if(empty($_POST['firstName'])||empty($_POST['password'])){
		return FALSE;
		exit;
		}
    //Salt and hash the provided password
    $hasher = new PasswordHash(8, FALSE);
    $hash = $hasher->HashPassword($password);

    //this query inserts the new user record into the table with the salted and hashed password
    $query = sprintf("INSERT INTO user (email, password, firstName, lastName, position, typeID) VALUES ('%s', '%s', '%s', '%s','%s','%s')", mysql_real_escape_string($email), $hash, mysql_real_escape_string($firstName), mysql_real_escape_string($lastName), mysql_real_escape_string($position), mysql_real_escape_string($typeID));

    return mysql_query($query);
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
        <li><a href="admin-ui.php">Program Management</a></li>
      </ul>
         <ul class="nav navbar-nav navbar-left">
        <li class="active" ><a href="profile.php">User Management</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#">My Account</a></li>
        <li><a href="?logout">Sign out</a></li>
      </ul>
EOD;

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>HAWKS PMS - My Account</title>

<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/custom.css" rel="stylesheet">
</head>
<body>
<div class="container">
  <nav class="navbar navbar-default navbar-inverse" role="navigation">
    <div class="container-fluid">
    <? if($_SESSION['typeID']==1){
			echo $admin_nav;} ?>
    </div>
  </nav>
  
  <!-- Profile data -->

	<div class="panel panel-default">
	  <div class="panel-body">
		  
  		    <div class="form-group">
            <form role="form" action="profile.php" method="post">
	    <h3 class="panel-title">Account Type: <?php if($_SESSION['typeID']==1){
			echo "<select name='typeID'><option value='1'>Admin</option><option value='3'>Presenter</option><option value='2'>Customer</option></select>";}
			else if($_SESSION['typeID']==2){
				echo "Customer";
				}else{
					echo "Presenter";
					} ?>
        </h3>
  		      <label for="firstName">First Name</label>
  		      <input type="text" class="form-control" id="firstName" placeholder="Your First Name" name="firstName">
  		    </div>
  		    <div class="form-group">
  		      <label for="lastName">Last Name</label>
  		      <input type="text" class="form-control" id="lastName" placeholder="Your Last Name" name="lastName">
  		    </div>
  		    <div class="form-group">
  		      <label for="position">Position</label>
  		      <input type="text" class="form-control" id="position" placeholder="e.g. Resident Assistant" name="position">
  		    </div>
			<div class="form-group">
				<label for="email">Email address</label> <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email">
			</div>
			<div class="form-group">
		      <label for="newPassword">New Password</label>
		      <input type="password" class="form-control" id="newPassword" name="password" max='100' placeholder="Password">
		    </div>
			
			<div class="form-group">
		      <label for="newPasswordConfirm">Confirm New Password</label>
		      <input type="password" class="form-control" id="newPasswordConfirm" placeholder="Password" name="password2" max='100'>
		    </div>
			 <input class=" btn btn-info" type="submit" value="Save my details">
             <?php if($_SESSION['typeID']==1){
				 echo " <input type='submit' name='signup' value='Add new user' class='btn btn-info'>";}
				 ?>
		  </form>
	
	  </div>

	<? echo $area;
	?> </div>
    <div class="panel panel-default">
    <div class="panel-body">
        <h3 class="panel-title">User List</h3>
      </div>
  </div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> 
<!-- Include all compiled plugins (below), or include individual files as needed --> 
<script src="js/bootstrap.min.js"></script>
</body>
</html>