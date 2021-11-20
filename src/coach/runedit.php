<?php
require 'header2.php';

if (isset($_POST["cancel"]) && $_POST["cancel"]=="Cancel editing")
        ForceLoad($runphp);//recargar run.php

if (isset($_POST["giveup"]) && $_POST["giveup"]=="Re-juzgar" &&
    isset($_POST["number"]) && is_numeric($_POST["number"])) {

        $number = myhtmlspecialchars($_POST["number"]);
        ////(contest,site,number,ip='',ans='',true)
        //funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
        if (DBGiveUpRunAutojudging($_SESSION["usertable"]["contestnumber"], $number))
            MSGError("Ejecucion re-juzgar.");//Corre renovado.

        ForceLoad($runphp);

}
//eliminar aun falta
if (isset($_POST["delete"]) && $_POST["delete"]=="Eliminar" &&
    isset($_POST["number"]) && is_numeric($_POST["number"])) {
        if ($_POST["confirmation"]=="confirm") {

                $number = myhtmlspecialchars($_POST["number"]);
                //actualizar la tabla runtable a status deleted y guarda una nueva tarea en tasktable
                //obtenido datos de tabla problemtable y answertable del mismo usuario
                //falta
                if (DBRunDelete($number, $_SESSION["usertable"]["contestnumber"],
                             $_SESSION["usertable"]["usernumber"]))
                        MSGError("Run deleted.");
        }
        ForceLoad($runphp);
}

if (isset($_POST["answer"]) && isset($_POST["open"]) && $_POST["open"]=="Abrir ejecucion para re-juzgar" &&
    isset($_POST["number"]) && is_numeric($_POST["number"])) {

	if ($_POST["confirmation"] == "confirm") {

        	$number = myhtmlspecialchars($_POST["number"]);
            ////(contest,site,number,ip='',ans='',true)
            //funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
			DBGiveUpRunAutojudging($_SESSION["usertable"]["contestnumber"], $number);
            // devuelve una ejecución que estaba siendo respondida. Recibirá el número de ejecución,
            // el número de sitio de ejecución y el número de concurso.
            // intenta cambiar el estado a 'openrun'. Si no puede devolver falso
            //jefe ejecutar terminar
			if (DBChiefRunGiveUp($_POST["number"], $_SESSION["usertable"]["contestnumber"]))
				MSGError("Run returned.");//Run regresó.

            ForceLoad($runphp);
	}
}
//para responder runanswer
if (isset($_POST["answer"]) && isset($_POST["Submit"]) && $_POST["Submit"]=="Juzgar" && is_numeric($_POST["answer"]) &&
    isset($_POST["number"]) &&
    is_numeric($_POST["number"])) { // && isset($_POST["notifyuser"]) && isset($_POST["updatescore"])) {

	if ($_POST["confirmation"] == "confirm") {
	        $answer = myhtmlspecialchars($_POST["answer"]);

	        $number = myhtmlspecialchars($_POST["number"]);
//      	  $notuser = myhtmlspecialchars($_POST["notifyuser"]);
//	        $updscore = myhtmlspecialchars($_POST["updatescore"]);
            // responde a una contest. Reciba el número del contest, el sitio del usuario, el usuario, el sitio de ejecución,
            // número de ejecución, número de respuesta (notificar usuario y puntuación de actualización).
            // intenta cambiar el estado a 'juzgado'.
	        DBChiefUpdateRun($_SESSION["usertable"]["contestnumber"],
	                     $_SESSION["usertable"]["usernumber"],
	                     $number, $answer); //, $notuser, updscore);

    }

    ForceLoad($runphp);

}

if (!isset($_GET["runnumber"]) ||
    !is_numeric($_GET["runnumber"])) {
        //Intento abrir admin / runedit.php con parámetros incorrectos.
	//IntrusionNotify("tried to open the admin/runedit.php with wrong parameters.");
	ForceLoad($runphp);//mejorar
}

$runnumber = myhtmlspecialchars($_GET["runnumber"]);
// corre para juzgar. Reciba el número de ejecución, el número de sitio y el número de concurso.
// intenta cambiar el estado a 'juzgar' y, si tiene éxito, devuelve una matriz de datos de ejecución. Si no puedes
//retorna false
//actulizar la tabla runtable status a judging y otros
//Devuelve en la matriz: contestnumber, sitenumber, number, timestamp (em segundos), problemname,
//			problemnumber, language, sourcename, sourceoid, (langscript, infiles, solfiles)
if (($a = DBChiefGetRunToAnswer($runnumber,
		$_SESSION["usertable"]["contestnumber"])) === false) {
	MSGError("Another judge got it first.");//Otro juez lo consiguió primero
	ForceLoad($runphp);
}
//recibe un numero de contest y problema
//devuelve todos los datos relacionados con el problema en cada liena de la matriz y cada linea representa el hecho
//que hay mas de un archivo de entrada/salida
//No devuelve datos sobre problemas falsos, como no deberian haberlo hecho.
$b = DBGetProblemData($a["problemnumber"]);

?>
<!--Use the following fields to judge the run:-->
<br><br><center><b>Utilice los siguientes campos para juzgar la ejecución:
</b></center>
<form name="form1" method="post" action="<?php echo $runeditphp; ?>">
  <input type=hidden name="confirmation" value="noconfirm" />
  <center>
    <table class="table table-bordered">

      <tr>
        <td width="27%" align=right><b>ID:</b></td>
        <td width="83%">
		    <input type=hidden name="number" value="<?php echo $a["number"]; ?>" />
		    <?php echo $a["number"]; ?>
        </td>
      </tr>
      <tr>
        <td width="27%" align=right><b>Tiempo:</b></td>
        <td width="83%">
		   <?php echo dateconvminutes($a["timestamp"]); ?> min.
        </td>
      </tr>
      <tr>
        <td width="27%" align=right><b>Problema</b><i> <?php echo $a["problemname"]; ?></i>: </td>
        <td width="83%">
<?php
for ($i=0;$i<count($b);$i++) {
	echo "<a href=\"../filedownload.php?". filedownload($b[$i]["inputoid"],$b[$i]["inputfilename"]) . "\">";
	echo $b[$i]["inputfilename"] . "</a>";
//	echo " <a href=\"#\" class=menu style=\"font-weight:bold\" onClick=\"window.open('../filewindow.php?".
//	filedownload($b[$i]["inputoid"],$b[$i]["inputfilename"]) ."', 'View$i - INPUT','width=680,height=600,scrollbars=yes,resizable=yes')\">view</a> &nbsp;";
/*
	echo "<b>Sol:</b><a href=\"../filedownload.php?". filedownload($b[$i]["soloid"], $b[$i]["solfilename"]) . "\">";
	echo $b[$i]["solfilename"] . "</a>";
	echo " <a href=\"#\" class=menu style=\"font-weight:bold\" onClick=\"window.open('../filewindow.php?".
             filedownload($b[$i]["soloid"], $b[$i]["solfilename"]) ."', 'View$i - CORRECT OUTPUT','width=680,height=600,scrollbars=yes,resizable=yes')\">view</a>";
*/
}
?>
	&nbsp;</td>
      </tr>
      <tr>
        <td width="27%" align=right><b>Lenguaje</b>:</td>
        <td width="83%">
        <i> <?php echo $a["language"]; ?></i></td>
      </tr>
      <tr>
        <td width="27%" align=right><b>Team's codigo:</b></td>
        <td width="83%">
<?php //'
echo "<a href=\"../filedownload.php?". filedownload($a["sourceoid"],$a["sourcename"]) . "\">" . $a["sourcename"] . "</a>\n";
echo "<a href=\"#\" class=\"btn btn-primary\" onClick=\"window.open('../filewindow1.php?" .
filedownload($a["sourceoid"],$a["sourcename"])  ."', 'View - SOURCE', 'width=680,height=600,scrollbars=yes,resizable=yes')\">Ver</a>\n";
?>
        </td>
      </tr>
      <tr>
        <td width="27%" align=right><b>Respuesta:</b></td>
        <td width="83%">
          <select name="answer">
<?php
// recibe el número del concurso
// devuelve una matriz, donde cada línea tiene los atributos number
//(número de respuesta) y desc (descripción de respuesta o answer) fproblem.php
$ans = DBGetAnswers();
//$isfak = true;
for ($i=0;$i<count($ans);$i++)
	if ($a["answer"] == $ans[$i]["number"]) {
//	  if($ans[$i]["fake"] != "t") $isfak = false;
       	  echo "<option selected value=\"" . $ans[$i]["number"] . "\">" . $ans[$i]["desc"] . "</option>\n";
	} else
	      echo "<option value=\"" . $ans[$i]["number"] . "\">" . $ans[$i]["desc"] . "</option>\n";
	echo "</select>";
//	if(!$isfak) {
    if($a["judge"] != "") {
 	      $uu = DBUserInfo ($a["judge"]);
   	      echo " [judge=" . $uu["username"] . "]";
    }
?>
        </td>
      </tr>
      <!--<tr>
        <td width="27%" align=right><b>Answer 1:</b></td>
        <td width="83%">
<?php
// recibe el número del concurso
// devuelve una matriz, donde cada línea tiene los atributos number
//(número de respuesta) y desc (descripción de respuesta o answer)
/*$ans = DBGetAnswers($_SESSION["usertable"]["contestnumber"]);
for ($i=0;$i<count($ans);$i++)
	if ($a["answer1"] == $ans[$i]["number"]) {
//	  if($ans[$i]["fake"] != "t") {
        if($a["judgesite1"] != "" && $a["judge1"] != "") {
   	        $uu = DBUserInfo ($_SESSION["usertable"]["contestnumber"], $a["judgesite1"], $a["judge1"]);
   	        echo $ans[$i]["desc"] . " [judge=" . $uu["username"] . " (" . $a["judgesite1"] . ")]";
	    } else
	        echo $ans[$i]["desc"];
    }*/
?>
        </td>
    </tr>-->


<!--
      <tr>
        <td width="27%" align=right><b>Notify user:</b></td>
        <td width="83%">
          <input class=checkbox type=checkbox name="notifyuser" value="yes"
<?php
if (($s=DBContestClockInfo($_SESSION["usertable"]["contestnumber"])) == null)
        ForceLoad("../index.php");//index.php

if ($a["timestamp"] < $s["contestlastmileanswer"]) echo "checked"; ?>>
(do not change this unless you know exactly what you are doing)
        </td>
      </tr>
      <tr>
        <td width="27%" align=right><b>Update score board:</b></td>
        <td width="83%">
          <input class=checkbox type=checkbox name="updatescore" value="yes"
<?php if ($a["timestamp"] < $s["contestlastmilescore"]) echo "checked"; ?>>
(do not change this unless you know exactly what you are doing)
        </td>
      </tr>
-->
    </table>
  </center>
  <br>
  <script language="javascript">
    function conf() {
      if (confirm("Confirm?")) {
          document.form1.confirmation.value='confirm';
      }
    }
  </script>
  <center>
      <input class="btn btn-primary" type="submit" name="Submit" value="Juzgar" onClick="conf()">
      <input class="btn btn-primary" type="submit" name="open" value="Abrir ejecucion para re-juzgar" onClick="conf()">
      <input class="btn btn-primary" type="submit" name="cancel" value="Cancelar">
      <input class="btn btn-primary" type="submit" name="delete" value="Eliminar" onClick="conf()">
      <input class="btn btn-primary" type="reset" name="Submit2" value="Limpiar">
<br><br>
  </center>

  <center>
<br>
<b>Autojuez</b>
<input class="btn btn-primary" type="submit" name="giveup" value="Re-juzgar">
<br><br>
  <table class="table table-bordered">
  <tr>
        <td width="27%" align=right><b>Autojuez responde:</b></td>
        <td width="83%">
<?php
if($a["autobegin"]!="" && $a["autoend"]=="")
      echo "in progress";
else if($a["autoend"]!="") {
      if($a["autoanswer"]!="") echo $a["autoanswer"];
      else echo "Autojudging error";
} else
      echo "unavailable";
?>
        </td>
  </tr>
  <tr>
        <td width="27%" align=right><b>Autojuzgado por:</b></td>
<?php if($a["autobegin"]!="" && $a["autoend"]=="")
      echo "<td width=\"83%\">". $a["autoip"] ." since ". dateconvsimple($a["autobegin"]) ."</td>";
else if($a["autoend"]!="")
      echo "<td width=\"83%\">". $a["autoip"] ." from ". dateconvsimple($a["autobegin"]) ." to ". dateconvsimple($a["autoend"]) ."</td>";
else
      echo "<td width=\"83%\">unavailable</td>";
?>
  </tr>
  <tr>
        <td width="27%" align=right><b>Standard output:</b></td>
        <td width="83%">
<?php
if($a["autostdout"]!="") {
	echo "<a href=\"../filedownload.php?".filedownload($a["autostdout"],"stdout") ."\">stdout</a>\n";
      echo "<a href=\"#\" class=\"btn btn-primary\" onClick=\"window.open('../filewindow.php?".
	filedownload($a["autostdout"],"stdout") ."', 'View - STDOUT','width=680,height=600,scrollbars=yes,".
	   "resizable=yes')\">Ver</a>\n";
} else
      echo "unavailable";
?>
        </td>
  </tr>
  <tr>
        <td width="27%" align=right><b>Standard error:</b></td>
        <td width="83%">
<?php
if($a["autostderr"]!="") {
	echo "<a href=\"../filedownload.php?". filedownload($a["autostderr"],"stderr") . "\">stderr</a>\n";
	echo "<a href=\"#\" class=\"btn btn-primary\" onClick=\"window.open('../filewindow.php?".
	filedownload($a["autostderr"],"stderr") ."', 'View - STDERR','width=680,height=600,scrollbars=yes,".
	"resizable=yes')\">Ver</a>\n";
} else
      echo "unavailable";
?>
        </td>
  </tr>
  </table>
  </center>

</form>




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
<?php include '../updateform.php';?>
