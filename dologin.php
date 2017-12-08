<?php
//Session handler
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

//DB data
$dbserver = "localhost";
$dbuser = "stackUser";
$dbpassword = "xxxx";
$dbname = "stack8";

// Create connection
$db = new mysqli($dbserver, $dbuser, $dbpassword, $dbname);

if ($db->connect_error)
{
	echo "Error with DB conection";
	exit(-1);
}

//prepare Statement
$query = $db->prepare("SELECT uid, upw FROM Users WHERE uname=?");
if(!$query)
{
	echo "Error: ". $db->errno . " ". $db->error;
	exit(-1);
}

//escape characters
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

//Check the results, if user exists and password is the same
$result = $query->get_result();

$row = $result->fetch_assoc();
if(is_null($row))
{
	header("Location: mystack.php");
	//echo "Nothing selected";
	//exit(-1);
	die();
}

if(!password_verify($_POST["password"],$row["upw"]))
{
	//echo "Wrong password";
	header("Location: mystack.php");
	die();
}

$_SESSION["uid"] = $row["uid"];
$_SESSION["CREATED"] = time();

$db->close();
header("Location: mystack.php");

?>
