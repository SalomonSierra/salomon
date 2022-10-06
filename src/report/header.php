<?php

ob_start();
session_start();
header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
//$locr = $_SESSION['locr'];
//$loc = $_SESSION['loc'];
$loc = $locr = "..";

require_once($locr . "/globals.php");
require_once($locr."/db.php");
require_once($locr."/freport.php");

if(isset($_POST['webcastcode']) && ctype_alnum($_POST['webcastcode'])) {
  header ("Content-transfer-encoding: binary\n");
  header ("Content-type: application/force-download");
  ob_end_flush();
} else {
  header ("Content-Type: text/html; charset=utf-8");
  require $locr.'/version.php';
  if(!ValidSession()) {
    InvalidSession($_SERVER['PHP_SELF']);
    ForceLoad($loc."/index.php");
  }
  if($_SESSION["usertable"]["usertype"] != "admin" && $_SESSION["usertable"]["usertype"] != "coach") {
    IntrusionNotify($_SERVER['PHP_SELF']);
    ForceLoad($loc."/index.php");
  }
  ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Pagina Reporte</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<?php

  echo "<link rel=stylesheet href=\"$loc/Css.php\" type=\"text/css\">\n";

  $contest=$_SESSION["usertable"]["contestnumber"];
  if(($ct = DBContestInfo($contest)) == null)
    ForceLoad($loc."/index.php");
  if(($st = DBContestClockInfo($contest)) == null)
    ForceLoad($loc."/index.php");

  //cabezera
}
?>
