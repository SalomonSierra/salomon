<?php

require 'header.php';

$contest=$_SESSION["usertable"]["contestnumber"];
//resivimos la info de la competencia parasando el id de contest
if(($ct = DBContestInfo($contest)) == null)
	ForceLoad("$loc/index.php");//index.php

	/*

    if (isset($_POST["SubmitDC"]) && $_POST["SubmitDC"] == "Delete ALL clars") {

 	    if ($_POST["confirmation"] == "confirm") {
			//funcion para eliminar todos los registro de la tabla clartable dado contest, site.
			//tambien actualiza la table sitetable sitenextclar=0

 		    DBSiteDeleteAllClars ($_SESSION["usertable"]["contestnumber"], -1,
 			    $_SESSION["usertable"]["usernumber"], $_SESSION["usertable"]["usersitenumber"]);
 	    }
 	    ForceLoad("contest.php");
    }*/
    if (isset($_POST["SubmitDR"]) && $_POST["SubmitDR"] == "Eliminar Todas las Ejecuciones") {
	    if ($_POST["confirmation"] == "confirm") {
			//funcion para vaciar la tabla runtable teniendo en cuenta contest site
			//user usersite tambien actualizar sitetable sitenextrun=0
		     DBSiteDeleteAllRuns ($_SESSION["usertable"]["contestnumber"], -1,
			   $_SESSION["usertable"]["usernumber"], $_SESSION["usertable"]["usersitenumber"]);
	    }
	    ForceLoad("contest.php");
    }
    /*if (isset($_POST["SubmitDT"]) && $_POST["SubmitDT"] == "Delete ALL tasks") {
	    if ($_POST["confirmation"] == "confirm") {
			////funcion para vaciar la tabla tasktable teniendo en cuenta contest site user
			//usersite tambien actualizar sitetable sitenexttask=0
		    DBSiteDeleteAllTasks ($_SESSION["usertable"]["contestnumber"], -1,
			   $_SESSION["usertable"]["usernumber"], $_SESSION["usertable"]["usersitenumber"]);
	    }
	    ForceLoad("contest.php");
    }*/
    /*if (isset($_POST["SubmitDB"]) && $_POST["SubmitDB"] == "Delete ALL bkps") {
	    if ($_POST["confirmation"] == "confirm") {
			//para eliminar todos los registros de la tabla bkptable enviado el contest y el site
		    DBSiteDeleteAllBkps ($_SESSION["usertable"]["contestnumber"], -1,
			   $_SESSION["usertable"]["usernumber"], $_SESSION["usertable"]["usersitenumber"]);
	    }
	    ForceLoad("contest.php");
    }*/


if (isset($_POST["Submit3"]) && isset($_POST["penalty"]) && is_numeric($_POST["penalty"]) &&
    isset($_POST["maxfilesize"]) && isset($_POST["name"]) &&
    $_POST["name"] != "" && isset($_POST["lastmileanswer"]) && is_numeric($_POST["lastmileanswer"]) &&
    isset($_POST["lastmilescore"]) && is_numeric($_POST["lastmilescore"]) &&
    isset($_POST["duration"]) && is_numeric($_POST["duration"]) &&
    isset($_POST["startdateh"]) && $_POST["startdateh"] >= 0 && $_POST["startdateh"] <= 23 &&
    isset($_POST["startdatemin"]) && $_POST["startdatemin"] >= 0 && $_POST["startdatemin"] <= 59 &&
    isset($_POST["startdated"]) && isset($_POST["startdatem"]) && isset($_POST["startdatey"]) &&
    checkdate($_POST["startdatem"], $_POST["startdated"], $_POST["startdatey"])) {

	if ($_POST["confirmation"] == "confirm") {

		$param['number']=$contest;

			$at = true;

			//Actualizar el concurso y todos los sitios
			//if($_POST["Submit3"] == "Update Contest and All Sites") $at = true;
			//checkdate Comprueba la validez de una fecha formada por los argumentos. Una fecha
	        // se considera válida si cada parámetro está propiamente definido.
	        //mktime ( int $hour = date("H") , int $minute = date("i") , int $second = date("s") ,
	         //int $month = date("n") , int $day = date("j") , int $year = date("Y") , int $is_dst = -1 ) : int
	         //Devuelve la marca de tiempo Unix correspondiente a los argumentos dados.
	         //Esta marca de tiempo es un entero que contiene el número de segundos entre
	         //la Época Unix (1 de Enero del 1970 00:00:00 GMT) y el instante especificado.
			$t = mktime ($_POST["startdateh"], $_POST["startdatemin"], 0,
						 $_POST["startdatem"], $_POST["startdated"], $_POST["startdatey"]);

			$param['name']=$_POST["name"];
			$param['startdate']=$t;
			$param['duration']=$_POST["duration"]*60;
			$param['lastmileanswer']=$_POST["lastmileanswer"]*60;
			$param['lastmilescore']= $_POST["lastmilescore"]*60;
			$param['penalty']=$_POST["penalty"]*60;
			$param['maxfilesize']=$_POST["maxfilesize"]*1000;
			$param['active']=0;



			//para ver resultados
	        if(isset($_POST["activeresult"]))
				$param['activeresult']= $_POST["activeresult"];
			if(isset($_POST["private"]))
				$param['private']= $_POST["private"];

			$param['atualizasites']=$at;

		//funccion para actulizar contesttable de true a false y sitetable, sitetimetable con valores nuevos
		DBUpdateContest ($param);

	}
	//devuelve la informacion de la competencia
	if(($ct = DBContestInfo($contest)) == null)
	  	ForceLoad("$loc/index.php");//index.php

	ForceLoad("contest.php");
}

?>

<br>

<div class="container">

	<form name="form1" enctype="multipart/form-data" method="post" action="contest.php">

	    <input type=hidden name="confirmation" value="noconfirm" />
	    <script language="javascript">
		    function conf() {
		       if (confirm("Confirm?")) {
		           document.form1.confirmation.value='confirm';
		       }
		    }
			function conf2() {
				//Esto reiniciará toda la información relacionada con el inicio / parada en todos los sitios. \ N \
				//Si tiene un concurso en curso, el resultado es impredecible. ¿Estas realmente seguro?
		      	if (confirm("This will restart all start/stop related information in all the sites.\n\
		If you have a contest running, the result is unpredictable. Are you really sure?")) {
		        	document.form1.confirmation.value='confirm';
		      	}
		    }
	    </script>
		<div class="form-group row">
            <label for="" class="col-sm-4 col-form-label">Numero de Competencia:</label>
            <div class="col-sm-8">
				<?php
				echo $contest;
				?>
            </div>
        </div>
		<div class="form-group row">
            <label for="name" class="col-sm-4 col-form-label">Nombre:</label>
            <div class="col-sm-8">
                <input type="text"  class="form-control" id="name" name="name" value="<?php echo $ct["contestname"]; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="startdateh" class="col-sm-2 col-form-label">Fecha de Inicio:</label>
            hh:mm
            <div class="col-sm-1">
                <input type="text" class="form-control" id="startdateh" name="startdateh" value="<?php echo date("H",$ct["conteststartdate"]);  ?>" maxlength="2">
            </div>
            :
            <div class="col-sm-1">
                <input type="text" class="form-control" id="startdatemin" name="startdatemin" value="<?php echo date("i",$ct["conteststartdate"]);  ?>">
            </div>
            &nbsp; &nbsp; dd/mm/yyyy
            <div class="col-sm-1">
                <input type="text" class="form-control" id="startdated" name="startdated" value="<?php echo date("d",$ct["conteststartdate"]);  ?>">
            </div>
            /
            <div class="col-sm-1">
                <input type="text" class="form-control" id="startdatem" name="startdatem" value="<?php echo date("m",$ct["conteststartdate"]);  ?>">
            </div>
            /
            <div class="col-sm-1">
                <input type="text" class="form-control" id="startdatey" name="startdatey" value="<?php echo date("Y",$ct["conteststartdate"]);  ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="duration" class="col-sm-4 col-form-label">Duracion (en min.):</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="duration" name="duration" value="<?php echo $ct["contestduration"]/60; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="lastmileanswer" class="col-sm-4 col-form-label">Dejar de responder (en min.):</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="lastmileanswer" name="lastmileanswer" value="<?php echo $ct["contestlastmileanswer"]/60; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="lastmilescore" class="col-sm-4 col-form-label">Detener scoreboard (en min.):</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="lastmilescore" name="lastmilescore" value="<?php echo $ct["contestlastmilescore"]/60; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="penalty" class="col-sm-4 col-form-label">Penalizacion (en min.):</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="penalty" name="penalty" value="<?php echo $ct["contestpenalty"]/60; ?>">
            </div>
        </div>
        <div class="form-group row">
			<!--Tamaño máximo de archivo permitido para equipos (en KB):-->
            <label for="maxfilesize" class="col-sm-4 col-form-label">Tamaño máximo de archivo permitido para equipos (en KB):</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="maxfilesize" name="maxfilesize" value="<?php echo $ct["contestmaxfilesize"]/1000; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="" class="col-sm-4 col-form-label">Su configuración de PHP. permite como máximo:</label>
            <div class="col-sm-8">
                <?php echo ini_get('post_max_size').'B(max. post) and '.ini_get('upload_max_filesize').'B(max. filesize)'; ?>
				<br>
				<!--de caducidad de la sesión y... como duración de la cookie (0 significa ilimitado)-->
				<?php echo ini_get('session.gc_maxlifetime').'s of session expiration and ' . ini_get('session.cookie_lifetime') . ' as cookie lifetime (0 means unlimited)'; ?>
			</div>
        </div>
		<!--para urlcontest-->
		<!--PARA desbloquear-->
		<!--PARA LLAVES DE CONTESTS-->
		<div class="form-group form-check">
			<label class="form-check-label" for="active">Competencia Privada</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php
				if ($ct["contestprivate"] == "t")
					echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"private\" id=\"private\" checked value=\"t\" />";
				else
					echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"private\" id=\"private\" value=\"t\" />";
			?>

		</div>
		<div class="form-group form-check">
			<label class="form-check-label" for="active">Ver respuestas</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php
				if ($ct["contestactiveresult"] == "t")
					echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"activeresult\" id=\"activeresult\" checked value=\"t\" />";
				else
					echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"activeresult\" id=\"activeresult\" value=\"t\" />";
			?>

		</div>

		<div class="form-group row">
			<input type="submit" class="btn btn-primary" name="Submit3" value="Actualizar" onClick="conf()">&nbsp;
			<input type="submit" class="btn btn-primary" name="SubmitDR" value="Eliminar Todas las Ejecuciones" onClick="conf2()">&nbsp;
  		   	<input type="reset" class="btn btn-primary" name="Submit4" value="Limpiar">
        </div>



	</form>
</div>

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
