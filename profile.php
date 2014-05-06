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

//The success span for when the user account is successfully updated
$userUpdated = <<<EOD
            <span class="label label-success">You have successfully updated the user account.</span>
EOD;

if (isset($_GET['logout'])) {
    //logout request; destroy the session
    logout();
}

if (isset($_POST['editUser'])) {
  changeUser();
}

	// Updating a user
	
	 if(isset($_POST['update'])) {
     $A=TRUE;

	
            if($_SESSION['changeFirstName'] != $_POST['firstName']) {
                $user_update_query = sprintf("UPDATE user
                                    SET firstName = '%s'
                                    WHERE userID = '%s'", mysql_real_escape_string($_POST['firstName']),mysql_real_escape_string($_SESSION['changeUserId']));
			 global $firstName;
			 $firstName = $_POST['firstName'];
            $result = mysql_query($user_update_query);
            echo mysql_error();}
			
			 if($_SESSION['changeLastName'] != $_POST['lastName']) {
                $user_update_query = sprintf("UPDATE user
                                    SET lastName = '%s'
                                    WHERE userID = '%s'", mysql_real_escape_string($_POST['lastName']),mysql_real_escape_string($_SESSION['changeUserId']));
			 global $firstName;
			 $lastName = $_POST['lastName'];
            $result = mysql_query($user_update_query);
            echo mysql_error();}
			
			     if($_SESSION['changePosition'] != $_POST['position']) {
                $user_update_query = sprintf("UPDATE user
                                    SET position = '%s'
                                    WHERE userID = '%s'", mysql_real_escape_string($_POST['position']),mysql_real_escape_string($_SESSION['changeUserId']));
			 global $position;
			 $position = $_POST['position'];
            $result = mysql_query($user_update_query);
            echo mysql_error();}
			
			 if($_SESSION['changeEmail'] != $_POST['email']) {
                $user_update_query = sprintf("UPDATE user
                                    SET email = '%s'
                                    WHERE userID = '%s'", mysql_real_escape_string($_POST['email']),mysql_real_escape_string($_SESSION['changeUserId']));
			 global $email;
			 $email = $_POST['email'];
            $result = mysql_query($user_update_query);
            echo mysql_error();}
			
						 if($_SESSION['changeTypeNumber'] != $_POST['typeID']) {
                $user_update_query = sprintf("UPDATE user
                                    SET typeID = '%s'
                                    WHERE userID = '%s'", mysql_real_escape_string($_POST['typeID']),mysql_real_escape_string($_SESSION['changeUserId']));
			 global $typeNumber;
			 $typeNumber = $_POST['typeID'];
            $result = mysql_query($user_update_query);
            echo mysql_error();}
				
				if(isset($_POST['password'])){
					if($_POST['password']==$_POST['password2']){
						//Salt and hash the provided password
    $hasher = new PasswordHash(8, FALSE);
    $hash = $hasher->HashPassword($_POST['password']);

    //this query inserts the new user record into the table with the salted and hashed password
    $user_update_query = sprintf("UPDATE user
                                    SET password = '${hash}'
                                    WHERE userID = '%s'",mysql_real_escape_string($_SESSION['changeUserId']));
            $result = mysql_query($user_update_query);
            echo mysql_error();}else{
				$area.=$passwordMatchError;
				$A=FALSE;
				}
						
						}
					
if($A){
	$area .=$userUpdated;}
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
else {
  //$area .= $registerForm;
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

function changeUser() {
  $query = "SELECT user.userID, user.firstName, user.lastName, user.position, user.email, type.typeName, user.typeID
                FROM user, type
                WHERE user.typeID = type.typeID AND user.userID = '${_POST['uid']}'";
      if(!$result = mysql_query($query)) {
        die("MySQL error: " . mysql_error());
    }
    list($userID, $first, $last, $position1, $email1, $type1, $typeNumber1) = mysql_fetch_row($result);
	global $userNumber;
	$userNumber=$userID;
	$_SESSION['changeUserId']=$userID;
    global $firstName;
    $firstName = $first;
	$_SESSION['changeFirstName']=$first;
    global $lastName;
    $lastName = $last;
	$_SESSION['changeLastName']=$last;
    global $position;
    $position = $position1;
	$_SESSION['changePosition']=$position1;
    global $email;
    $email = $email1;
	$_SESSION['changeEmail']=$email1;
    global $type;
    $type = $type1;
	
	global $typeNumber;
	$typeNumber=$typeNumber1;
	$_SESSION['changetypeNumber']=$typeNumber1;
}

$admin_nav =<<< EOD
<ul class="nav navbar-nav navbar-left">
        <li><a href="admin-ui.php">Program Management</a></li>
      </ul>
         <ul class="nav navbar-nav navbar-left">
        <li class="active" ><a href="profile.php">User Management</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
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
			echo $admin_nav;} else {echo "<ul class='nav navbar-nav navbar-left'><li><a href = 'http://www.users.miamioh.edu/poncelsc/cse252/semester2project'>Return</a></li></ul>";} ?>
    </div>
  </nav>
  
  <!-- Profile data -->

	<div class="panel panel-default">
	  <div class="panel-body">
		  




    <form role="form" action="profile.php" method="post">
      <div class="form-group">
    <h3 class="panel-title">Account Type: <?php if($_SESSION['typeID']==1){
      echo "<select name='typeID' id='typeID'>";
	  if(isset($_POST['editUser'])){
		  
		  echo"<option value='${typeNumber}' selected>${type}</option>";
		  if($typeNumber==1){
			  echo "<option value='3'>Presenter</option>
			  <option value='2'>Customer</option>";
			  }elseif($typeNumber==2){
				  		  echo "<option value='3'>Presenter</option>
			  <option value='1'>Administrator</option>";
				  }elseif($typeNumber==3){
				  		  echo "<option value='2'>Customer</option>
			  <option value='1'>Administrator</option>";
				  }
		  };
	  echo "</select>";}
      else if($_SESSION['typeID']==2){
        echo "Customer";
        }else{
          echo "Presenter";
          } ?>
        </h3>
            <label for="firstName">First Name</label>
            <input type="text" class="form-control" id="firstName" placeholder="Your First Name" name="firstName" value = "<?php if ($firstName == NULL && $_SESSION['typeID'] !=1) {echo $_SESSION['firstName'] ;} else {echo $firstName;} ?>">
          </div>
          <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" class="form-control" id="lastName" placeholder="Your Last Name" name="lastName" value = "<?php if ($lastName == NULL && $_SESSION['typeID'] !=1) {echo $_SESSION['lastName'];} else {echo $lastName;}  ?>">
          </div>
          <div class="form-group">
            <label for="position">Position</label>
            <input type="text" class="form-control" id="position" placeholder="e.g. Resident Assistant" name="position" value = "<?php if ($position == NULL && $_SESSION['typeID'] !=1) {echo $_SESSION['position'];} else {echo $position;}  ?>">
          </div>
      <div class="form-group">
        <label for="email">Email address</label> <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value = "<?php if ($email == NULL && $_SESSION['typeID'] !=1) {echo $_SESSION['email'];} else {echo $email;}  ?>">  
      </div>
      
      <? if(!isset($_POST['editUser'])||$_SESSION['userID']==$_SESSION['changeUserId']){
		  
      echo "<div class='form-group'>
          <label for='newPassword'>New Password</label>
          <input type='password' class='form-control' id='newPassword' name='password' max='100' placeholder='Password' >
        </div>
      
      <div class='form-group'>
          <label for='newPasswordConfirm'>Confirm New Password</label>
          <input type='password' class='form-control' id='newPasswordConfirm' placeholder='Password' name='password2' max='100'>
        </div>";}?>
        
        
<? if(isset($_POST['uid'])) {
	echo "<input class='btn btn-info' type='submit' name='update' value='Save my details'>";} ?>
  <? if($_SESSION['typeID']!=1) {
  echo "<input class='btn btn-info' type='submit' name='update' value='Save my details'>";} ?>
             <?php if($_SESSION['typeID']==1){
         echo " <input type='submit' name='signup' value='Add new user' class='btn btn-info'>";}
         ?>
      </form>
    </div></div>

    <?php 
    if($_SESSION['typeID']==1) {
      $query = "SELECT user.userID, user.firstName, user.lastName, user.position, user.email, type.typeName
                FROM user, type
                WHERE user.typeID = type.typeID";
      if(!$result = mysql_query($query)) {
        die("MySQL error: " . mysql_error());
    }
    if(isset($_GET['first']))
        $query .= " ORDER BY user.firstName";
    else if (isset($_GET['last']))
        $query .= " ORDER BY user.lastName";
    else if (isset($_GET['position']))
        $query .= " ORDER BY user.position";
    else if (isset($_GET['email']))
        $query .= " ORDER BY user.email";
    else if (isset($_GET['position']))
        $query .= " ORDER BY type.typeName";

    if(!$result = mysql_query($query)) {
        die("MySQL error: " . mysql_error());
    }

      echo "<div class='panel panel-default'>
	  	$area 
      <div class='panel-body'>
        <h3 class='panel-title'>User List</h3><br>
        <table class = 'table table-striped'>
          <tr>
            <th><a href='?first'>First</a></th>
            <th><a href='?last'>Last</a></th>
            <th><a href='?position'>Position</a></th>
            <th><a href='?email'>Email</a></th>
            <th><a href='?type'>User Type</a></th>
            <th>Edit User</th>
          </tr>";
        while(list($userID, $first, $last, $position1, $email1, $type1) = mysql_fetch_row($result)) {
          echo "<tr>
                  <td>$first</td>
                  <td>$last</td>
                  <td>$position1</td>
                  <td>$email1</td>
                  <td>$type1</td>
                  <td><form action = 'profile.php' method = 'POST'>
                    <button name = 'editUser' class = 'btn btn-sm btn-primary'>Edit User</button>
                    <input type = 'hidden' value ='$userID' name = 'uid'>
                  </form></td>
                </tr>";
        }
        echo "</table></div>";
		

	
      }
    ?>
  </div>
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> 
  <!-- Include all compiled plugins (below), or include individual files as needed --> 
  <script src="js/bootstrap.min.js"></script>
  <!-- EVENTHANDLER ********************************** --> 
<script>
        		var x=1;
		$( "#typeID").ready(function() {
			if(x ==1){
				x++;
			
			//function(event){
			//alert("hi");
			var urlId = "/poncelsc/cse252/semester2project/service/server.php/account-types";
<!-- AJAX CALL ********************************** -->			
			$.ajax({
  			url: urlId,
			})
  			.done(function(data) {
				console.log($("#typeID").text());
				if($("#typeID").text()==''){
				for(var i = 0; i < 3; i++ ) {
			$("#typeID").append(new Option(data[i].text, data[i].value));
					}
			}
  			});
			//}	
			}
		});
		</script>
</body>
</html>