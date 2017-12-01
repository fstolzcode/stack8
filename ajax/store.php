<?php
session_start();
if(!isset($_POST["program"]) || !isset($_POST["name"]) || !isset($_POST["private"]))
{
	echo "Error with parameters";
	exit(-1);
}

if(empty(trim($_POST["program"])) || empty(trim($_POST["name"])) || !is_numeric($_POST["private"]))
{
	echo "Error with parameters";
	exit(-1);
}
if(count(explode("\n", $_POST["program"])) > 8181 || strlen($_POST["program"]) > 819100)
{
	echo "Program too large";
	exit(-1);
}

if(intval($_POST["private"]) == 1 && isset($_SESSION["uid"]) && isset($_SESSION["CREATED"]))
{
    if( (time() - $_SESSION["CREATED"]) > 3600 )
    {
        //Session too old
        session_unset();
        session_destroy();
		die("Your session expired");
    }
}
else if(intval($_POST["private"]) == 1)
{
	die("You have to log in");
}
//die("1");
//var_dump($_POST);

$dbserver = "localhost";
$dbuser = "xxxxr";
$dbpassword = "xxxx";
$dbname = "stack8";

// Create connection
$db = new mysqli($dbserver, $dbuser, $dbpassword, $dbname);

if ($db->connect_error)
{
	echo "Error with DB conection";
	exit(-1);
}

$query = $db->prepare("INSERT IGNORE INTO Programs (uid,pname,phash,public) VALUES (?,?,?,?)");
if(!$query)
{
	echo "Error: ". $db->errno . " ". $db->error;
	exit(-1);
}

$uid = 0;
if(isset($_SESSION["uid"]))
{
	$uid = intval($_SESSION["uid"]);
}
$pname = $_POST["name"];
$pname = htmlspecialchars($pname);
$phash = substr(hash("whirlpool",$pname),0,32);
$sharePublic = 1 - intval($_POST["private"],10);
//echo $pname;
//echo $_POST["program"];
//exit(0);
//echo "pname: ". $pname ." phash: ". $phash ." ";

if(!$query->bind_param("issi",$uid,$pname,$phash,$sharePublic))
{
	echo "Error: ". $query->errno . " ". $query->error;
	exit(-1);
}
if(!$query->execute())
{
	echo "Execution error";
	exit(-1);
}
if($query->affected_rows == 0)
{
	echo "No insertion";
	exit(-1);
}

$db->close();
//echo 1;
$filepath = "/var/www/html/programs/".$phash;
$myfile = fopen($filepath,"w") or die(print_r(error_get_last(),true));
fwrite($myfile,$_POST["program"]);
fclose($myfile);

echo "Successfully stored your program";
?>
