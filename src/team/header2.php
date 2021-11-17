<?php
ob_start();
header("Expires: ".gmdate("D, d M Y H:i:s")." GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

header("Content-Type: text/html; charset=utf-8");
session_start();
require_once('../version.php');

require_once('../globals.php');
require_once('../db.php');
$runteam='run.php';

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Team</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link rel="stylesheet" href="<?php echo  $loc; ?>/Css.php" type="text/css">
<?php
//funcion retorna true o false si no existe usertable en session false si es id diferente false
//si ho hay usertable en session  FALSE
//de usuario es true su multi llogion  TRUE
//si el ip son diferentes FALSE

if(!ValidSession()){
    InvalidSession("team/index.php");////funcion para expirar el session y registar 3= debug en logtable
    ForceLoad("../index.php");//index.php
}

//inicio logica de ingreso a la competencia
if($_SESSION["usertable"]["contestnumber"]==0){
    if(!isset($_GET["contest"])){
        ForceLoad("contest.php");//index.php
    }else{
        if($_GET["contest"]==0)
            ForceLoad("contest.php");//index.php
        if(($ct = DBContestInfo($_GET["contest"])) == null)
        	ForceLoad("contest.php");//index.php
        $_SESSION["usertable"]["contestnumber"]=$_GET["contest"];
    }
}else{
    if(isset($_GET["contest"])){
        if($_GET["contest"]!=$_SESSION["usertable"]["contestnumber"])
            $_SESSION["usertable"]["contestnumber"]=$_GET["contest"];

    }
    if(($ct = DBContestInfo($_SESSION["usertable"]["contestnumber"])) == null)
    	ForceLoad("contest.php");//index.php
}
$contestnumber=$_SESSION["usertable"]["contestnumber"];
//fin logica de ingreso a la competencia

$contestinfo=DBContestInfo($_SESSION["usertable"]["contestnumber"]);
if(isset($_GET["id"])){
    $_SESSION["usertable"]["private"]=$_GET["id"];
    $_SESSION["usertable"]["count"]=1;
}
if($contestinfo["contestprivate"]=='t'){
    if($contestinfo["contestpassword"]!=$_SESSION["usertable"]["private"]){
        if($_SESSION["usertable"]["count"]==0){
            ValidContest($_SESSION["usertable"]["contestnumber"]);
        }else{
            MSGError("Contraseña Incorrecta");
            ForceLoad("contest.php");
        }
    }
}


if($_SESSION["usertable"]["usertype"] != "team"){
    IntrusionNotify("team/index.php");
    ForceLoad("../index.php");//index.php
}

?>
        <script language="javascript" src="../reload.js"></script>
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
        <body onload="Comecar()" onunload="Parar()">


            <div class="col-sm-12 bg-primary">
                <div class="row bg-primary">
                    <div class="col-sm-4 bg-danger">
                        <label for=""class="text-white"><?php list($clockstr,$clocktype)=siteclock2($_SESSION["usertable"]["contestnumber"]); echo $clockstr; ?></label>
                    </div>
                    <div class="col-sm-8">
                        <span class="text-dark font-italic">NOMBRE:</span>

                        <!--para codigo php-->
                        <?php
                        echo "..::".strtoupper($contestinfo["contestname"])."::..";
                        $ds = DIRECTORY_SEPARATOR;
                        if($ds=="") $ds = "/";
                        //var/www/salomon/src/private/runtmp/run-contest-1-site1-user2.php
                        $runtmp = $_SESSION["locr"] . $ds . "private" . $ds . "runtmp" . $ds . "run-contest" . $_SESSION["usertable"]["contestnumber"] .
                        	"-user" . $_SESSION["usertable"]["usernumber"] . ".php";
                        $doslow=true;
                        //file_exists — Comprueba si existe un fichero o directorio
                        if(file_exists($runtmp)) {

                        	if(($strtmp = file_get_contents($runtmp,FALSE,NULL,0,1000000)) !== FALSE) {
                        		$postab=strpos($strtmp,"\t");
                        		$conf=globalconf();
                        		if(isset($conf['doenc']) && $conf['doenc'])
                        		  $strcolors = decryptData(substr($strtmp,$postab+1,strpos($strtmp,"\n")-$postab-1),$conf['key'],'');
                        		else
                        		  $strcolors = substr($strtmp,$postab+1,strpos($strtmp,"\n")-$postab-1);
                        		$doslow=false;
                        		$rn=explode("\t",$strcolors);
                        		$n=count($rn);
                        		for($i=1; $i<$n-1;$i++) {
                        			echo "<img alt=\"".$rn[$i]."\" width=\"10\" ".
                        				"src=\"" . balloonurl($rn[$i+1]) . "\" />\n";
                        			$i++;
                        		}
                        	} else unset($strtmp);
                        }
                        if($doslow) {
                            //(contestnumber,usersite,user)
                            //funcion para capturar el color y otros de un problema resuelto de n usuario en runtable

                            $run = DBUserRunsYES($_SESSION["usertable"]["contestnumber"],
                        						 $_SESSION["usertable"]["usernumber"]);
                        	$n=count($run);
                        	for($i=0; $i<$n;$i++) {
                        		echo "<img alt=\"".$run[$i]["colorname"]."\" width=\"10\" ".
                        			"src=\"" . balloonurl($run[$i]["color"]) . "\" />\n";
                        	}
                        }
                        //tiempo emergente
                        if(!isset($_SESSION["popuptime"]) || $_SESSION["popuptime"] < time()-120) {
                        	$_SESSION["popuptime"] = time();

                        	if(($st = DBContestClockInfo($_SESSION["usertable"]["contestnumber"])) != null) {
                                // devuelve una matriz, donde cada línea tiene los atributos
                                // número (número de aclaración)
                                // marca de tiempo (hora de creación del clar)
                                // problema (nombre del problema)
                                // estado (situación de aclaración)
                                // pregunta (clar texto)
                                // respuesta (texto con la respuesta)
                                /*$clar = DBUserClars($_SESSION["usertable"]["contestnumber"],
                        							$_SESSION["usertable"]["usersitenumber"],
                        							$_SESSION["usertable"]["usernumber"]);
                        		for ($i=0; $i<count($clar); $i++) {
                        			if ($clar[$i]["anstime"]>$_SESSION["usertable"]["userlastlogin"]-$st["sitestartdate"] &&
                        				$clar[$i]["anstime"] < $st['siteduration'] &&
                        				trim($clar[$i]["answer"])!='' && !isset($_SESSION["popups"]['clar' . $i . '-' . $clar[$i]["anstime"]])) {
                        				$_SESSION["popups"]['clar' . $i . '-' . $clar[$i]["anstime"]] = "(Clar for problem ".$clar[$i]["problem"]." answered)\n";
                        			}
                        		}*/
                                // devuelve una matriz, donde cada línea tiene los atributos
                                // número (número de ejecución)
                                // marca de tiempo (tiempo de creación de ejecución)
                                // problema (nombre del problema)
                                // estado (situación de ejecución)
                                // respuesta (texto con la respuesta)
                                // lenguaje y extension
                        		$run = DBUserRuns($_SESSION["usertable"]["contestnumber"],
                        						  $_SESSION["usertable"]["usernumber"]);
                        		for ($i=0; $i<count($run); $i++) {
                        			if ($run[$i]["anstime"]>$_SESSION["usertable"]["userlastlogin"]-$st["conteststartdate"] &&
                        				$run[$i]["anstime"] < $st['contestlastmileanswer'] &&
                        				$run[$i]["ansfake"]!="t" && !isset($_SESSION["popups"]['run' . $i . '-' . $run[$i]["anstime"]])) {
                        				$_SESSION["popups"]['run' . $i . '-' . $run[$i]["anstime"]] = "(Run ".$run[$i]["number"]." result: ".$run[$i]["answer"] . ')\n';
                        			}
                        		}
                        	}

                        	$str = '';
                        	if(isset($_SESSION["popups"])) {
                        		foreach($_SESSION["popups"] as $key => $value) {
                        			if($value != '') {
                        				$str .= $value;
                        				$_SESSION["popups"][$key] = '';
                        			}
                        		}
                        		if($str != '') {
                                    //TIENES NOTICIAS
                        			MSGError('TIENES NOTICIAS:\n' . $str . '\n');
                        		}
                        	}
                        }

                        ?>
                    </div>
                </div>
            </div>

            <nav class="navbar navbar-expand-md navbar-light bg-dark">

              <div class="container">


                <button class="navbar-toggler" data-toggle="collapse" data-target="#first" type="button" >
                  <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse"id="first">

                  <a class="navbar-brand text-primary"href="contest.php">

                    Salomon
                  </a>
                  <ul class="navbar-nav mr-auto">

                     <?php
                        if($contestinfo["usernumber"]==$_SESSION["usertable"]["usernumber"]){
                            echo "<li class=\"nav-item\"> <a class=\"nav-link text-primary\" href=\"problemcontest.php?contest=".$_SESSION["usertable"]["contestnumber"]."\">Problemas set</a> </li>";
                        }
                      ?>
                    <li class="nav-item"> <a class="nav-link text-primary" href="problem.php?contest=<?php echo $_SESSION["usertable"]["contestnumber"]; ?>">Problemas</a> </li>
                    <li class="nav-item"> <a class="nav-link text-primary" href="run.php?contest=<?php echo $_SESSION["usertable"]["contestnumber"];  ?>">Envios</a> </li><!---->
                    <li class="nav-item"> <a class="nav-link text-primary" href="score.php?contest=<?php echo $_SESSION["usertable"]["contestnumber"];  ?>">Score</a> </li><!--index.php-->
                    <!--<li class="nav-item"> <a class="nav-link text-primary" href="user.php">Clarifications</a> </li>
                    <li class="nav-item"> <a class="nav-link text-primary" href="problem.php">Tasks</a> </li>
                    <li class="nav-item"> <a class="nav-link text-primary" href="../index1.php">Backups</a> </li>-->
                    <!--
                    <li class="nav-item"> <a class="nav-link text-primary" href="option.php">Options</a> </li>
                    -->


                  </ul>

                </div>

                <ul class="navbar-nav d-inline-block">
                    <li class="nav-item d-inline-block dropdown">
                        <!--para username-->
                        <a class="btn btn-outline-primary dropdown-toggle mx-1" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION["usertable"]["userfullname"]; ?></a>
                        <!--nosotros estamos utilizando directo en aqui para update po ahora no...:-->
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a href="" class="dropdown-item" data-toggle="modal" data-target="#updateModal">Update</a>
                            <a href="#" class="dropdown-item">Message</a>
                        </div>
                    </li>

                    <li class="nav-item d-inline-block"> <a class="btn btn-outline-primary" href="../index.php">Logout</a> </li>

                </ul>
            </div>
        </nav>

        <!--PARA UPDATE-->
        <?php
        include '../optionlower.php';
        ?>
