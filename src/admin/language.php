<?php

require('header.php');

if(($ct = DBContestInfo(0)) == null)
	ForceLoad("../index.php");//index.php

/*if (isset($_GET["delete"]) && is_numeric($_GET["delete"])) {
	$param["number"] = $_GET["delete"];
    //recibe el numero de concurso y el numero de respuesta y lo elimina si su tipo no es falso
    //actualizar la tabla langtable namelan añade con (DEL) y tambien el runtable
	DBDeleteLanguage ($_SESSION["usertable"]["contestnumber"],$param);
	ForceLoad("language.php");
}*/

if (isset($_POST["Submit3"]) && isset($_POST["langnumber"]) && is_numeric($_POST["langnumber"]) &&
    isset($_POST["langname"]) && $_POST["langname"] != "") {
	if(strpos(trim($_POST["langname"]),' ')!==false) {
		$_POST["confirmation"]='';
		MSGError('Language name cannot have spaces');
	} else {
    	if ($_POST["confirmation"] == "confirm") {
    		$param = array();
    		$param['number'] = $_POST['langnumber'];
    		$param['name'] = trim($_POST['langname']);
    		$param['extension'] = $_POST['langextension'];
            ////es para actulizar o insertar un nuevo registro el name y extension en la tabla langtable
    		DBNewLanguage ($param);
    	}
	}
	ForceLoad("language.php");
}
?>
<br>
  <script language="javascript">
    function conf2(url) {
        //¿Confirma la SUPRESIÓN del IDIOMA y TODOS los datos asociados a él (incluidos los ENVIOS)?
      if (confirm("Confirm the DELETION of the LANGUAGE and ALL data associated to it (including the SUBMISSIONS)?")) {
		  if (confirm("Are you REALLY sure about what you are doing? DATA CANNOT BE RECOVERED!")) {
			  document.location=url;
		  } else {
			  document.location='language.php';
		  }
      } else {
        document.location='language.php';
      }
    }
  </script>
<table class="table table-sm table-bordered table-hover">
	<thead>
		<tr>
		 <td scope="col">Lenguaje #</td>
		 <td scope="col">Nombre</td>
		 <td scope="col">Extension</td>
		</tr>
	</thead>
	<tbody>

<?php
// recibe el número del concurso
// devuelve una matriz, donde cada línea tiene el número de atributos (número de idioma)
//y el nombre (nombre del idioma)
$lang = DBGetLanguages();
$cf = globalconf();
for ($i=0; $i<count($lang); $i++) {
  echo " <tr>\n";
  //echo "  <td><a href=\"javascript: conf2('language.php?delete=" . $lang[$i]["number"] . "')\">" .
 //	  $lang[$i]["number"] . "</a></td>\n";
 echo "  <td>".$lang[$i]["number"] . "</td>\n";
  echo "  <td>" . $lang[$i]["name"] . "</td>\n";
  echo "  <td>" . $lang[$i]["extension"] . "</td>\n";
  echo " </tr>\n";
}
echo "</tbody>\n</table>";
if (count($lang) == 0) echo "<br><center><b><font color=\"#ff0000\">SIN LENGUAJES DEFINIDOS</font></b></center>";

?>
<!--Clicking on a language number will DELETE it.<br>
WARNING: deleting a language will remove EVERYTHING related to it.<br>
It is NOT recommended to change anything while the contest is running.-->


<form name="form1" enctype="multipart/form-data" method="post" action="language.php">
  <input type=hidden name="confirmation" value="noconfirm" />
  <script language="javascript">
    function conf() {
      if (confirm("Confirm?")) {
        document.form1.confirmation.value='confirm';
      }
    }
  </script>
  <center>
      <!--To insert/edit a language, enter the data below.<br>
      Note that any changes will overwrite the already defined data.<br>-->
<b>Para insertar / editar un lenguaje, ingrese los datos a continuación. <br>
Tenga en cuenta que cualquier cambio sobrescribirá los datos ya definidos. <br><br>
</b>

    <table border="0">
      <tr>
        <td width="35%" align=right>Numero:</td>
        <td width="65%">
          <input type="text" name="langnumber" value="" size="20" maxlength="20" />
        </td>
      </tr>
      <tr>
        <td width="35%" align=right>Nombre:</td>
        <td width="65%">
          <input type="text" name="langname" value="" size="20" maxlength="20" />
        </td>
      </tr>
      <tr>
        <td width="35%" align=right>Extension:</td>
        <td width="65%">
          <input type="text" name="langextension" value="" size="20" maxlength="20" />
        </td>
      </tr>
    </table>
  </center>
  <center>
      <input type="submit" class="btn btn-primary" name="Submit3" value="Enviar" onClick="conf()">
      <input type="reset" class="btn btn-primary" name="Submit4" value="Limpiar">
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
