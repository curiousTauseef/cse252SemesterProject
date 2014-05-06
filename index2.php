<?php
	$loginForm = <<<EOD


EOD;
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
<link href="css/custom2.css" rel="stylesheet">
</head>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Project name</a>
        </div>
        <div class="navbar-collapse collapse">
          <form class="navbar-form navbar-right" role="form">
            <div class="form-group">
              <input type="text" placeholder="Email" class="form-control">
            </div>
            <div class="form-group">
              <input type="password" placeholder="Password" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Sign in</button>
          </form>
        </div><!--/.navbar-collapse -->
      </div>
   </div>
<div class="container">
  <h3>Welcome to the HAWKS Program Managment System!</h3>
  <div class="row">
    <div class="span4 offset4 well">
      <legend>Please Sign In</legend>
      <h4>Welcome to the HAWKS! Program Management System. We would be happy to help you setup your prorgam. Please sign in or create you account if you haven't done so already in order to begin scheduling</h4>
      <form class="form-signin" role="form">
        <input type="email" class="form-control" placeholder="Email address" required autofocus>
        <input type="password" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary" type="submit">Sign in</button>
        <button class="btn btn-lg btn-primary" type="createNew">Create Account</button>
      </form>
      <?=$area?>
    </div>
  </div>
</div>
</html>
