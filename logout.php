<?php
session_start();
session_unset();     // unset $_SESSION variable for the run-time 
session_destroy();   
header("Location: mystack.php");
die();
?>
