<?php
require('header2.php');
$ds = DIRECTORY_SEPARATOR;
if($ds=="") $ds = "/";

if (isset($_POST["problem"]) && isset($_POST["language"]) &&
    ((isset($_FILES["sourcefile"]) && isset($_POST["Submit"]) && $_FILES["sourcefile"]["name"]!="") || (isset($_POST["textsource"]) && isset($_POST["Submit"]) && $_POST["textsource"]!="") || (isset($_POST["data"]) && isset($_POST["name"])))) {

  if ((isset($_POST["confirmation"]) && $_POST["confirmation"] == "confirm") || (isset($_POST["data"]) && isset($_POST["name"]))) {
      //info de la competencia dada
      if(($ct = DBContestInfo($_SESSION["usertable"]["contestnumber"])) == null) {
          if(isset($_POST['name']) && $_POST['name'] != '') {
              //RESULTADO: CONCURSO NO ENCONTRADO
	           echo "\nRESULTADO: CONCURSO NO ENCONTRADO";
	           exit;
           }
           ForceLoad("../index.php");//index.php
       }
       $prob = myhtmlspecialchars($_POST["problem"]);
       $lang = myhtmlspecialchars($_POST["language"]);

        //si no es numerico entra
       if(!is_numeric($prob)) {
           // recibe el número del concurso
           // devuelve una matriz, donde cada línea tiene el número de atributos (número de problema), problema (nombre del problema),
           // descfilename (nombre de archivo con descripción del problema) y descoid (objeto grande con descripción)
           $probs = DBGetProblems($_SESSION["usertable"]["contestnumber"],$_SESSION["usertable"]["usertype"]=='judge');
           $i = 0;
           $ss = "";
           for (;$i<count($probs);$i++) {

	           if($probs[$i]["problem"]==$prob) {

	               $prob = $probs[$i]["number"];
	               break;
	            }
	            $ss .= $probs[$i]["problem"] . " ";
            }
            if($i >= count($probs)) {
                //RESULTADO: PROBLEMA NO VÁLIDO (las opciones son: ". $ Ss.")
	             echo "\nRESULTADO: PROBLEMA NO VÁLIDO (las opciones son " . $ss . ")";
	             exit;
            }
        }
        //language no es numerico entoces entra
        if(!is_numeric($lang)) {
            // recibe el número del concurso
            // devuelve una matriz, donde cada línea tiene el número de atributos (número de idioma)
            //y el nombre (nombre del idioma)
            $langs = DBGetLanguages();
            $i = 0;
            $ss = "";
            for (;$i<count($langs);$i++) {

	            if($langs[$i]["name"]==$lang) {
	                $lang = $langs[$i]["number"];
	                break;
	            }
	            $ss .= $langs[$i]["name"] . " ";
             }
             if($i >= count($langs)) {
	             echo "\nRESULTADO: LENGUAJE NO VALIDO (las opciones son: " . $ss . ")";
	             exit;
             }
         }
         //capturamos de un archivo config 0
         $linesubmission = @file_get_contents($_SESSION["locr"] . $ds . "private" . $ds . 'run-using-command.config');
         if(trim($linesubmission) =='1') {
              if(!isset($_POST['name']) || $_POST['name'] == '') {
	              echo "\nREQUISITOS INVALIDOS";//requisitos invalidos
	              ForceLoad("../index.php");//index.php
	              exit;
               }

          }
          //yes creamos el directirio y archivo y escribimos el data
          if(isset($_POST['name']) && $_POST['name'] != '') {
               $runsfiles = $_SESSION["locr"] . $ds . "private" . $ds . 'runsfiles';
               @mkdir($runsfiles,0770);
               //crea un archivo temporal en el directorio establecido su prefijo
               $temp = tempnam($runsfiles,"bkp-");
               //Apertura para lectura y escritura; coloca el puntero al fichero al principio del
               // fichero y trunca el fichero a longitud cero. Si el fichero no existe se intenta crear.
               $fout = fopen($temp,"wb");
               //va escribir
               fwrite($fout,base64_decode($_POST['data']));
               fclose($fout);
               //Devuelve el tamaño del fichero en bytes, o false
               $size=filesize($temp);
               $name=$_POST['name'];
               if ($size > $ct["contestmaxfilesize"] || strlen($name)>100 || strlen($name)<1) {
	                echo "\nRESULTADO: ARCHIVO ENVIADO (O NOMBRE) DEMASIADO GRANDE";
                    //RESULTADO: ARCHIVO ENVIADO (O NOMBRE) DEMASIADO GRANDE
	                exit;
                }
           } else {
                //para textsource
                if(!isset($_FILES["sourcefile"]) || $_FILES["sourcefile"]["name"]==''){
                    //application/octet-stream 40 print.py2 /tmp/phpiRK8fB
                    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $cad=substr(str_shuffle($permitted_chars), 0, 6);
                    $temp="/tmp/php".$cad;
                    @file_put_contents($temp, $_POST["textsource"], FILE_APPEND | LOCK_EX);
                    $type='application/octet-stream';
                    $size=strlen($_POST["textsource"]);
                    $pdata=DBGetProblemData($prob);
                    $ldata=DBGetLanguage($lang);
                    $name=$pdata[0]["basefilename"].".".$ldata["extension"];
                    if ($size > $ct["contestmaxfilesize"]) {

    	                 LOGLevel("User {$_SESSION["usertable"]["username"]} tried to submit file " .
    		                   "$name with $size bytes ({$ct["contestmaxfilesize"]} max allowed).", 1);
    	                 MSGError("File size exceeds the limit allowed.");//El tamaño del archivo supera el límite permitido.
    	                 ForceLoad($runteam);
                     }
                     if (strlen($name)>100) {
    	                 IntrusionNotify("file upload problem.");//problema al cargar
    	                 ForceLoad("../index.php");//index.php
                     }

                }else{
                    $temp=myhtmlspecialchars($_FILES["sourcefile"]["tmp_name"]);

                    @file_put_contents($temp, $_POST["textsource"], LOCK_EX);
                    $type=myhtmlspecialchars($_FILES["sourcefile"]["type"]);
                    $size=myhtmlspecialchars($_FILES["sourcefile"]["size"]);
                    $name=myhtmlspecialchars($_FILES["sourcefile"]["name"]);

                    //MSGError($type." ".$size." ".$name." ".$temp);///tmp/phpwSC8Z5

                    //application/octet-stream 40 print.py2 /tmp/phpiRK8fB
                    if ($size > $ct["contestmaxfilesize"]) {

    	                 LOGLevel("User {$_SESSION["usertable"]["username"]} tried to submit file " .
    		                   "$name with $size bytes ({$ct["contestmaxfilesize"]} max allowed).", 1);
    	                 MSGError("File size exceeds the limit allowed.");//El tamaño del archivo supera el límite permitido.
    	                 ForceLoad($runteam);
                     }
                     if (!is_uploaded_file($temp) || strlen($name)>100) {
    	                 IntrusionNotify("file upload problem.");//problema al cargar
    	                 ForceLoad("../index.php");//index.php
                     }
                }

           }
           //como una validacion
           if(strpos($name,' ') === true || strpos($temp,' ') === true || strpos($name,'/') === true || strpos($temp,'/') === true ||
                strpos($name,'`') === true || strpos($temp,'`') === true || strpos($name,'\'') === true || strpos($temp,'\'') === true ||
                strpos($name, "\"") === true || strpos($temp, "\"") === true || strpos($name,'$') === true || strpos($temp,'$') === true) {
                 if(isset($_POST['name']) && $_POST['name'] != '') {
                     //RESULTADO: PROBLEMA DE NOMBRE DE ARCHIVO (POR EJEMPLO, NO PUEDE TENER ESPACIOS)
	                   echo "\nRESULTADO: PROBLEMA DE NOMBRE DE ARCHIVO (NO PUEDE TENER ESPACIOS)";
	                   exit;
                  }
                  //El nombre del archivo no puede contener espacios.
                  MSGError("El nombre del archivo no puede contener espacios.");
                  ForceLoad($runteam);//run.php
            }
            //no existe a menos que envio de otro archivo
            if(isset($_POST['pastcode']) && $_POST['pastcode'] != '')
                  $shaf = myhtmlspecialchars($_POST["pastcode"]);
            else $shaf = @sha1_file($temp);//del archivo cargado o subido todo archivo a sha1
            //Calcula el hash sha1 del archivo especificado mediante filename utilizando
            //el » algoritmo de hash seguro 1 de US y devuelve ese hash. El hash es un número hexadecimal de 40 caracteres.
            //
            if(@rename($temp, $temp . "." . sanitizeFilename($shaf)))
                  $temp = $temp . "." . sanitizeFilename($shaf);//archivotmp.asldkfjs

    //		$ac=array('contest','site','user','problem','lang','filename','filepath');
    //		$ac1=array('runnumber','rundate','rundatediff','rundatediffans','runanswer','runstatus','runjudge','runjudgesite',
    //			   'runjudge1','runjudgesite1','runanswer1','runjudge2','runjudgesite2','runanswer2',
    //			   'autoip','autobegindate','autoenddate','autoanswer','autostdout','autostderr','updatetime');

            $param = array('contest'=>$_SESSION["usertable"]["contestnumber"],
		           'user'=>  $_SESSION["usertable"]["usernumber"],
		           'problem'=>$prob,
        		   'lang'=>$lang,
        		   'filename'=>$name,
        		   'filepath'=>$temp);
            ////elimina los caracteres que no son ASSCI y replaza caracteres como *"$&().. con _ y otro ' " lo remplaza con barra invertida
            //web_169.168.0.1_contest_site_user
            $compv = "web_" . sanitizeFilename(getIP()) . "_" . $_SESSION["usertable"]["contestnumber"].'_'.$_SESSION["usertable"]["usernumber"];
            if(trim($linesubmission) =='1') {
                   if(isset($_POST['comp']) && $_POST['comp'] != '') $compv=substr(trim(sanitizeFilename($_POST['comp'])),0,150);
                   else {
	                    echo "\nRESULT: ERROR COMPUTER KEY";//RESULTADO: ERROR TECLA DE COMPUTADORA
	                    exit;
                   }
            }
            $name = str_replace("-", "_", $name);//remplazamos todos los guiones a _ en name del archivo subido
            //web_169.168.0.1_1_1_4-asldfkjlsakdfjsha1-rn.cpp-nprob-nlang-contest-site-user
            $verify = $compv . '-'. $shaf . '-' . $name . '-'. $prob . '-' . $lang . '-' .
                  $_SESSION["usertable"]["contestnumber"].'-'.$_SESSION["usertable"]["usernumber"];

            $pasthash=""; if(isset($_POST["pasthash"])) $pasthash=myhtmlspecialchars($_POST["pasthash"]);
            $pastvalhash=''; if(isset($_POST["pastvalhash"])) $pastvalhash=myhtmlspecialchars($_POST["pastvalhash"]);
            $pastval=''; if(isset($_POST["pastval"])) $pastval=myhtmlspecialchars($_POST["pastval"]);
            $pastabs=''; if(isset($_POST["pastabs"])) $pastabs=myhtmlspecialchars($_POST["pastabs"]);
            //pasthash-pastvalhash-pastval-pastabs-web_169.168.0.1_contest_site_user-sadlflasfjsha1-namearchivo-nprobl-nlang-contest-site-user
            $verify1 = $pasthash . '-' . $pastvalhash .'-'. $pastval .'-'. $pastabs .'-'. $compv . '-'. $shaf . '-' . $name . '-'. $prob . '-' . $lang . '-' .
                  $_SESSION["usertable"]["contestnumber"].'-'.$_SESSION["usertable"]["usernumber"];

            @mkdir($_SESSION["locr"] . $ds . "private" . $ds . 'runslog',0770);//un directorio con estos permisos
            //private/runslog/runs-submitted-contest-site-user
            $fcname = $_SESSION["locr"] . $ds . "private" . $ds . 'runslog' . $ds . 'runs-submitted-' . $_SESSION["usertable"]["contestnumber"].'-'.
                  $_SESSION["usertable"]["usernumber"];
            @mkdir($_SESSION["locr"] . $ds . "private" . $ds . 'comp',0770);
            //private/comp/web_169.168.0.1_contest_site_user.comp
            $fcnamex = $_SESSION["locr"] . $ds . "private" . $ds . 'comp' . $ds . $compv . ".comp";
            //si falla false contenido de ruta especificado
            $prevcomp = @file_get_contents($fcnamex);

            if($prevcomp === false || trim($prevcomp) == '') {
                //contest-site-user
                   @file_put_contents($fcnamex, $_SESSION["usertable"]["contestnumber"].'-'.$_SESSION["usertable"]["usernumber"], LOCK_EX);
            } else {
                   if(trim($prevcomp) != $_SESSION["usertable"]["contestnumber"].'-'.$_SESSION["usertable"]["usernumber"]) {
	                    @file_put_contents($fcname . ".try", $verify1 . "-UNAUTH\n", FILE_APPEND | LOCK_EX);
	                    if(isset($_POST['name']) && $_POST['name'] != '') {
            	            echo "\nRESULT: UNAUTHORIZED COMPUTER";//ORDENADOR NO AUTORIZADO
            	            exit;
            	         }
            	         MSGError("Could not save computer info");//No se pudo guardar la información de la computadora
                    }
            }

            //@file_put_contents($fcname . ".try", $verify1 . "\n", FILE_APPEND | LOCK_EX);
            //file — Transfiere un fichero completo a un array /private/runslog/runs-submitted-contest-site-user.txt
            $codes = @file($fcname . ".txt",FILE_IGNORE_NEW_LINES);
            //in_array(,)verifiy existe en array codes?
            if(is_array($codes) && in_array($verify,$codes)) {
                   @file_put_contents($fcname . ".try", $verify1 . "-ALREADY\n", FILE_APPEND | LOCK_EX);
                   if(isset($_POST['name']) && $_POST['name'] != '') {
            	       echo "\nRESULT: SAME FILE ALREADY SUBMITTED FOR THIS PROB/LANG";//EL MISMO ARCHIVO YA ENVIADO PARA ESTE PROB / LANG
            	       exit;
                    }
                    //Ya se envió el mismo archivo para este problema e idioma
                    MSGError("Ya se envió el mismo archivo para este problema e idioma"); ForceLoad($runteam);
            }
            //si existe pastcode
            if(isset($_POST['pastcode']) && $_POST['pastcode'] != '') {

                    $pastcode = myhtmlspecialchars($_POST["pastcode"]);
                    if(isset($_POST["pasthash"]) && isset($_POST["pastval"])) {

            	        $pasthash = myhtmlspecialchars($_POST["pasthash"]);
            	        $pastvalhash = myhtmlspecialchars($_POST["pastvalhash"]);
            	        $pastval = myhtmlspecialchars($_POST["pastval"]);
            	        $pastabs = myhtmlspecialchars($_POST["pastabs"]);
            	        if(is_readable($_SESSION["locr"] . $ds . "private" . $ds . 'run-past.config')) {
            	             $pcodes = @file($_SESSION["locr"] . $ds . "private" . $ds . 'run-past.config');
                             //Devuelve un array que contiene todos los elementos de array1 después de aplicar la función callback a cada uno de ellos.
                             $pastsubmission = array_map(function($element){ $sp=explode(' ',$element,4); return trim($sp[2]); }, $pcodes);
            	             $key=-1;

            	             for($hh=0; $hh < count($pastsubmission); $hh++)
            	                if(myhash($pastsubmission[$hh] . $pastcode . $compv . $pastval) == $pastvalhash) { $key = $hh; break; }
            	             if($key < 0) {
                        	    //if(($key=array_search($pastvalhash, $pastsubmission))===false) {
                        	    //	$pastsubmission = array_map(function($element){ $sp=explode(' ',$element,4); return myhash(trim($sp[2]) . trim($pastcode) . trim($pastabs)); }, $pcodes);
                        	    //	if(($key=array_search($pasthash, $pastsubmission))===false) {
                        	    echo "\nRESULT: INVALID SUBMISSION CODE (0)";//RESULTADO: CÓDIGO DE ENVÍO NO VÁLIDO (0)
                        	    exit;
                        	    //	}
                        	  }
            	          } else {
            	               @file_put_contents($fcname . ".try", $verify1 . "-BADCODE1\n", FILE_APPEND | LOCK_EX);
            	               echo "\nRESULT: INVALID SUBMISSION CODE (1)";//RESULTADO: CÓDIGO DE ENVÍO NO VÁLIDO (1)
            	               exit;
            	          }
                      } else {

            	           @file_put_contents($fcname . ".try", $verify1 . "-BADCODE2\n", FILE_APPEND | LOCK_EX);
            	           echo "\nRESULT: INVALID SUBMISSION CODE (2)";//CÓDIGO DE ENVÍO NO VÁLIDO (2)
            	           exit;
                      }
                      // cassio: being restrict with respect to internet connection in the client
                      //estar restringido con respecto a la conexión a Internet en el cliente
                      $pok = 'OK';
                      if(!isset($_POST['oknet']) || !is_numeric($_POST['oknet']) || $_POST['oknet']>0) {
            	          $pok = 'OKNET';
                           if(true) {
                        	  @file_put_contents($fcname . ".try", $verify1 . "-NET" . $_POST['oknet'] . "\n", FILE_APPEND | LOCK_EX);
                        	  echo "\nRESULT: INVALID SUBMISSION CODE (3)";//CÓDIGO DE ENVÍO NO VÁLIDO (3)
                        	  exit;
                           }
                       }
                       if($pastval > 0) {
            	           $param['rundate']=time() - $pastval;
            	           $b = DBContestClockInfo($_SESSION["usertable"]["contestnumber"]);
            	           $dif = $b["currenttime"];
            	           $param['rundatediff']=$dif - $pastval;
                        }

                        $tardes = array_map(function($element){ $sp=explode(' ',$element,4); if(count($sp)>3) return 60*trim($sp[3]); return 0; }, $pcodes);
                        ///////CASO DE COMECAR MAIS TARDE NO CENTRALIZADO(CASO DE EMPEZAR MÁS TARDE EN EL CENTRALIZADO)
                        if($key >= 0 && $tardes[$key] > 0) { //substr($_SESSION["usertable"]["username"],0,3) == 'XXX') {
            	             $param['rundate']=$param['rundate'] - $tardes[$key]; // 60*10 = 10 minutos
            	             $param['rundatediff']=$param['rundatediff'] - $tardes[$key];
                         }
                         //DBNewRun //funcion para insertar o actulizar un registro en la tabla runtable con los parametros enviados
                         $retv = DBNewRun ($param);
                         if($retv == 2) {
            	             if(isset($_POST['oknet']) && is_numeric($_POST['oknet']) && $_POST['oknet']>0) $pok .= $_POST['oknet'];
            	             @file_put_contents($fcname . ".try", $verify1 . "-" . $pok . "-" . $param['rundatediff'] . "-" . $param['rundate'] . "-" . $b["currenttime"] . "\n", FILE_APPEND | LOCK_EX);
            	             @file_put_contents($fcname . ".txt", $verify . "\n", FILE_APPEND | LOCK_EX);
            	             echo "\nRESULT: RUN SUBMITTED SUCCESSFULLY ($pastval)";//EJECUTAR ENVIADO CON ÉXITO
                         } else {
                        	 if($retv == 0) {
                        	      echo "\nRESULT: CONTEST NOT RUNNING";//EL CONCURSO NO SE EJECUTA
                        	      @file_put_contents($fcname . ".try", $verify1 . "-NOTRUNNING\n", FILE_APPEND | LOCK_EX);
                        	 } else {
                	              echo "\nRESULT: UNKNOWN PROBLEM";//PROBLEMA DESCONOCIDO
                	              @file_put_contents($fcname . ".try", $verify1 . "-UNKNOWN\n", FILE_APPEND | LOCK_EX);
                	         }
                         }
                         exit;
                 }
                 if(trim($linesubmission) =='1') {
                      @file_put_contents($fcname . ".try", $verify1 . "-BADCALL\n", FILE_APPEND | LOCK_EX);
                      echo "\nINVALID REQUEST";//requisitos invalidos
                      exit;
                  }


                  ///CASO DE EMPEZAR MÁS TARDE EN EL CENTRALIZADO
                  if(substr($_SESSION["usertable"]["username"],0,3) == 'XXX') {
                      $param['rundate']=$param['rundate'] - 60*10; // 10 minutos
                      $param['rundatediff']=$param['rundatediff'] - 60*10;
                  }
                  $retv = DBNewRun ($param);
                  //fcname//private/runslog/runs-submitted-contest-site-user
                  //escribe web_169.168.0.1_1_1_4-asldfkjlsakdfjsha1-rn.cpp-nprob-nlang-contest-site-user
                  if($retv==2)
                      @file_put_contents($fcname . ".txt", $verify . "\n", FILE_APPEND | LOCK_EX);
                  if(isset($_POST['name']) && $_POST['name'] != '') {
                      if($retv == 2) {
                          @file_put_contents($fcname . ".try", $verify1 . "-OK\n", FILE_APPEND | LOCK_EX);
                          echo "\nRESULT: RUN SUBMITTED SUCCESSFULLY";
                       }
                      else {
                    	  if($retv == 0) {
                    	       @file_put_contents($fcname . ".try", $verify1 . "-NOTRUNNING2\n", FILE_APPEND | LOCK_EX);
                    	       echo "\nRESULT: CONTEST NOT RUNNING";
                    	   }
                    	   else {
                    	       @file_put_contents($fcname . ".try", $verify1 . "-UNKNOWN\n", FILE_APPEND | LOCK_EX);
                    	       echo "\nRESULT: UNKNOWN PROBLEM";
                    	   }
                       }
                       exit;
                   }
                   $_SESSION['forceredo']=true;
    }
    //run.php
    ForceLoad($runteam);
}
//
if(isset($_POST['name']) && $_POST['name'] != '') {
  echo "RESULT: PARAMETERS MISSING";//FALTAN PARAMETROS
  exit;
}
//private/runtmp/run-contest1-site-user.php
$runtmp = $_SESSION["locr"] . $ds . "private" . $ds . "runtmp" . $ds . "run-contest" . $_SESSION["usertable"]["contestnumber"] .
  "-user" . $_SESSION["usertable"]["usernumber"] . ".php";
$redo = TRUE;
if(!isset($_SESSION['forceredo']) || $_SESSION['forceredo']==false) {

    $actualdelay = 15;
    if(file_exists($runtmp)) {

        if(isset($strtmp) || (($strtmp = file_get_contents($runtmp,FALSE,NULL,0,1000000)) !== FALSE)) {

            list($d) = sscanf($strtmp,"%*s %d");
            if($d > time() - $actualdelay) {

	            $conf=globalconf();
	            if(isset($conf['doenc']) && $conf['doenc'])
	               $strtmp = decryptData(substr($strtmp,strpos($strtmp,"\n")+1),$conf["key"],'runtmp');
	            else $strtmp = substr($strtmp,strpos($strtmp,"\n")+1);
	            if($strtmp !== false)
	               $redo = FALSE;
            }
        }
    }
}
if($redo) {
    //tiempo emergente//forzar reacer=false
    $_SESSION["popuptime"] = time();
    $_SESSION['forceredo']=false;
    if(($st = DBContestClockInfo($_SESSION["usertable"]["contestnumber"])) == null)
        ForceLoad("../index.php");//index.php
    $strtmp="<br>\n<div class=\"\">\n<table class=\"table table-hover\">\n <thead>\n <tr>\n  <th scope=\"col\">Run #</th>\n<th scope=\"col\">Time</th>\n".
        "  <th scope=\"col\">Problem</th>\n  <th scope=\"col\">Language</th>\n  <th scope=\"col\">Answer</th>\n  <th scope=\"col\">File</th>\n </tr>\n </thead>\n<tbody>\n";
    $strcolors = "0";
    // recibe el número del concurso, el número del sitio y el número del usuario
    // devuelve una matriz, donde cada línea tiene los atributos
    // número (número de ejecución)
    // marca de tiempo (tiempo de creación de ejecución)
    // problema (nombre del problema)
    // estado (situación de ejecución)
    // respuesta (texto con la respuesta)
    // lenguaje y extension
    $run = DBUserRuns($_SESSION["usertable"]["contestnumber"],
		     $_SESSION["usertable"]["usernumber"]);
    //sacamos informacion de la competencia de la base de datos
    $ct=DBContestInfo($_SESSION["usertable"]["contestnumber"]);

    for ($i=0; $i<count($run); $i++) {

        $strtmp .= " <tr>\n";
        $strtmp .= "  <td>" . $run[$i]["number"] . "</td>\n";
        //
        $strtmp .= "  <td>" . dateconvminutes($run[$i]["timestamp"]) . "</td>\n";
        $strtmp .= "  <td>" . $run[$i]["problem"] . "</td>\n";
        $strtmp .= "  <td>" . $run[$i]["language"] . "</td>\n";
        //  $strtmp .= "  <td nowrap>" . $run[$i]["status"] . "</td>\n";
        if (trim($run[$i]["answer"]) == "") {

            $run[$i]["answer"] = "Not answered yet";
            $strtmp .= "  <td class=\"text-primary\">Not answered yet";
        }
        else {

            if(strpos($run[$i]["answer"],"YES")===false){
                if(strpos($run[$i]["answer"],"Not")===false){

                    //para ver el resultado
                    if($ct["contestactiveresult"] == 't'){
                        $strtmp .= "  <td class=\"text-danger\">" . "<a class=\"text-danger\" href=\"\" onClick=\"window.open('../filewindow.php?".
                  	     filedownload($run[$i]["stdoutuser"],"user") ."', 'View - OUTUSER','width=680,height=600,scrollbars=yes,".
                  	   "resizable=yes')\">".$run[$i]["answer"]."</a>";
                    }else{
                        $strtmp .= "  <td class=\"text-danger\">" . $run[$i]["answer"];
                    }

                }else{
                    $strtmp .= "  <td class=\"text-primary\">" . $run[$i]["answer"];
                }

            }else{
                $strtmp .= "  <td class=\"text-success\">" . $run[$i]["answer"];
            }
            if(false) {

	            if(strpos($run[$i]["autoanswer"],"OKs") > 0)
	                $strtmp .= ' ' . substr($run[$i]["autoanswer"],strrpos($run[$i]["autoanswer"],'('));
            }
            if($run[$i]['yes']=='t') {
	            $strtmp .= " <img alt=\"".$run[$i]["colorname"]."\" width=\"15\" ".
	               "src=\"" . balloonurl($run[$i]["color"]) ."\" />";
	            $strcolors .= "\t" . $run[$i]["colorname"] . "\t" . $run[$i]["color"];
            }
        }
        $strtmp .= "</td>\n";
        $strtmp .= "<td><a href=\"../filedownload.php?" . filedownload($run[$i]["oid"],$run[$i]["filename"]) . "\">";
        $strtmp .= $run[$i]["filename"] . "</a>&nbsp;&nbsp;";
        //mejorado

        $strtmp .= "<a href=\"#\" class=\"btn btn-primary\" style=\"font-weight:bold\" onClick=\"window.open('../filewindow1.php?".filedownload($run[$i]["oid"],$run[$i]["filename"])."', 'View - SOURCE', 'width=680,height=600,scrollbars=yes,resizable=yes')\">Ver Codigo</a>\n";


        $strtmp .= "</td>\n";

        $strtmp .= " </tr>\n";
    }
    //fin de la tabla runs
    $strtmp .= "</tbody>\n</table>\n</div>\n";

    if (count($run) == 0) $strtmp .= "<br><center><b><font color=\"#ff0000\">AUN NO ENVIASTE NINGUN PROBLEMA</font></b></center>";
    $linesubmission = @file_get_contents($_SESSION["locr"] . $ds . "private" . $ds . 'run-using-command.config');
    if(trim($linesubmission) == '1') {
        /*Para enviar un programa, use la herramienta de línea de comandos:
             boca-submit-run ARCHIVO DE IDIOMA DEL PROBLEMA DE CONTRASEÑA DE USUARIO
             "donde USUARIO es su nombre de usuario, CONTRASEÑA es su contraseña,
             "PROBLEMA es uno de*/
        $strtmp .= "<br><br><center><b>To submit a program, use the command-line tool:</b>\n<br>".
            "<pre>boca-submit-run USER PASSWORD PROBLEM LANGUAGE FILE</pre><br>".
            "where USER is your username, PASSWORD is your password, <br>".
            "PROBLEM is one of { ";

        $prob = DBGetProblems($_SESSION["usertable"]["contestnumber"],$_SESSION["usertable"]["usertype"]=='judge');
        for ($i=0;$i<count($prob);$i++)
            $strtmp .= $prob[$i]["problem"] . " ";
        $strtmp .= "} and<br>LANGUAGE is one of { ";
        $lang = DBGetLanguages();
        for ($i=0;$i<count($lang);$i++)
            $strtmp .= $lang[$i]["name"] . " ";
        $strtmp .= "}<br>FILE is your submission file<br><br>\n";
    } else {

        //To submit a program, just fill in the following fields:
        $strtmp .= "<br><br><center><b>Para enviar un programa, simplemente complete los siguientes campos:</b></center>\n".
            "<div class=\"container\">\n<form name=\"form1\" enctype=\"multipart/form-data\" method=\"post\" action=\"". $runteam ."\">\n".
            "  <input type=hidden name=\"confirmation\" value=\"noconfirm\" />\n".

            " <div class=\"form-group row\">\n".
            "   <div class=\"col-sm-4\">\n".
            "       <div class=\"form-group row\">\n".
            "           <label class=\"col-sm-4 col-form-label\">Problem:</label>\n".
            "           <div class=\"col-sm-8\">\n".
            "               <select name=\"problem\" onclick=\"Arquivo()\">\n";
            $prob = DBGetProblems($_SESSION["usertable"]["contestnumber"],$_SESSION["usertable"]["usertype"]=='judge');
            $strtmp .= "<option selected value=\"-1\"> -- </option>\n";
            for ($i=0;$i<count($prob);$i++)
            $strtmp .= "<option value=\"" . $prob[$i]["number"] . "\">" . $prob[$i]["problem"] . "</option>\n";
            $strtmp .= "	</select>\n".
            "           </div>\n".
            "       </div>\n".
            "       <div class=\"form-group row\">\n".
            "           <label class=\"col-sm-4 col-form-label\">Language:</label>\n".
            "           <div class=\"col-sm-8\">\n".
            "               <select name=\"language\" onclick=\"Arquivo()\">\n";
            $lang = DBGetLanguages();
            $strtmp .= "<option selected value=\"-1\"> -- </option>\n";
            for ($i=0;$i<count($lang);$i++)
            $strtmp .= "<option value=\"" . $lang[$i]["number"] . "\">" . $lang[$i]["name"] . "</option>\n";
            $strtmp .= "	  </select>\n".
            "           </div>\n".
            "       </div>\n".
            "       <label class=\"col-form-label\">Source code:</label>\n".
            "  	    <input type=\"file\" class=\"form-control\" id=\"sourcefile\" name=\"sourcefile\" size=\"40\" onclick=\"Arquivo()\">\n".
            "   </div>\n".
            "   <div class=\"col-sm-8\">\n".
            "       <textarea class=\"form-control\" id=\"textsource\"  name=\"textsource\" rows=\"10\"></textarea>\n".
            "   </div>\n".
            " </div>\n".
            "  <script language=\"javascript\">\n".
            "    function conf() {\n".
            "      if (document.form1.problem.value != '-1' && document.form1.language.value != '-1') {\n".
            "       if (confirm(\"Confirm submission?\")) {\n".
            "        document.form1.confirmation.value='confirm';\n".
            "       }\n".
            "      } else {\n".
            "        alert('Invalid problem and/or language');\n".
            "      }\n".
            "    }\n".
            "  </script>\n".
            "  <center>\n".
            "      <input type=\"submit\" class=\"btn btn-primary\" name=\"Submit\" value=\"Send\" onClick=\"conf()\">\n".
            "      <input type=\"reset\" class=\"btn btn-primary\" name=\"Submit2\" value=\"Clear\">\n".
            "  </center>\n".
            "</form>\n</div>\n";
        }
        $conf=globalconf();
        if(isset($conf['doenc']) && $conf['doenc'])
            $strtmp1 = "<!-- " . time() . " --> <?php exit; ?>\t" . encryptData($strcolors,$conf["key"],false) . "\n" . encryptData($strtmp,$conf["key"],false);
        else
            $strtmp1 = "<!-- " . time() . " --> <?php exit; ?>\t" . $strcolors . "\n" . $strtmp;
        $randnum = session_id() . "_" . rand();
        if(file_put_contents($runtmp . "_" . $randnum, $strtmp1,LOCK_EX)===FALSE) {////private/runtmp/run-contest1-site-user.php
            if(!isset($_SESSION['writewarn'])) {
                LOGError("Cannot write to the user-run cache file $runtmp -- performance might be compromised");
                $_SESSION['writewarn']=true;
            }
        }
        @rename($runtmp . "_" . $randnum, $runtmp);
}

echo $strtmp;
?>


<!--PIE DE PAGINA......PAGINA........PAGINA-->
	<?php include '../footnote.php'; ?>



	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</body>
</html>
<script language="JavaScript" src="../sha256.js"></script>
<script language="JavaScript" src="../hex.js"></script>
<!--para editor de codigo-->
<script type="text/javascript">
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
        // readOnly: true, // solo lectura

    });
    //editor.setSize("800","350");
</script>
<script>
function leerArchivo(e) {
  var archivo = e.target.files[0];
  if (!archivo) {
    return;
  }
  var lector = new FileReader();
  lector.onload = function(e) {
    var contenido = e.target.result;
    mostrarContenido(contenido);
  };
  lector.readAsText(archivo);
}

function mostrarContenido(contenido) {
  var elemento = document.getElementById('textsource');
  elemento.innerHTML = contenido;
}

document.getElementById('sourcefile').addEventListener('change', leerArchivo, false);


$(document).ready(function(){
    //para enviar el codigo fuente

     //update
     $('#update_button').click(function(){
		 var username,userdesc,userfull,passHASHo,passHASHn;
		 if($('#passwordn1').val() != $('#passwordn2').val()){
			 alert('password confirmacion debe ser igual');
		 }else{
			 if($('#passwordn1').val() == $('#passwordo').val()){
				 alert('password nuevo debe ser diferente al anterior');
			 }else{
				 username = $('#username').val();
				 userdesc = $('#userdesc').val();
				 userfull = $('#userfull').val();
				 passHASHo = js_myhash(js_myhash($('#passwordo').val())+'<?php echo session_id(); ?>');
				 passHASHn = bighexsoma(js_myhash($('#passwordn2').val()),js_myhash($('#passwordo').val()));
				 $('#passwordn1').val('                                                     ');
				 $('#passwordn2').val('                                                     ');
				 $('#passwordo').val('                                                     ');

				 $.ajax({

						  url:"../include/i_optionlower.php",
						  method:"POST",
						  data: {username:username, userdesc:userdesc, userfullname:userfull, passwordo:passHASHo, passwordn:passHASHn},

						  success:function(data)
						  {
							   //alert(data);
							   if(data.indexOf('Data updated.') !== -1)
							   {
									alert("Data updated.");
									$('#updateModal').hide();
									location.reload();
							   }
							   else
							   {
								   if (data.indexOf('Incorrect password')!== -1) {
									   alert("Incorrect password");

									   //location.href="../indexs.php";
								   }else{
									   alert(data);
								   }

							   }

						  }
				 });




			 }
		 }



     });


});
</script>
