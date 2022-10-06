<?php
//solamente para global
ob_start();
session_start();
require_once("globals.php");

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

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Code</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link rel="stylesheet" href="<?php echo  $loc; ?>/Css.php" type="text/css">
        <link rel="stylesheet" href="../bootstrap4/css/modern-business.css">
        <!--para editor de codigo-->
        <script src="../codemirror/lib/codemirror.js"></script>
        <link rel="stylesheet" href="../codemirror/lib/codemirror.css">
        <script src="../codemirror/mode/groovy/groovy.js"></script>
        <script src="../codemirror/mode/clike/clike.js"></script>
        <!--pegado de codigo-->
        <link rel="stylesheet" href="../codemirror/addon/fold/foldgutter.css"/>
        <script src="../codemirror/addon/fold/foldcode.js"></script>
        <script src="../codemirror/addon/fold/foldgutter.js"></script>
        <script src="../codemirror/addon/fold/brace-fold.js"></script>
        <script src="../codemirror/addon/fold/comment-fold.js"></script>
        <link rel="stylesheet" href="../codemirror/theme/dracula.css">
    </head>
    <body class="p-0">

            <textarea id="textsource"name="name" rows="20" cols="80"><?php if (($PP=DB_lo_read_tobrowser ($_SESSION["usertable"]["contestnumber"],$lo,$c)) === false)echo"No se puede leer";?></textarea>
            <?php
            ob_end_flush();
           //  echo "</pre>\n";
            DB_lo_close($lo);

            DBExec($c, "commit work");
            DBClose($c);
            }
            ?>



		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

	    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    </body>
</html>
<script language="javascript">


var editor=CodeMirror.fromTextArea
(document.getElementById('textsource'),{
    mode: "text/groovy",    // Darse cuenta de resaltado de código maravilloso

    mode: "text/x-c++src", // Darse cuenta del resaltado de código C
    mode: "text/x-java", // Darse cuenta del resaltado de código Java
    lineNumbers: true,  // Mostrar número de línea
    theme: "dracula",   // Establecer tema
    lineWrapping: true, // Código plegable
    foldGutter: true,
    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
    matchBrackets: true    // Correspondencia de corchetes
    //readOnly: true, // solo lectura

});
editor.setSize("680","599");
editor.setOption("readOnly", true); // Similar a esto
//editor.setValue("\n\n\n\n\n\n\n\n\n");
</script>
