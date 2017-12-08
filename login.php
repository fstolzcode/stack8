<?php
session_start();
if(isset($_SESSION["uid"]) && isset($_SESSION["CREATED"]))
{
    if( (time() - $_SESSION["CREATED"]) > 3600 )
    {
        //Session too old
        session_unset();
        session_destroy();
    }
    else
    {
		header("Location: mystack.php");
        die();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="#">Stack8</a>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="howto.php">Manual</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="community.php">Shared Programs</a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="#">My Stack</a>
      </li>
    </ul>
  </div>
</nav>

<div class="container">	
	<div class="row p-2" >
  		<div class="col-sm-6">
    		<div class="card">
      			<div class="card-block">
        			<h3 class="card-title">Log in...</h3>
					 <form action="dologin.php" method="post">
        			 Name: <input type="text" name="username" class="form-control" placeholder="Your username"><br>
        			 Password: <input type="password" name="password" class="form-control"><br>
        			<button type="submit" class="btn btn-primary">Login</button>
        			</form>
      			</div>
    		</div>
  		</div>
  		<div class="col-sm-6">
    		<div class="card">
      			<div class="card-block">
        		<h3 class="card-title">... or register</h3>
				<form action="doregister.php" method="post">
                Name: <input type="text" name="username" class="form-control" placeholder="Your username"><br>
                Password: <input type="password" name="password" class="form-control"><br>
                <button type="submit" class="btn btn-success">Register</button>
                </form>
      			</div>
    		</div>
  		</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

</body>
</html>
