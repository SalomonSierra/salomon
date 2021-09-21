<?php

ob_start();
session_start();
require_once("globals.php");

if(!ValidSession()) {
    //talves las librerias de bootstrap
	echo "<html><head><title>Download Page</title>";
    ////funcion para expirar el session y registar 3= debug en logtable
    InvalidSession("filedownload.php");
    ForceLoad("index.php");//index.php
}

if(!isset($_GET["oid"]) || !is_numeric($_GET["oid"]) || !isset($_GET["filename"]) ||
   !isset($_GET["check"]) || $_GET["check"]=="") {
	echo "<html><head><title>Download Page</title>";
    //Parámetros incorrectos en filedownload.php
        IntrusionNotify("Bad parameters in filedownload.php");
        ForceLoad("index.php");//index.php
}

$cf = globalconf();
$fname = decryptData(myrawurldecode($_GET["filename"]), session_id() . $cf["key"]);

if(isset($_GET["msg"]))
	$p = myhash($_GET["oid"] . $fname . myrawurldecode($_GET["msg"]) . session_id() . $cf["key"]);
else
	$p = myhash($_GET["oid"] . $fname . session_id() . $cf["key"]);

if($p != $_GET["check"]) {
        //Parámetros modificados en filedownload.php.
        echo "<html><head><title>View Page</title>";
        IntrusionNotify("Parameters modified in filedownload.php. ");
        ForceLoad("index.php");//index.php
}

require_once("db.php");

if ($_GET["oid"]>=0) {
	$c = DBConnect();
	DBExec($c, "begin work");

	if (($lo = DB_lo_open ($c, $_GET["oid"], "r")) === false) {
		echo "<html><head><title>Descargas</title>";
		DBExec($c, "rollback work");
		LOGError ("No se puede descargar el archivo (" . basename($fname) . ")");
		MSGError ("No se puede descargar el archivo (" . basename($fname) . ")");
		ForceLoad("index.php");//index.php
	}

	header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-transfer-encoding: binary\n");
	header ("Content-type: application/force-download");
//header ("Content-type: application/octet-stream");
//if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE"))
//	header("Content-Disposition: filename=" .$_GET["filename"]); // For IE
//else
	header ("Content-Disposition: attachment; filename=" . basename($fname));
	ob_end_flush();
    //DB_lo_read_tobrowser funcion para imprimir el contenido de oid inviado y retona true
	if (DB_lo_read_tobrowser ($_SESSION["usertable"]["contestnumber"],$lo,$c) === false) {
		echo "<html><head><title>Download Page</title>";
		DBExec($c, "rollback work");
		LOGError ("No se puede descargar el archivo (" . basename($fname) . ")");
		MSGError ("No se puede descargar el archivo (" . basename($fname) . ")");
		ForceLoad("index.php");
	}
	DB_lo_close($lo);

	DBExec($c, "commit work");
	DBClose($c);
} else {
	header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
//header ("Content-type: application/octet-stream");
//if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE"))
//	header("Content-Disposition: filename=" .$_GET["filename"]); // For IE
//else
	header ("Content-Disposition: attachment; filename=" . basename($fname));

	if (($str=file_get_contents($fname))===false) {
		header ("Content-type: text/html");
		echo "<html><head><title>Descargas</title>";
        //No es posible abrir el archivo
		MSGError ("No es posible abrir el archivo (" . basename($fname) . ")");
		LOGError ("No es posible abrir el archivo (" . basename($fname) . ")");
		ForceLoad('index.php');//index.php
		exit;
	} else {
		header ("Content-transfer-encoding: binary\n");
		header ("Content-type: application/force-download");
		echo decryptData($str, $cf["key"]);
	}
	ob_end_flush();
}

?>
