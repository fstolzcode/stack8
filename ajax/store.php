<?php
if(!isset($_POST["program"]) || !isset($_POST["name"]))
{
	echo "Error with parameters";
	exit(-1);
}

if(empty(trim($_POST["program"])) || empty(trim($_POST["name"])))
{
	echo "Error with parameters";
	exit(-1);
}
if(count(explode("\n", $_POST["program"])) > 8181 || strlen($_POST["program"]) > 819100)
{
	echo "Program too large";
	exit(-1);
}
//var_dump($_POST);

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

$query = $db->prepare("INSERT IGNORE INTO Programs (uid,pname,phash) VALUES (?,?,?)");
if(!$query)
{
	echo "Error: ". $db->errno . " ". $db->error;
	exit(-1);
}

$uid = 0;
$pname = $_POST["name"];
$pname = htmlspecialchars($pname);
$phash = substr(hash("whirlpool",$pname),0,32);
//echo $pname;
//echo $_POST["program"];
//exit(0);
//echo "pname: ". $pname ." phash: ". $phash ." ";

if(!$query->bind_param("iss",$uid,$pname,$phash))
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
