<?php 
require 'db.php';
require 'passwordhash.php';
session_save_path(dirname(__FILE__) . '/sessions');
session_start();
//PlaceHolder variables
$area='';
//The HTML of the login form
$loginForm = <<<EOD
      <form method="POST" action="index.php">
        <label for='username'>Email </label><br/>
		 <input type="email" id="email" class="span4" name="email" placeholder="Email" max='100'><br/>
        <label for='password'>Password </label><br/>
		 <input type="password" id="password" class="span4" name="password" placeholder="Password" max='100'><br/><br/>
        <button type="submit" name="login" class="btn btn-info ">Sign in</button>
        <button type="submit" name="register" class="btn btn-info">Create a new account</button>
      </form>
EOD;

//The HTML of the register form
$registerForm = <<<EOD
            <form action="index.php" method="POST">
                <label for='firstName'>First Name:</label><br />
                <input type="text" id='firstName' name='firstName' max='100' placeholder='First name' required><br />
                <label for='lastName'>Last Name:</label><br />
                <input type="text" id='lastName' name='lastName' max='100' placeholder='Last name' required><br />
				  <label for='position'>Position:</label><br />
				  <input type="text" id='position' name='position' max='100' placeholder='e.g. Resident Assistant'><br />
				  <label for='position'>Email:</label><br />
                <input type="email" id='email' name='email' max='100' placeholder='email' required><br />
                <label for='password1'>Password:</label><br />
                <input id='password1' type="password" name="password"  max='100' placeholder='Password' required><br />
				  <label for='password2'>Confirm password:</label><br />
                <input id='password2' type="password" name="password2" max='100' placeholder='Password' required><br /><br/>
                <input type="submit" name="signup" value="Register" class="btn btn-info">
            </form>
EOD;

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
            <span class="label label-danger">There was an error registering your account.  Please try again.</span>
EOD;

//The error span for any a login error
$loginError = <<<EOD
            <span class="label label-danger">The provided credentials are incorrect.</span>
EOD;

//The success span for when the user account is successfully created
$userCreated = <<<EOD
            <span class="label label-success">You have successfully created your account.  Proceed with login.</span>
EOD;



//This is the main if statement that chooses which content to display

if(isset($_POST['register'])) {
    //Show the registration form if the Register New User button was clicked
    $area .= $registerForm;
} else if (isset($_POST['signup'])) {
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
        //if there was an error, re-output the registration form
        $area .= $registerForm;
    } else {
        //attempt to create the user
        if(createUser($_POST['email'], $_POST['password'], $_POST['firstName'], $_POST['lastName'],$_POST['position'])) {
			
            //user creation successful; show the login form
            $area .= $userCreated . $loginForm;
        } else {
            //user creation failed for some reason; show the register form
            $area .= $registrationError . $registerForm;
        }

    }
} else if (isset($_POST['login'])) {
    //attempt to the authenticate the user of the Login button was clicked
    if(checkAuth($_POST['email'], $_POST['password'])){
        //the login was successful; show the secret infos
        $_SESSION['auth'] = TRUE;
		//load database data into SESSION
		 $query = "SELECT firstName, lastName, typeID, userID, position, email
              FROM user
              WHERE email ='${_POST['email']}'";

    if(!$result = mysql_query($query)) {
        die("MySQL error: " . mysql_error());
    }
	
	$row = mysql_fetch_assoc($result);
    $_SESSION['firstName'] = $row['firstName'];
	$_SESSION['lastName'] = $row['lastName'];
    $_SESSION['position'] = $row['position'];
    $_SESSION['email'] = $row['email'];
	$_SESSION['typeID'] = $row['typeID'];
	$_SESSION['userID'] = $row['userID'];
	if($_SESSION['typeID']==2){
	   header('Location: http://www.users.miamioh.edu/poncelsc/cse252/semester2project/request-program.php');
	} else if($_SESSION['typeID']==1) {
		header('Location: http://www.users.miamioh.edu/poncelsc/cse252/semester2project/admin-ui.php');
	} else {
		header('Location: http://www.users.miamioh.edu/poncelsc/cse252/semester2project/presenter-ui.php');
	}
exit;
	
	//The content to display when there is an actively authenticate session
$authContent = <<<EOD
            <p>You are currently logged in as ${_SESSION['firstName']} ${_SESSION['lastName']}.</p>
            <a href="?logout" class="btn btn-info">Logout</a>
EOD;
	
	
	
        $area .= $authContent;
    } else {
        //the login failed; reshow the login form
        $area .= $loginError . $loginForm;
    }
}
    else if($_SESSION['typeID']==2){
       header('Location: http://www.users.miamioh.edu/poncelsc/cse252/semester2project/request-program.php');
    } else if($_SESSION['typeID']==1) {
        header('Location: http://www.users.miamioh.edu/poncelsc/cse252/semester2project/admin-ui.php');
    } else if($_SESSION['typeID']==3) {
        header('Location: http://www.users.miamioh.edu/poncelsc/cse252/semester2project/presenter-ui.php');
    }

 else if (isset($_GET['logout'])) {
    //logout request; destroy the session
    logout();
    $area .= $loginForm;
} else if ($_SESSION['auth'] === TRUE) {
    //the user is successfully authenticated; show secret infos
	
    $area .= $authContent;
} else {
    //no other conditions met; show the login form
    $area .= $loginForm;
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

function createUser($email, $password, $firstName, $lastName, $position) {
	if(empty($_POST['firstName']) ||empty($_POST['password'])){
		return FALSE;
		exit;
		}
    //Salt and hash the provided password
    $hasher = new PasswordHash(8, FALSE);
    $hash = $hasher->HashPassword($password);

    //this query inserts the new user record into the table with the salted and hashed password
    $query = sprintf("INSERT INTO user (email, password, firstName, lastName, position) VALUES ('%s', '%s', '%s', '%s','%s')", mysql_real_escape_string($email), $hash, mysql_real_escape_string($firstName), mysql_real_escape_string($lastName), mysql_real_escape_string($position));

    return mysql_query($query);
}

function checkAuth($email, $password) {
    //This query gets the password hash from the user table for the user attempting to login
    $query = sprintf("SELECT password FROM user WHERE email = '%s'", mysql_real_escape_string($email));

    if (! $result = mysql_query($query))
        return FALSE;

    if (mysql_num_rows($result) != 1)
        return FALSE;

    //Hash the provided password and compare to the hash from the database
    $hash = mysql_result($result, 0);
    $hasher = new PasswordHash(8, FALSE);

    return $hasher->CheckPassword($password, $hash);
}

function logout() {
    $_SESSION = array();
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
    session_destroy();
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>HAWKS PMS - Home</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="css/custom.css" rel="stylesheet">
</head>

<div class="container">
  <h3>Welcome to the HAWKS Program Managment System!</h3>
  <div class="row">
    <div class="span4 offset4 well">
      <legend>Please Sign In</legend>
      <?=$area?>
    </div>
  </div>
</div>
</html>
