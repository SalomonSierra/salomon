<?php
session_start();//para iniciar session_sta
require_once("../globals.php");
require_once("../db.php");
if(isset($_POST["problemnumber"]) && is_numeric($_POST["problemnumber"])&& isset($_POST["language"]) && is_numeric($_POST["language"])){
    $prob=$_POST["problemnumber"];
    $lang=$_POST["language"];
    if((isset($_FILES["sourcefile"]) && $_FILES["sourcefile"]["name"]!="") || (isset($_POST["textsource"]) && $_POST["textsource"]!='')){
        if(($ct = DBContestInfo($_SESSION["usertable"]["contestnumber"])) == null) {
             echo "no esta logueado o no hay competencias iniciales";

         }else{
             $error=false;
             $msg='';
             if(!isset($_FILES["sourcefile"]) || $_FILES["sourcefile"]["name"]==''){
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
                      $msg="File size exceeds the limit allowed.";//El tamaño del archivo supera el límite permitido.
                      $error=true;
                  }else if (strlen($name)>100) {
                      IntrusionNotify("file upload problem.");//problema al cargar
                      ForceLoad("../index.php");//index.php
                      $msg="file upload problem.";
                      $error=true;
                  }
             }else{
                 $temp=myhtmlspecialchars($_FILES["sourcefile"]["tmp_name"]);
                 @file_put_contents($temp, $_POST["textsource"], LOCK_EX);
                 $type=myhtmlspecialchars($_FILES["sourcefile"]["type"]);
                 $size=myhtmlspecialchars($_FILES["sourcefile"]["size"]);
                 $name=myhtmlspecialchars($_FILES["sourcefile"]["name"]);
                 if ($size > $ct["contestmaxfilesize"]) {

                      LOGLevel("User {$_SESSION["usertable"]["username"]} tried to submit file " .
                            "$name with $size bytes ({$ct["contestmaxfilesize"]} max allowed).", 1);
                      $msg="File size exceeds the limit allowed.";//El tamaño del archivo supera el límite permitido.
                      $error=true;
                  }else if (!is_uploaded_file($temp) || strlen($name)>100) {
                          IntrusionNotify("file upload problem.");//problema al cargar
                          $msg="File upload problem.";//El tamaño del archivo supera el límite permitido.
                          $error=true;
                  }
             }
             if($error){
                 echo $msg;
             }else if(strpos($name,' ') === true || strpos($temp,' ') === true || strpos($name,'/') === true || strpos($temp,'/') === true ||
                   strpos($name,'`') === true || strpos($temp,'`') === true || strpos($name,'\'') === true || strpos($temp,'\'') === true ||
                   strpos($name, "\"") === true || strpos($temp, "\"") === true || strpos($name,'$') === true || strpos($temp,'$') === true) {

                     //El nombre del archivo no puede contener espacios.

                     echo "El nombre del archivo no puede contener espacios.";
              }else{
                   $shaf = @sha1_file($temp);
                   if(@rename($temp, $temp . "." . sanitizeFilename($shaf)))
                         $temp = $temp . "." . sanitizeFilename($shaf);//archivotmp.asldkfjs

                   $param = array('contest'=>$_SESSION["usertable"]["contestnumber"],
             		       'user'=>  $_SESSION["usertable"]["usernumber"],
                           'problem'=>$prob,
                       	   'lang'=>$lang,
                       	   'filename'=>$name,
                       	   'filepath'=>$temp);
                    ////elimina los caracteres que no son ASSCI y replaza caracteres como *"$&().. con _ y otro ' " lo remplaza con barra invertida
                    //web_169.168.0.1_contest_site_user
                    $compv = "web_" . sanitizeFilename(getIP()) . "_" . $_SESSION["usertable"]["contestnumber"].'_'.$_SESSION["usertable"]["usernumber"];

                    $retv = DBNewRun0 ($param);
                    echo "Yes";

               }

         }



    }else{
        echo "No";
    }

}else{
    echo "No";
}

?>
