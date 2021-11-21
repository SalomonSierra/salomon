<?php
//ob_start();
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
	exit;
	//ob_end_flush();
else{

	$fileName = trim(basename($text));
	$filePath = '../tools/'.$fileName;
	if(!empty($fileName) && file_exists($filePath)){

		$filename = "../tools/".$fileName;

		$chunksize = 5 * (1024 * 1024); //5 MB (= 5 242 880 bytes) per one chunk of file.

		if(file_exists($filename))
		{
		    set_time_limit(300);

		    $size = intval(sprintf("%u", filesize($filename)));

		    header('Content-Type: application/octet-stream');
		    header('Content-Transfer-Encoding: binary');
		    header('Content-Length: '.$size);
		    header('Content-Disposition: attachment;filename="'.basename($filename).'"');

		    if($size > $chunksize)
		    {
		        $handle = fopen($filename, 'rb');

		        while (!feof($handle))
		        {
		          print(@fread($handle, $chunksize));

		          ob_flush();
		          flush();
		        }

		        fclose($handle);
		    }
		    else readfile($path);
			@unlink("../tools/".$fileName);
		    exit;
		}
		else echo 'File "'.$filename.'" does not exist!';
/*
		header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header ("Cache-Control: no-cache, must-revalidate");
		header ("Pragma: no-cache");
		header ("Content-transfer-encoding: binary\n");
		header ("Content-type: application/force-download");
		header ("Content-Disposition: attachment; filename=" . basename($fileName));
		$contenido=file_get_contents("../tools/".$fileName);
		echo $contenido;
		//$gestor=fopen("../tools/".$fileName,"r");
		//$contenido=fread($gestor,filesize("../tools/".$fileName));
		//echo $contenido;
		//fclose($gestor);
		//readfile("../tools/".$fileName);
*/
		//@unlink("../tools/".$fileName);



		exit;
		//ob_end_flush();
	}else{
		exit;
		//ob_end_flush();
	}
}

?>
