<?php
//Get the hash, go to directoy, read the file, check for path exploit, write results into the repsonse
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
?>
