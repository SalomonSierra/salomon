<?php
require 'header2.php';//la cabezera

if(isset($_GET["order"]) && $_GET["order"] != "") {

    $order = myhtmlspecialchars($_GET["order"]);
	$_SESSION["runline"] = $order;
} else {

	if(isset($_SESSION["runline"]))
        $order = $_SESSION["runline"];
    else
		$order = '';
}
?>
<!--$runphp = run.php-->
<form name="form1" method="post" action="<?php echo $runphp; ?>">
  <input type=hidden name="confirmation" value="noconfirm" />
  <br>
  <table class="table table-bordered table-hover">
     <thead>
  		<tr>
            <th scope="col"><a href="<?php echo $runphp; ?>?contest=<?php echo $_GET["contest"]; ?>&order=run">Run #</a></th>

            <?php if($runphp == "run.php") { ?>
            <th scope="col"><a href="<?php echo $runphp; ?>?contest=<?php echo $_GET["contest"]; ?>&order=user">User</a></th>
            <?php } ?>
            <th scope="col">Time</th>
            <th scope="col"><a href="<?php echo $runphp; ?>?contest=<?php echo $_GET["contest"]; ?>&order=problem">Problem</a></th>
            <th scope="col"><a href="<?php echo $runphp; ?>?contest=<?php echo $_GET["contest"]; ?>&order=language">Language</a></th>
            <!--  <td><b>Filename</b></td> -->
            <th scope="col"><a href="<?php echo $runphp; ?>?contest=<?php echo $_GET["contest"]; ?>&order=status">Status</a></th>
            <th scope="col"><a href="<?php echo $runphp; ?>?contest=<?php echo $_GET["contest"]; ?>&order=judge">Judge</a></th>
            <th scope="col">AJ</th>
            <th scope="col"><a href="<?php echo $runphp; ?>?contest=<?php echo $_GET["contest"]; ?>&order=answer">Answer</a></th>
  		</tr>
  	 </thead>
  	 <tbody>

<?php
if (($s=DBContestClockInfo($_SESSION["usertable"]["contestnumber"])) == null)
        ForceLoad("../index.php");//index.php

// forca aparecer as runs do proprio site
//obliga a que aparezcan las ejecuciones del sitio
//if (trim($s["sitejudging"])!="") $s["sitejudging"].=",".$_SESSION["usertable"]["usersitenumber"];
//else $s["sitejudging"]=$_SESSION["usertable"]["usersitenumber"];
//(contest,sitejudging(usersiten),''0 user(etc))
//capturamos toda la informacion acerca de todos los envios realizados de un contest
//funcion que me devuelve datos de las tablas runtable,problemtable,langtable,answertable,usertable
//y dependiendo del orden -1 es para no añadir mas a la consulta de la base de datos
$run = DBAllRunsInContest($_SESSION["usertable"]["contestnumber"],$order);
//$run = DBAllRunsInSites($_SESSION["usertable"]["contestnumber"], $s["sitejudging"], $order);

if(isset($_POST)) {

    $nrenew = 0;
    $nreopen = 0;
    for ($i=0; $i<count($run); $i++) {

	    if(isset($_POST["cbox_" . $run[$i]["number"]]) &&
		   $_POST["cbox_" . $run[$i]["number"]] != "") {
               //Vuelva a ejecutar la evaluación automática para las ejecuciones seleccionadas
		    if(isset($_POST["auto"]) && $_POST["auto"]=="Autojuez Re-juzgar ejecuciones selecionadas") {
                //dar en funcionamiento evaluacion automatica
                //funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
                //exito true alguna false
		        if (DBGiveUpRunAutojudging($_SESSION["usertable"]["contestnumber"],
					    $run[$i]["number"], '', '', true))
		                   $nrenew++;
		    }
            //Abrir ejecuciones seleccionadas para volver a juzgar
		    if(isset($_POST["open"]) && $_POST["open"]=="Abrir ejecuciones selecionadas para re-juzgar") {

                //dar en funcionamiento evaluacion automatica
                //funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
                //exito true alguna false
		        DBGiveUpRunAutojudging($_SESSION["usertable"]["contestnumber"],
					   $run[$i]["number"]);
                // devuelve una ejecución que estaba siendo respondida. Recibirá el número de ejecución,
                       // el número de sitio de ejecución y el número de concurso.
                       // intenta cambiar el estado a 'openrun'. Si no puede devolver falso
                //jefe ejecutar terminar
		        if (DBChiefRunGiveUp($run[$i]["number"],
					$_SESSION["usertable"]["contestnumber"]))
		              $nreopen++;
		    }
	    }
    }
    if($nrenew > 0) {
        //corre renovado para autojugar.
        MSGError($nrenew . " Ejecuciones para re-juzgar por el autojuez");
        ForceLoad($runphp);
    }
    if($nreopen > 0) {
        //corre reabierto.
        MSGError($nreopen . " Ejeciones abiertas para re-juzgar");
        ForceLoad($runphp);
    }
}

////saca todos los username de los usuario guardando en un matriz $a[i][usersitenu-usernumber]='username'
$us = DBAllUserNames($_SESSION["usertable"]["contestnumber"]);
for ($i=0; $i<count($run); $i++) {

    if(($run[$i]["status"] != "judged" && $run[$i]["status"] != 'deleted')) {

        if($runphp == "runchief.php")
            echo " <tr bgcolor=\"ff0000\">\n";
        else
            echo "<tr>\n";
        echo "  <td bgcolor=\"\">";//ff0000
    }
    else {
        echo "  <tr><td>";
    }
    echo "<input type=\"checkbox\" name=\"cbox_" . $run[$i]["number"] ."\" />";
    echo " <a href=\"" . $runeditphp . "?runnumber=".$run[$i]["number"] .
        "\">" . $run[$i]["number"] . "</a></td>\n";


    if($runphp == "run.php") {
        if ($run[$i]["user"] != "") {
	        echo "  <td>" . $us[$run[$i]["user"]] . "</td>\n";
        }
    }
    echo "  <td>" . dateconvminutes($run[$i]["timestamp"]) . "</td>\n";
    echo "  <td>" . $run[$i]["problem"] . "</td>\n";
    echo "  <td>" . $run[$i]["language"] . "</td>\n";
    //  echo "  <td nowrap>" . $run[$i]["filename"] . "</td>\n";
    if ($run[$i]["judge"] == $_SESSION["usertable"]["usernumber"] &&
      $run[$i]["status"] == "judging")
        $color="ff7777";
    else if ($run[$i]["status"]== "judged+" && $run[$i]["judge"]=="") $color="ffff00";
    else if ($run[$i]["status"]== "judged") $color="bbbbff";
    else if ($run[$i]["status"] == "judging" || $run[$i]["status"]== "judged+") $color="77ff77";
    else if ($run[$i]["status"] == "openrun") $color="ffff88";
    else $color="ffffff";

    echo "  <td bgcolor=\"#$color\">" . $run[$i]["status"] . "</td>\n";
    if ($run[$i]["judge"] != "") {
        //username-judge(judgesite)
	    echo "  <td>" . $us[$run[$i]["judge"]];
    } else
	    echo "  <td>&nbsp;";

    /*if ($run[$i]["judge1"] != "") {
	    echo " [" . $us[$run[$i]["judgesite1"] .'-'. $run[$i]["judge1"]] . " (" . $run[$i]["judgesite1"] . ")]";
    }*/
    /*if ($run[$i]["judge2"] != "") {
	    echo " [" . $us[$run[$i]["judgesite2"] .'-'. $run[$i]["judge2"]] . " (" . $run[$i]["judgesite2"] . ")]";
    }*/

    echo "</td>\n";

    if ($run[$i]["autoend"] != "") {
        $color="bbbbff";//azul blanqueado
        if ($run[$i]["autoanswer"]=="") $color="";//$color="ff7777";//rojo
    }
    else if ($run[$i]["autobegin"]=="") $color="ffff88";//color amarillo
    else $color="77ff77";//color verde
    echo "<td bgcolor=\"#$color\">&nbsp;&nbsp;</td>\n";

    if ($run[$i]["answer"] == "") {
        echo "  <td>&nbsp;</td>\n";
    } else {
        echo "  <td>" . $run[$i]["answer"];
        if($run[$i]['yes']=='t') {

            echo " <img alt=\"".$run[$i]["colorname"]."\" width=\"10\" ".
			    "src=\"" . balloonurl($run[$i]["color"]) ."\" />";
        }
        echo "</td>\n";
    }
    echo " </tr>\n";
}

echo "<tbody></table>";
//NO RUNS AVAILABLE
if (count($run) == 0) echo "<br><center><b><font color=\"#ff0000\">NO HAY EJECUCIONES DISPONIBLES</font></b></center>";
else {
?>
  <br>
  <script language="javascript">
    function conf() {
        if (confirm("Confirm?")) {
            document.form1.confirmation.value='confirm';
        }
    }
  </script>
  <center>
      <!--Click on the number of a run to edit it or select them with<br />the checkboxes and use the buttons to work on multiple runs:-->
      <b>Haga clic en el número de una ejecución para editarla o selecciónela con <br /> las casillas de verificación y use los botones para trabajar en varias ejecuciones:</b><br /><br />

      <input type="submit" class="btn btn-primary" name="auto" value="Autojuez Re-juzgar ejecuciones selecionadas" onClick="conf()">
      <input type="submit" class="btn btn-primary" name="open" value="Abrir ejecuciones selecionadas para re-juzgar" onClick="conf()">
      <br><br>
  </center>
  </form>
<?php
}
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
<script>

$(document).ready(function(){

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
