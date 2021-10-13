<?php

require 'header.php';
if(isset($_GET["new"]) && $_GET["new"] == "1"){
    //creamos un nuevo contest y insertamos problem fake, language, respuestas del juez
    //funcion para create un nuevo sitio tambien esta incluido sitetimetable
    $usernumber=$_SESSION["usertable"]["usernumber"];//id del usuario propietario de la competencia
    $n= DBNewContest($usernumber);
    ForceLoad("contest.php?contest=$n#form_contest");
}

if(isset($_GET["contest"]) && is_numeric($_GET["contest"]))
    $contest=$_GET["contest"];
else
    $contest=0;//mejorar....
    //$contest=$_SESSION["usertable"]["contestnumber"];//mejorar....
//retorna la informacion de la competencia en caso no retorna null
if(($ct=DBContestInfo(0)) == null)
    ForceLoad("../index.php");//index.php


if(isset($_POST["Submit3"]) && isset($_POST["penalty"]) && is_numeric($_POST["penalty"]) &&
        isset($_POST["maxfilesize"]) &&
        isset($_POST["name"]) && $_POST["name"] != "" && isset($_POST["lastmileanswer"]) &&
        is_numeric($_POST["lastmileanswer"]) &&
        isset($_POST["lastmilescore"]) && is_numeric($_POST["lastmilescore"]) && isset($_POST["duration"]) &&
        is_numeric($_POST["duration"]) && isset($_POST["scorelevel"]) && is_numeric($_POST["scorelevel"]) &&
        isset($_POST["startdateh"]) && $_POST["startdateh"]>=0 && $_POST["startdateh"] <= 23 &&
        isset($_POST["contest"]) && is_numeric($_POST["contest"]) &&
        isset($_POST["startdatemin"]) && $_POST["startdatemin"] >=0 && $_POST["startdatemin"]<=59 &&
        isset($_POST["startdated"]) && isset($_POST["startdatem"]) && isset($_POST["startdatey"]) &&
        checkdate($_POST["startdatem"], $_POST["startdated"], $_POST["startdatey"])){
    if($_POST["confirmation"] == "confirm"){
        //checkdate Comprueba la validez de una fecha formada por los argumentos. Una fecha
        // se considera válida si cada parámetro está propiamente definido.
        //mktime ( int $hour = date("H") , int $minute = date("i") , int $second = date("s") ,
         //int $month = date("n") , int $day = date("j") , int $year = date("Y") , int $is_dst = -1 ) : int
         //Devuelve la marca de tiempo Unix correspondiente a los argumentos dados.
         //Esta marca de tiempo es un entero que contiene el número de segundos entre
         //la Época Unix (1 de Enero del 1970 00:00:00 GMT) y el instante especificado.
        $t=mktime($_POST["startdateh"], $_POST["startdatemin"], 0, $_POST["startdatem"],
                        $_POST["startdated"], $_POST["startdatey"]);
        /*if($_POST["Submit3"] == "Activar") $ac=1;
        else $ac=0;*/
        $param['number']=$_POST["contest"];
        $param['name']=$_POST["name"];
        $param['startdate']=$t;
        $param['duration']=$_POST["duration"]*60;
        $param['lastmileanswer']=$_POST["lastmileanswer"]*60;
        $param['lastmilescore']=$_POST["lastmilescore"]*60;
        $param['penalty']=$_POST["penalty"]*60;
        $param['maxfilesize']=$_POST["maxfilesize"]*1000;
        $param['password']=$_POST["password"];
        //$param['active']=$ac;



        //para ver resultados
        if(isset($_POST["activeresult"]))
			$param['activeresult']= $_POST["activeresult"];
        if(isset($_POST["private"]))
    		$param['private']= $_POST["private"];
        //del sitio
        $param['contestjudging']= $_POST["judging"];
        //$param['sitetasking']= $_POST["tasking"];
        if(isset($_POST["autoend"]))
            $param['contestautoend']= $_POST["autoend"];
        if(isset($_POST["globalscore"]))
            $param['contestglobalscore']= $_POST["globalscore"];
        if(isset($_POST["active"]))
            $param['contestactive']=$_POST["active"];
        $param['contestscorelevel']=$_POST["scorelevel"];
        $param['contestpermitlogins']='';
        if(isset($_POST["autojudge"]))
            $param['contestautojudge']=$_POST["autojudge"];
        //$param['sitechiefname']=$_POST["chiefname"];
        //funcion para actulizar contesttable de true a false y sitetable, sitetimetable con valores nuevos

        DBUpdateContest($param);
        /*if($ac==1 && $_POST["contest"] != $_SESSION["usertable"]["contestnumber"]){
            $cf=globalconf();
            //Debes iniciar sesión en el nuevo concurso. La contraseña de administrador estándar está vacía (si aún no se ha cambiado)
            if($cf["basepass"] == "")
                MSGError("Debes iniciar sesión en el nuevo competencia. La contraseña de administrador estándar está vacía (si aún no se ha cambiado).");
            else
                MSGError("Debes iniciar sesión en el nuevo competencia. La contraseña de administrador estándar es ".$cf["basepass"]." (si aún no se ha cambiado).");
            ForceLoad("../index.php");//index.php Debes iniciar sesión en el nuevo concurso. La contraseña de administrador estándar es
        }*/
    }
    ForceLoad("contest.php");
}










?>
<br>
<div class="container">
    <a href="contest.php?new=1" class="btn btn-success">Crear Competencia</a>
    <br>
    <br>
<table class="table table-bordered table-hover">
	<thead>
		<tr class="d-flex">
			<th class="col-1" scope="col">ID</th>
			<th class="col-5" scope="col">Name</th>
			<th class="col-2" scope="col">Status</th>
			<th class="col-2" scope="col">Private</th>
			<th class="col-2" scope="col">Action</th>
		</tr>
	</thead>
	<tbody>

<?php
//$prob = DBGetProblemsGlobal($_SESSION["usertable"]["contestnumber"]);
//$ct = DBContestInfoAll();
$cs=DBAllContestInfo();
$ac=DBGetActiveContest();
for ($i=0; $i<count($cs); $i++) {
    $cnumber=$cs[$i]["contestnumber"];
    list($clockstr,$clocktype)=siteclock2($cs[$i]["contestnumber"]);
	if($clocktype==-1000000000){
		echo " <tr class=\"d-flex table-secondary\">\n";
	}else{
		if($cs[$i]["contestprivate"]=='f'){
			echo " <tr class=\"d-flex table-success\">\n";
		}else{
			echo " <tr class=\"d-flex table-warning\">\n";
		}
	}
//  echo "  <td nowrap>" . $prob[$i]["number"] . "</td>\n";
  echo "  <td class=\"col-1\">" .$cs[$i]["contestnumber"]."</td>\n";
  echo "  <td class=\"col-5\"><a href=\"problemcontest.php?contest=$cnumber\">" . $cs[$i]["contestname"] . "&nbsp;</a></td>\n";

  echo "  <td class=\"col-2\">" . $clockstr . "&nbsp;</td>\n";
  if($cs[$i]["contestprivate"]=='t'){
	  echo "  <td class=\"col-2 text-danger\">Privado</td>\n";
  }else{
	  echo "  <td class=\"col-2 text-success\">Publico</td>\n";
  }
  echo "<td class=\"col-2\"> <a href=\"contest.php?contest=".$cs[$i]["contestnumber"]."#form_contest\" class=\"btn btn-primary\">Actualizar</a></td>";

  echo " </tr>\n";
}
echo "</tbody></table>";
if (count($cs) == 0) echo "<br><center><b><font color=\"#ff0000\">NO HAY COMPETENCIAS</font></b></center>";

?>


    <?php if(isset($_GET["contest"])){ $ct=DBContestClockInfo($_GET["contest"]);?>
    <a id="form_contest"></a>
    <center><b><font color="#ff30a2">DATOS DE LA COMPENTECIA</font></b></center>
    <form name="form1" enctype="multipart/form-data" method="post" action="contest.php">
        <input type="hidden" name="confirmation" value="noconfirm">
        <script language="javascript">
            function conf() {
                if(confirm("Confirm?")){
                    document.form1.confirmation.value='confirm';
                }
            }
            function newcontest(){
                document.location='contest.php?new=1';
            }
            function contestch(n){
                if(n==null){
                    k=document.form1.contest[document.form1.contest.selectedIndex].value;
                    if(k=='new') newcontest();
                    else document.location='contest.php?contest='+k+'#form_contest';
                }else{
                    document.location='contest.php?contest='+n+'#form_contest';
                }

            }
        </script>
        <br><br>
        <div class="form-group row">
            <label for="contest" class="col-sm-2 col-form-label">Numero de Competencia:</label>
            <div class="col-sm-10">

                <br>
                <select class="btn btn-primary" onChange="contestch()" id="contest" name="contest">
                    <?php
                    //retorna todas las competencias

                    $isfake=false;
                    for ($i=0; $i <count($cs) ; $i++) {
                        echo "<option value=\"".$cs[$i]["contestnumber"]."\" ";
                        if($contest == $cs[$i]["contestnumber"]){
                            echo "selected";
                            if($cs[$i]["contestnumber"] == 0) $isfake=true;
                        }

                        echo ">".$cs[$i]["contestnumber"].($cs[$i]["contestactive"]=="t"?"":"")."</option>\n";
                    }
                    ?>
                    <option class="bg-success"value="new">nueva competencia</option>
                </select>
            </div>
        </div>



        <div class="form-group row">
            <label for="name" class="col-sm-4 col-form-label">Nombre:</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $ct["contestname"]; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="startdateh" class="col-sm-2 col-form-label">Fecha de inicio:</label>
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
            <label for="duration" class="col-sm-4 col-form-label">Duración (en minutos):</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="duration" name="duration" value="<?php echo $ct["contestduration"]/60; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="lastmileanswer" class="col-sm-4 col-form-label">Deja de responder (en minutos):</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="lastmileanswer" name="lastmileanswer" value="<?php echo $ct["contestlastmileanswer"]/60; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="lastmilescore" class="col-sm-4 col-form-label">Detener score (en minutos):</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="lastmilescore" name="lastmilescore" value="<?php echo $ct["contestlastmilescore"]/60; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="penalty" class="col-sm-4 col-form-label">Penalización (en minutos):</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="penalty" name="penalty" value="<?php echo $ct["contestpenalty"]/60; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="maxfilesize" class="col-sm-4 col-form-label">Tamaño máximo de archivo permitido para equipos (en KB):</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="maxfilesize" name="maxfilesize" value="<?php echo $ct["contestmaxfilesize"]/1000; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="" class="col-sm-4 col-form-label">Su configuración de PHP. permite como máximo:</label>
            <div class="col-sm-8">
                <?php echo ini_get('post_max_size').'B(max. post) and '.ini_get('upload_max_filesize').'B(max. filesize)'; ?>
            </div>
        </div>
        <div class="form-group form-check">
            <label class="form-check-label" for="private">Competencia Privada</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
                if ($ct["contestprivate"] == "t")
                    echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"private\" id=\"private\" checked value=\"t\" />";
                else
                    echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"private\" id=\"private\" value=\"t\" />";
            ?>

            <div class="col-md-2">
                <input type="text" class="form-control" name="password" value="<?php echo $ct["contestpassword"]; ?>" placeholder="password">
            </div>


		</div>
        <div class="form-group form-check">
			<label class="form-check-label" for="activeresult">Ver respuestas</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php
				if ($ct["contestactiveresult"] == "t")
					echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"activeresult\" id=\"activeresult\" checked value=\"t\" />";
				else
					echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"activeresult\" id=\"activeresult\" value=\"t\" />";
			?>

		</div>
        <!--MEJORAR PARA LA COMPETENCIA-->

    	<?php
    	if (!$ct["contestrunning"]) {
    	?>
    		<div class="form-group row">
    			<label for="" class="col-sm-4 col-form-label">Competencia terminado en:</label>
    			<div class="col-sm-8">
    				<b>
    				<?php echo dateconv($ct["contestendeddate"]); ?>
    				</b>
    			</div>
    	   </div>

        <?php
    		if($ct["contestautoended"])
    			$w = (int) ($ct["contestduration"]/60);
    		else
    			$w = (int) ($ct["currenttime"]/60);
        ?>
    		<div class="form-group row">
    			<label for="" class="col-sm-4 col-form-label">Real duration:</label>
    			<div class="col-sm-8">
    				<b>
    				<?php echo $w."minutes"; ?>
    				</b>
    			</div>
    	   </div>

    	<?php
    	}
    	?>
        <!--
    	   <div class="form-group row">

    			<label for="" class="col-sm-4 col-form-label">Runs que serán por:</label>
    			<div class="col-sm-8">
    				<input readonly type="text" name="judging" class="form-control" value="<?php echo $ct["contestjudging"]; ?>" size="20" maxlength="200" />
    			</div>
    	   </div>-->
           <input readonly type="hidden" name="judging" class="form-control" value="<?php echo $ct["contestjudging"]; ?>" size="20" maxlength="200" />
    		<div class="form-group form-check">
    			<label class="form-check-label" for="active">Activo:</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    			<?php
    				if ($ct["contestactive"] == "t")
    					echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"active\" id=\"active\" checked value=\"t\" />";
    				else
    					echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"active\" id=\"active\" value=\"t\" />";
    			?>

    		</div>
    		<div class="form-group form-check">
    			<label class="form-check-label" for="autoend">Auto finalización:</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    			<?php
    				if ($ct["contestautoend"] == "t")
    					echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"autoend\" id=\"autoend\" checked value=\"t\" />";
    				else
    					echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"autoend\" id=\"autoend\" value=\"t\" />";
    			?>

    		</div>
    		<div class="form-group row">
     		    <!--score global
     		 	<label for="" class="col-sm-4 col-form-label">Score Global:</label>-->
     		 	<div class="col-sm-8">
    				<input type="hidden" name="globalscore" class="form-control" value="<?php echo $ct["contestglobalscore"]; ?>" size="20" maxlength="50" />
     		 	</div>
     	    </div>
    		<div class="form-group form-check">
    			<!--Autojuzgar (sin interacción humana):Autojudge (without human interaction):-->
    			<label class="form-check-label" for="autojudge">Autojuzgar (sin interacción humana):</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    			<?php
    				if ($ct["contestautojudge"] == "t")
    					echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"autojudge\" id=\"autojudge\" checked value=\"t\" />";
    				else
    					echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"autojudge\" id=\"autojudge\" value=\"t\" />";
    				echo "&lt;- experimental";
    			?>

    		</div>
    		<!--Los inicios de sesión son:-->
    		<div class="form-group row">

     		 	<label for="" class="col-sm-4 col-form-label">Los inicios de sesión son:</label>
     		 	<div class="col-sm-8">
    				<?php
    						  if ($ct["contestpermitlogins"] == "t")
    							echo "activados";
    						  else
    							echo "desactivados";
    				?>
     		 	</div>
     	    </div>
    	    <!--nivel de puntuacion-->
    		<div class="form-group row">
    			<!--
     		 	<label for="" class="col-sm-4 col-form-label">Nivel de score:</label>-->
     		 	<div class="col-sm-8">
    				<input type="hidden" class="form-control" name="scorelevel" value="<?php echo $ct["contestscorelevel"]; ?>" size="2" maxlength="2" />
     		 	</div>
     	    </div>

    	   <div class="form-group row">
    		  <label for="" class="col-sm-4 col-form-label">Numero de Ejecuciones:</label>
    		  <div class="col-sm-8">
    			  <?php echo $ct["contestnextrun"]; ?>
    		  </div>
    	  </div>



        <div class="form-group row">
            <input type="submit" name="Submit3" onClick="conf()" class="btn btn-primary" value="Enviar">&nbsp;

            <input type="reset" name="Submit4" class="btn btn-primary" value="Limpiar">
        </div>


    </form>
    <?php }?>





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
