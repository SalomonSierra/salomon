<?php
require_once("db.php");

if(isset($_SESSION["locr"]))
	$locr=$_SESSION["locr"];
else
	$locr='.';
//si clock existe y currenttime esta presente entonces valor del currenttime imprimir caso contrario 0
if(isset($_GET["clock"]) && $_GET["clock"]==1) {
	ob_start();
	header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-Type: text/html; charset=utf-8");
	session_start();
	ob_end_flush();


        ////funcion para que devuelve la informacion de la competencia activa
		//$ct=DBGetActiveContest();
		$ct=DBContestInfo($_SESSION["usertable"]["contestnumber"]);
		$contest=$ct['contestnumber'];
		//$localsite=1;

    //(contest,site, c=null,msg=true)
    //retorna el resultado de un contest y sitio dado mas la info. de siterunning, currenttime,siteendeddate
    //de la tabla sitimetable;
	if (($blocal = DBContestClockInfo($contest)) == null) {
		echo "0";
		exit;
	}
	if(isset($blocal['currenttime']))
		echo $blocal["currenttime"];
	else echo "0";
	exit;
}
//si existe remote saca info de usuario y tiene que ser sitio sino index.php
if(isset($_GET['remote'])) {
	ob_start();
	header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-Type: text/html; charset=utf-8");
	session_start();
	ob_end_flush();

	if (isset($_SESSION["usertable"])) {
        //(contest,site,usernumber,c=null,hashpass=true)
        //esta funcion retorna la info. del usuario, si hashpass es true userpassword= userpassword.usersessionextra
        $tmp = DBUserInfo($_SESSION["usertable"]["usernumber"]);
		$_SESSION["usertable"]['usersessionextra'] = $tmp['usersessionextra'];
	} else {
		IntrusionNotify("scoretable1");//para logout expire
        ForceLoad("index.php");//index.php
	}
	if(!isset($_SESSION['usertable']['usertype'])) {
		IntrusionNotify("scoretable2");//para sitio expire
        ForceLoad("index.php");//index.php
	}
}

if(!ValidSession()) {
	InvalidSession("scoretable.php");//expire
	ForceLoad("index.php");//index.php
}
$loc = $_SESSION["loc"];
if(!isset($detail)) $detail=true;
if(!isset($final)) $final=false;
$scoredelay["admin"] = 10;
$scoredelay["score"] = 60;
$scoredelay["team"] = 30;
$scoredelay["judge"] = 20;
$scoredelay["staff"] = 30;
$actualdelay = 30;
//para asignar a $actualdelay el valor segun el tipo
if(isset($scoredelay[$_SESSION["usertable"]["usertype"]])) $actualdelay = $scoredelay[$_SESSION["usertable"]["usertype"]];
$ds = DIRECTORY_SEPARATOR;
if($ds=="") $ds = "/";
//var/www/salomon/src/private/scoretmp/usertype-username.php
$zxzcontest=$_SESSION["usertable"]["contestnumber"];
$scoretmp = $_SESSION["locr"] . $ds . "private" . $ds . "scoretmp" . $ds ."contest-".$zxzcontest.'-'.$_SESSION["usertable"]["usertype"] . '-' . $_SESSION["usertable"]["username"] . ".php";
$redo = TRUE;
//si existe si no no hace nada
if(file_exists($scoretmp)) {
    //file_get_contents — Transmite un fichero completo a una cadena
	if(($strtmp = file_get_contents($scoretmp,FALSE,NULL,0,5000000)) !== FALSE) {
        //Asignar variables como si fueran un array
        //El sscanf() Esta función analiza el aporte de una cadena de acuerdo con un
        //formato especificado. El sscanf() Esta función analiza una cadena en variables basadas en la cadena de formato.
		list($d) = sscanf($strtmp,"%*s %d");
		if($d > time() - $actualdelay) {
			$redo = FALSE;
		}
	}
}
//
if(isset($_GET["remote"])) {
    //
    $privatedir = $_SESSION['locr'] . $ds . "private";
    $remotedir = $_SESSION['locr'] . $ds . "private" . $ds . "remotescores";
    $destination = $remotedir . $ds ."scores.zip";
    //Devuelve true si filename existe y es escribible. El argumento nombre_archivo
    // puede ser el nombre de un directorio, permitiendo así comprobar si el directorio es escribible.
    //el directorio remotescores/sores.zip es existe y es ecribibre entra
    //saca la informacion los datos con run,todo y guarda en un archivo
    if(is_writable($remotedir)) {
        //is_readable — Indica si un fichero existe y es legible
        //
	    if($redo || !is_readable($destination)) {

            //fopen con x crea un fichero de solo escritura, si existe devuelve false
	        if(($fp = @fopen($destination . ".lck",'x')) !== false) {

		        if (($s = DBContestClockInfo($_SESSION["usertable"]["contestnumber"])) == null)
			        ForceLoad("index.php");//index.php

		        $level=$s["contestscorelevel"];
		        $data0 = array();
        		if($level>0) {
                    //contest, siteuser, 0, -1
                    ////devuelve el dato ordenado score, el resultado de consulta en run,problem,answer,user
					//añalizando...
        			list($score,$data0) = DBScoreContest($_SESSION["usertable"]["contestnumber"],
        											  0, -1);
        		}
                /////funcion para que devuelve la informacion de la competencia activa
		        //$ct=DBGetActiveContest(); mejorado
				$ct=DBContestInfo($_SESSION["usertable"]["usernumber"]);
		        //$localsite=1;
                //var/www/salomon/src/private/score_localsite_1_x
		        //$fname = $privatedir . $ds . "score_localsite_" . $localsite . "_x"; // . md5($_SERVER['HTTP_HOST']);
		        $fname = $privatedir . $ds . "score_localcontest_" . $ct["contestnumber"] . "_x"; // . md5($_SERVER['HTTP_HOST']);
				//tomar en cuenta..
				//Si filename no existe, se crea el fichero. De otro modo, el fichero
                // existente se sobrescribe, a menos que la bandera FILE_APPEND esté establecida.
                //Genera una representación almacenable de un valor.
                //Este tipo de codificación está diseñado para que datos binarios sobrepasen
                //capas de transporte que no son de 8-bits 100%, como por ejemplo el cuerpo de un E-Mail.
                //La codificación en Base64 hace que los datos sean un 33% más largos que los datos originales.
                @file_put_contents($fname . ".tmp",base64_encode(serialize($data0)));
                //rename — Renombra un fichero o directorio
		        @rename($fname . ".tmp",$fname . ".dat");

		        $data0 = array();
        		if($level>0) {
                    //contest, siteuser, 1, -1
                    //devuelve score ordenano y datos
        			list($score,$data0) = DBScoreContest($_SESSION["usertable"]["contestnumber"],
        											  1, -1);
        		}
				//$ct=DBGetActiveContest(); mejorado
        		$ct=DBContestInfo($_SESSION["usertable"]["usernumber"]);
        		//$localsite=1;
                //var/www/salomon/src/private/remotescores/score_site/1_1_x
        		$fname = $remotedir . $ds . "score_contest" . $ct["contestnumber"] . "_" . $ct["contestnumber"] . "_x"; // . md5($_SERVER['HTTP_HOST']);
                //crea o escribe en el fichero
                @file_put_contents($fname . ".tmp",base64_encode(serialize($data0)));
        		@rename($fname . ".tmp",$fname . ".dat");
        		//scoretransfer($fname . ".dat", $localsite);
		        //funcion para empaquetar a .zip
        		if(@create_zip($remotedir,glob($remotedir . '/*.dat'),$fname . ".tmp") != 1) {
        			LOGError("No se puede crear un archivo .zip de score");//No se puede crear un archivo zip de puntuación
        			if(@create_zip($remotedir,array(),$fname . ".tmp") == 1)
        				@rename($fname . ".tmp",$destination);
        		} else {
        			@rename($fname . ".tmp",$destination);
        		}
        		@fclose($fp);

		        //getMainXML($_SESSION["usertable"]["contestnumber"]);
		        //unlink — Borra un fichero
		        @unlink($destination . ".lck");
	        } else {
                //filemtime Esta función devuelve el momento de cuándo los bloques de información de un archivo fueron escritos, es decir,
                // el momento de cuándo el contenido del archivo se modificó.
			    if(file_exists($destination . ".lck") && filemtime($destination . ".lck") < time() - 180)
				    @unlink($destination . ".lck");
	        }
	    }
    }
    if(is_numeric($_GET["remote"])) {

		if($_GET["remote"]==-42) {
			echo file_get_contents($destination);
		} else {

			if (($s = DBContestClockInfo($_SESSION["usertable"]["contestnumber"])) == null)
				ForceLoad("index.php");//index.php

			$level=$s["contestscorelevel"];
			$score = array();
			if($level>0) {
                //contest, siteuser, 1, -1,remote
                //devuelve score ordenano y datos
				list($score,$data0) = DBScoreContest($_SESSION["usertable"]["contestnumber"],
								  1, -1, $_GET["remote"]);
			}
			echo base64_encode(serialize($score));
		}
     } else {
         echo base64_encode(serialize(array()));
     }
  exit;
}

//
if(!$redo) {
	$conf=globalconf();
    ////funcion para descriptar datos
	if(isset($conf['doenc']) && $conf['doenc'])
	   $strtmp = decryptData(substr($strtmp,strpos($strtmp,"\n")),$conf["key"],'score');
	else $strtmp = substr($strtmp,strpos($strtmp,"\n"));
	if($strtmp=="") $redo=TRUE;
}
//
if($redo) {
	$strtmp = "<script language=\"JavaScript\" src=\"" . $loc . "/hide.js\"></script>\n";
    // recibe el número del concurso
    // devuelve una matriz, donde cada línea tiene el número de atributos (número de problema), problema (nombre del problema),
    // descfilename (nombre de archivo con descripción del problema) y descoid (objeto grande con descripción)
    $pr = DBGetProblems($_SESSION["usertable"]["contestnumber"]);

	$ct=DBContestInfo($_SESSION["usertable"]["contestnumber"]);
	$contest=$ct['contestnumber'];
	$duration=$ct['contestduration'];

	if(!isset($hor)) $hor = -1;
	if($hor>$duration) $hor=$duration;

	$level=$s["contestscorelevel"];
	if($level<=0) $level=-$level;
	else {
		$des=true;
	}

	if (($s = DBContestClockInfo($_SESSION["usertable"]["contestnumber"])) == null)
		ForceLoad("index.php");//index.php
	$score = DBScore($_SESSION["usertable"]["contestnumber"], $ver, $hor*60, $s["contestglobalscore"]);
	////funcion para score ordenado
	if ($_SESSION["usertable"]["usertype"]!="score" && $_SESSION["usertable"]["usertype"]!="admin" && $level>3) $level=3;

	$minu = 3;
    //funcion para sacar la informacion de las tablas runtable, answertable, problemtable, usertable. con condiciones

	$rn = DBRecentNews($_SESSION["usertable"]["contestnumber"],
					   $ver, $minu);
	if(count($rn)>0 && $level>3) {
		$strtmp .= "<table border=0><tr>";
		$strtmp .= "<td>News (last ${minu}'): &nbsp;</td>\n";
		for($i=0; $i<count($rn); $i++) {
			$strtmp .= "<td width=200>";
			if($rn[$i]["yes"]=='t') {
				$strtmp .= "<img alt=\"".$rn[$i]["colorname"].":\" width=\"28\" ".
					"src=\"" . balloonurl($rn[$i]["color"]) ."\" />";
                    //bolloonurl me envia un color en RGB
                    //retorna un globo creado con el color dado.
			}
			else
				$strtmp .= "<img alt=\"\" width=\"22\" ".
					"src=\"$loc/images/bigballoontransp-blink.gif\" />\n";

			$strtmp .= $rn[$i]["problemname"] . ": " . $rn[$i]["userfullname"] . " (" . ((int) ($rn[$i]["time"]/60)) . "')";
			$strtmp .= "</td>\n";
		}
		$strtmp .= "</tr></table>";
	}
	if($hor>=0) {
        //A partir de $ hor minutos. Próximo:
		$strtmp .= "<center>A partir $hor minutos. Próximo: ";
		for($h=-30; $h<40; $h+=10) {
			if($hor+$h>=0 && $h!=0) {
                //
				$strtmp .= "<a href=\"$loc/report/score.php?p=0&hor=" . ($hor+$h) . "\">";
				if($h>0) $strtmp .= "+";
				$strtmp .= "$h</a>&nbsp;";
			}
		}
		$strtmp .= "</center><br>";
	}
	if(is_readable($_SESSION["locr"] . $ds . 'private' . $ds . 'score.sep')) {
		/*$rf=file($_SESSION["locr"] . $ds . 'private' . $ds . 'score.sep');
		$fta=true;
		for($rfi=1;$rfi<=count($rf);$rfi++) {
			$lin = explode('#',trim($rf[$rfi-1]));
			if(isset($lin[1]) && $_SESSION["usertable"]["usertype"]!='admin') {
				$arr=explode(' ',trim($lin[1]));
				for($arri=0;$arri<count($arr);$arri++)
					if($arr[$arri] != '' && preg_match($arr[$arri],$_SESSION["usertable"]["username"])) break;
				if($arri>=count($arr)) continue;
			}
			$lin = trim($lin[0]);
			if($lin=='') continue;
		    if($fta) { $fta=false; $strtmp .= "<br><img src=\"$loc/images/smallballoontransp.png\" alt=\"\" onload=\"javascript:toggleGroup($rfi)\"> <b>Available scores:</b> \n"; }
            $grname=explode(' ',$lin);
			$class=1;
			reset($score);
			while(list($e,$c) = each($score)) {
				if(!isset($score[$e]['classingroup'])) $score[$e]['classingroup']=array();
				for($k=1;$k<count($grname);$k++) {
					if($score[$e]['site']==$grname[$k]) {
						$score[$e]['classingroup'][$rfi]=$class;
						$class++;
					}
					else if(strpos($grname[$k],'/') >= 1) {
						$u1 = explode('/',$grname[$k]);
						if(isset($u1[1]) && $score[$e]['user'] >= $u1[0] && $score[$e]['user'] <= $u1[1]) {
							if(!isset($u1[2]) || $u1[2]==$score[$e]['site']) {
							$score[$e]['classingroup'][$rfi]=$class;
							$class++;
							}
						}
					}

				}
			}
			if($class>1)
				$strtmp .= "<a href=\"#\" onclick=\"javascript:toggleGroup($rfi)\">" . $grname[0] . "</a> ";
		}
		$strtmp .= "<br>\n";*/
	} else {
        //reset — Establece el puntero interno de un array a su primer elemento
		reset($score);
		$class = 1;
		while(list($e,$c) = each($score)) {
			$score[$e]['classingroup'][1]=$class;
		    $class++;
		}
	}



	$strtmp .= "<br>\n<table id=\"myscoretable\" class=\"table table-bordered table-dark table-hover\">\n<thead>\n <tr>\n  <th scope=\"col\">#</th>\n  <th scope=\"col\">Usuario</th>\n  <th scope=\"col\">Nombre</th>\n";
	if(!$des) {
		if($level>0)
			$strtmp .= "<th scope=\"col\">Problems</th>";
	} else if($detail) {
		for($i=0;$i<count($pr);$i++)
			$strtmp .= "<th scope=\"col\" nowrap><b>" . $pr[$i]["problem"] . " &nbsp;</b></th>";
	}
	$strtmp .= "<th scope=\"col\">Total</th>\n";
	$strtmp .= "</tr>\n</thead>\n";
	$n=0;
	reset($score);
	$strtmp.="<tbody>\n";
	while(list($e, $c) = each($score)) {
	    if(!isset($score[$e]['classingroup'])) continue;
	    reset($score[$e]['classingroup']);

 	    while(list($cg1,$cg2) = each($score[$e]['classingroup'])) {
  	        $strtmp .= " <tr class=\"";
		    $strtmp .= "sitegroup" . $cg1 . "\">";
			//para lugares

			if ($cg2==1) {
				//$strtmp .= "<td>".$cg2."st <img alt=\"1\" width=\"18\" ".
				//	"src=\"../images/oro.png\" /></td>";
				$strtmp .= "<td>" . $cg2 . "st</td>\n";
			}else if($cg2==2){
				$strtmp .= "<td>" . $cg2 . "nd</td>\n";
			}else if($cg2==3){
				$strtmp .= "<td>" . $cg2 . "rd</td>\n";
			}else{
				$strtmp .= "<td>" . $cg2 . "</td>\n";
			}

			$_SESSION["scoreblink"][$score[$e]["username"]]=0;
			if( $score[$e]["userflag"] != '')
			  $strtmp .= "  <td nowrap><img alt=\"" .  $score[$e]["userflag"]. "\" width=\"18\" src=\"" . $loc. '/images/flags/' .
			    $score[$e]["userflag"] . ".png\"> " . $score[$e]["username"]. " </td>";
			else
			  $strtmp .= "  <td nowrap>" . $score[$e]["username"]."</td>";
			  //$strtmp .= "  <td nowrap>" . $score[$e]["username"]."/".$score[$e]["usersitename"] . " </td>";

			if($score[$e]['usershortinstitution'] != '')
			  $strtmp .= "<td>[" . $score[$e]['usershortinstitution'] . '] ' . $score[$e]["userfullname"];
			else
			  $strtmp .= "<td>" . $score[$e]["userfullname"];
//		}
		$_SESSION["scorepos"][$score[$e]["username"]] = $cg2;

//    $strtmp .= "(" . $score[$e]["site"] . ")";
//    $strtmp .= "</td>\n";
//    if(!$detail && $score[$e]["userdesc"]!="")
//        $strtmp .= "(" . $score[$e]["userdesc"] . ")";
		$strtmp .= "</td>";
		if($level > 0) {
			if(!$des) $strtmp .= "<td>";
			for($h=0;$h<count($pr);$h++) {
				$ee = $pr[$h]["number"];
				if($detail) {
					if($des) {
						//$strtmp .= "<td nowrap>"; antes con globos
//					$name=$score[$e]["problem"][$ee]["name"];
						if(isset($score[$e]["problem"][$ee]["solved"]) && $score[$e]["problem"][$ee]["solved"]) {
							$strtmp .= "<td nowrap bgcolor=\"#C8F3C9\" style=\"color:#000000;\">";
							$strtmp .= "<img alt=\"".$score[$e]["problem"][$ee]["colorname"].":\" width=\"18\" ".
								"src=\"" . balloonurl($score[$e]["problem"][$ee]["color"]) ."\" />";
						}
						else {
							if (isset($score[$e]["problem"][$ee]['count']) && $score[$e]["problem"][$ee]["count"]!=0) {
								$strtmp .= "<td nowrap bgcolor=\"#EAA3A3\">";//#D4F2BB
							} else{
								$strtmp .= "<td nowrap>";
							}


							if($level>3 && isset($score[$e]["problem"][$ee]["judging"]) && $score[$e]["problem"][$ee]["judging"])
								$strtmp .= "<img alt=\"\" width=\"18\" ".
									"src=\"$loc/images/bigballoontransp-blink.gif\" />\n";
							else
								$strtmp .= "&nbsp;";
						}
					}
					if ($ver && $level<3) {
						if(isset($score[$e]["problem"][$ee]["solved"]) && $score[$e]["problem"][$ee]["solved"]) {
							if ($level==1) {
								$strtmp .= "/". $score[$e]["problem"][$ee]["time"] . "\n";
							}
							else
								$strtmp .= $score[$e]["problem"][$ee]["count"] . "/" .
									$score[$e]["problem"][$ee]["time"] . "\n";
						} else if($des) $strtmp .= "&nbsp;";
					}
					else {
						if (isset($score[$e]["problem"][$ee]['count']) && $score[$e]["problem"][$ee]["count"]!=0) {
							$tn = $score[$e]["problem"][$ee]["count"];
							if (isset($score[$e]["problem"][$ee]["solved"]) && $score[$e]["problem"][$ee]["solved"]) $t = $score[$e]["problem"][$ee]["time"];
							else $t = "-";
							$strtmp .= "<font size=\"-2\">" . $tn . "/${t}" . "</font>\n";
						} else if($des) $strtmp .= "&nbsp;";
					}
					if($des)
						$strtmp .= "</td>";

				}
			}
			if(!$des) $strtmp .= "&nbsp;</td>\n";
		}

		$strtmp .= "  <td nowrap>" .
			$score[$e]["totalcount"] . " (" . $score[$e]["totaltime"] . "min)</td>\n";
		$strtmp .= " </tr>\n";
		$n++;
	  }
	}
	$strtmp .= "</tbody>\n</table>";
	if ($n == 0) $strtmp .= "<br><center><b><font color=\"#ff0000\">EL SCOREBOARD ESTA VACIO</font></b></center>";
	else {
		if(!$des)
			if($level>0) $strtmp .= "<br><font color=\"#ff0000\">P.S. Problem names are hidden.</font>";
			else  $strtmp .= "<br><font color=\"#ff0000\">P.S. Problem data are hidden.</font>";
	}

	$conf=globalconf();
	if(isset($conf['doenc']) && $conf['doenc'])
	  $strtmp = "<!-- " . time() . " --> <?php exit; ?>\n" . encryptData($strtmp,$conf["key"],false);
	else $strtmp = "<!-- " . time() . " --> <?php exit; ?>\n" . $strtmp;
	$randnum = session_id() . "_" . rand();
	if(file_put_contents($scoretmp . "_" . $randnum, $strtmp,LOCK_EX)===FALSE) {
		if($_SESSION["usertable"]["usertype"] == 'admin') {
			MSGError("Cannot write to the score cache file -- performance might be compromised");
		}
		LOGError("Cannot write to the ".$_SESSION["usertable"]["usertype"]."-score cache file -- performance might be compromised");
	} else {
	  @rename($scoretmp . "_" . $randnum, $scoretmp);
	}
	$conf=globalconf();
	if(isset($conf['doenc']) && $conf['doenc'])
	  $strtmp = decryptData(substr($strtmp,strpos($strtmp,"\n")),$conf["key"]);
	else $strtmp = substr($strtmp,strpos($strtmp,"\n"));
}
echo $strtmp;

?>
