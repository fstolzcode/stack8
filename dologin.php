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
		echo "Already logged in";
		die();
    }
}
if(!isset($_POST["username"]) || !isset($_POST["password"]))
{
	echo "Error with parameters";
	exit(-1);
}

if(empty(trim($_POST["username"])) || empty($_POST["password"]))
{
	echo "Error with parameters";
	exit(-1);
}


$dbserver = "localhost";
$dbuser = "xxxx";
$dbpassword = "xxxx";
$dbname = "stack8";

// Create connection
$db = new mysqli($dbserver, $dbuser, $dbpassword, $dbname);

if ($db->connect_error)
{
	echo "Error with DB conection";
	exit(-1);
}

$query = $db->prepare("SELECT uid, upw FROM Users WHERE uname=?");
if(!$query)
{
	echo "Error: ". $db->errno . " ". $db->error;
	exit(-1);
}

$providedUser = htmlspecialchars($_POST["username"]);

if(!$query->bind_param("s",$providedUser))
{
	echo "Error: ". $query->errno . " ". $query->error;
	exit(-1);
}
if(!$query->execute())
{
	echo "Execution error";
	exit(-1);
}

$result = $query->get_result();

$row = $result->fetch_assoc();
if(is_null($row))
{
	echo "Nothing selected";
	exit(-1);
}

if(!password_verify($_POST["password"],$row["upw"]))
{
	echo "Wrong password";
}

$_SESSION["uid"] = $row["uid"];
$_SESSION["CREATED"] = time();


$db->close();

?>
