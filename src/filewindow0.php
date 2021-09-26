<?php

ob_start();
session_start();
require_once("globals.php");

if(!ValidSession()) {
	echo "<html><head><title>View Page</title>";
    InvalidSession("filewindow.php");
	echo "<script>window.close();</script></html>";
	exit;
}

if(!isset($_GET["oid"]) || !is_numeric($_GET["oid"]) || !isset($_GET["filename"]) ||
   !isset($_GET["check"]) || $_GET["check"]=="") {
	echo "<html><head><title>View Page</title>";
        IntrusionNotify("Bad parameters in filewindow.php");//Parámetros incorrectos en filewindow.php
	echo "<script>window.close();</script></html>";
	exit;
}

$cf = globalconf();
$fname = decryptData(myrawurldecode($_GET["filename"]), session_id() . $cf["key"]);
$msg = '';
if(isset($_GET["msg"]))
	$msg = myrawurldecode($_GET["msg"]);

$p = myhash($_GET["oid"] . $fname . $msg . session_id() . $cf["key"]);

if($p != $_GET["check"]) {
	echo "<html><head><title>View Page</title>";
        IntrusionNotify("Parameters modified in filewindow.php");//Parámetros modificados en filewindow.php
	echo "<script>window.close();</script></html>";
	exit;
}

require_once("db.php");

if ($_GET["oid"]>=0) {
  $c = DBConnect();
  DBExec($c, "begin work");

  if (($lo = DB_lo_open ($c, $_GET["oid"], "r")) === false) {
	echo "<html><head><title>View Page</title>";
	DBExec($c, "rollback work");
	LOGError ("Unable to download file (" . basename($fname) . ")");
	MSGError ("Unable to download file (" . basename($fname) . ")");
	echo "<script>window.close();</script></html>";
	exit;
  }

  header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
  header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header ("Cache-Control: no-cache, must-revalidate");
  header ("Pragma: no-cache");

  if(strpos($fname,".pdf")===false){
	  header ("Content-type: text/plain");//text/htmltext/plain
  }else{
	  header ("Content-type: application/pdf");
  }


//  echo "<html><body>\n";
  if($msg != '') {
//    echo " <a href=\"#\" onClick=\"window.print()\"><h1>".$_GET["msg"]."</h1></a>";
    echo $msg ."\n";
    echo $msg ."\n";
    echo $msg ."\n\n\n";
  }
//  echo "<pre>\n";
    ////funcion para imprimir el contenido de oid inviado y retona true
  if (($PP=DB_lo_read_tobrowser ($_SESSION["usertable"]["contestnumber"],$lo,$c)) === false) {
        header ("Content-type: text/html");
	    echo "<html><head><title>View Page</title>";
	    DBExec($c, "rollback work");
	    LOGError ("Unable to open file (" . basename($fname) . ")");
	    MSGError ("Unable to open file (" . basename($fname) . ")");
	    echo "<script>window.close();</script></html>";
	    exit;
  }

  ob_end_flush();
//  echo "</pre>\n";
  DB_lo_close($lo);
  if($msg != '') {
//    echo " <a href=\"#\" onClick=\"window.print()\"><h1>".$_GET["msg"]."</h1></a>";
    echo "\n\n\n".$msg ."\n";
    echo $msg ."\n";
    echo $msg ."\n";
  }

  DBExec($c, "commit work");
  DBClose($c);
} else {
  header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
  header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header ("Cache-Control: no-cache, must-revalidate");
  header ("Pragma: no-cache");
  //fname descriptado filename
  if (($str = file_get_contents($fname))===false) {
	  header ("Content-type: text/html");
	  echo "<html><head><title>View Page</title>";
	  MSGError ("Unable to open file (" . basename($fname) . ")");
	  LOGError ("Unable to open file (" . basename($fname) . ")");
	  echo "<script>window.close();</script></html>";
	  exit;
  }
  if(strpos($fname,".pdf")===false){
	header ("Content-type: text/plain");
  }else{
	header ("Content-type: application/pdf");
  }
  echo  decryptData($str, $cf["key"]);
  ob_end_flush();
}
?>
