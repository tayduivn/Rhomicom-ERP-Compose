<?php
// Starting session
session_start();
date_default_timezone_set('UTC');
// Storing session data
$_SESSION["firstname"] = "Peter";
$_SESSION["lastname"] = "Parker";
$lstActvtyStr= date('l jS \of F Y h:i:s A');
//$lstActvtyStr = $dt->format('Y-m-d H:i:s');
$_SESSION["Today's Date"] = $lstActvtyStr;
// Accessing session data
echo 'Hi, ' . $_SESSION["firstname"] . ' ' . $_SESSION["lastname"]."<br/>";
//var_dump($_SESSION);
echo "<br/>";
print_r($_SESSION);
?>
<?php

// Show all information, defaults to INFO_ALL
phpinfo();

?>
