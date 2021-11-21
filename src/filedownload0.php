<?php
ob_start();
session_start();
require_once("globals.php");

if(!ValidSession()) {
    //talves las librerias de bootstrap
	echo "<html><head><title>Download Page</title>";
    ////funcion para expirar el session y registar 3= debug en logtable
    InvalidSession("filedownload0.php");
    ForceLoad("index.php");//index.php
}

$file="../tools/exportdb.sh";
$ex = escapeshellcmd($file);
$text=shell_exec("./".$file." 2>&1");
//echo "(".$text.")"
if(!$text)
	ob_end_flush();
else{

	$fileName = trim(basename($text));
	$filePath = '../tools/'.$fileName;
	if(!empty($fileName) && file_exists($filePath)){
		header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header ("Cache-Control: no-cache, must-revalidate");
		header ("Pragma: no-cache");
		header ("Content-transfer-encoding: binary\n");
		header ("Content-type: application/force-download");
		header ("Content-Disposition: attachment; filename=" . basename($fileName));

		readfile("../tools/".$fileName);
		@unlink("../tools/".$fileName);
		ob_end_flush();
	}else{
		ob_end_flush();
	}
}

?>
