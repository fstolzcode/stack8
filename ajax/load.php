<?php
$phash = $_GET["phash"];
$localDir = realpath("/var/www/html/programs");
$requestedFile = realpath("/var/www/html/programs/".$phash);

//echo $localDir."<br>";
//echo $requestedFile."<br>";

if (strpos($requestedFile, $localDir) === false) {
    die();
}

//var_dump($_GET);
$myfile = fopen($requestedFile,"r") or die();
echo fread($myfile,filesize($requestedFile));
fclose($myfile);
/*
$dbserver = "localhost";
$dbuser = "stackUser";
$dbpassword = "dcuHO8aWBYfPwEOP";
$dbname = "stack8";

// Create connection
$db = new mysqli($dbserver, $dbuser, $dbpassword, $dbname);

if ($db->connect_error)
{
	echo "Error with DB conection";
	exit(-1);
}

if($query = $db->prepare("SELECT pname FROM Programs WHERE phash=?"))
{
	$query->bind_param("s",$_GET["phash"]);
	$query->execute();
	$query->bind_result($pname);
	$query->fetch();
	echo "NAME: ".$pname."<br>";
	$myfile = fopen($phash,"r"
	$query->close();
}

$db->close();
*/
?>
