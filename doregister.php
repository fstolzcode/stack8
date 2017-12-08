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
?>
<?php
//Check Parameters
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
$dbpassword = "xxxxx";
$dbname = "stack8";

// Create connection
$db = new mysqli($dbserver, $dbuser, $dbpassword, $dbname);

if ($db->connect_error)
{
	echo "Error with DB conection";
	exit(-1);
}

//prepare Statements
$query = $db->prepare("INSERT IGNORE INTO Users (uname,upw) VALUES (?,?)");
if(!$query)
{
	echo "Error: ". $db->errno . " ". $db->error;
	exit(-1);
}

$newuser = htmlspecialchars($_POST["username"]);
$newpw = password_hash($_POST["password"], PASSWORD_BCRYPT, array("cost" => 12));
//echo "USER: ". $newuser ." PW: ". $newpw ."<br>";
//die();

if(!$query->bind_param("ss",$newuser,$newpw))
{
	echo "Error: ". $query->errno . " ". $query->error;
	exit(-1);
}
if(!$query->execute())
{
	echo "Execution error";
	exit(-1);
}

//User already exists -> No Rows inserted
if($query->affected_rows == 0)
{
	//echo "No insertion";
	//exit(-1);
	header("Location: mystack.php");
	die();
}

$db->close();

header("Location: mystack.php");

?>
