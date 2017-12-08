<?php
die();
//Inactive script
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

$list = $db->query("SELECT pname,phash FROM Programs");

if($list->num_rows > 0 )
{
	while($row = $list->fetch_assoc())
	{
		echo "<a href=\"load.php?phash=".$row["phash"]."\">".$row["pname"]."</a><br>";
		//echo "PNAME: ".$row["pname"]." PHASH: ".$row["phash"]."<br>";
	}
}
else
{
	echo "Nothing to show<br>";
}

$db->close();
?>
