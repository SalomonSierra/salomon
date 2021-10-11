<?php

require('header.php');

if(($ct = DBContestInfo(0)) == null)
	ForceLoad("../index.php");

?>
<br>
<table class="table table-bordered table-hover">
	<thead>
		<tr>
			<th scope="col">Nombre</th>
			<th scope="col">Nombre Base</th>
			<th scope="col">Titulo</th>
			<th scope="col">Setter</th>
			<th scope="col">Archivo Descripcion</th>
			<th scope="col">AC</th>
			<th scope="col">Envios</th>

		</tr>
	</thead>
	<tbody>

<?php
//$prob = DBGetProblems($_GET["contest"]);
$prob = DBGetFullProblemData(true);
for ($i=0; $i<count($prob); $i++) {
	$prun=DBRunProblem($prob[$i]["number"]);
  echo " <tr>\n";
//  echo "  <td nowrap>" . $prob[$i]["number"] . "</td>\n";
  echo "  <td>" . $prob[$i]["number"];
  if($prob[$i]["color"] != "")
          echo " <img alt=\"".$prob[$i]["colorname"]."\" width=\"20\" ".
			  "src=\"" . balloonurl($prob[$i]["color"]) ."\" />\n";
  echo "</td>\n";
  echo "  <td>" . $prob[$i]["basefilename"] . "&nbsp;</td>\n";
  //$fabian="fabian";
  echo "  <td>" . $prob[$i]["fullname"] . "&nbsp;</td>\n";
  //para setter
  $userinfo=DBUserInfo($prob[$i]["user"]);
  echo "  <td>".$userinfo["username"]."</td>";
  if (isset($prob[$i]["descoid"]) && $prob[$i]["descoid"] != null && isset($prob[$i]["descfilename"])) {
    echo "  <td><a href=\"#\" class=\"btn btn-primary\" style=\"font-weight:bold\" onClick=\"window.open('../filewindow0.php?".filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"])."', 'Ver - PROBLEMA', 'width=680,height=600,scrollbars=yes,resizable=yes')\">Ver Problema</a></td>\n";
  }
  else
    echo "  <td>no description file available</td>\n";
  echo "  <td>" . $prun["ac"] . "</td>\n";

  if (isset($prob[$i]["descoid"]) && $prob[$i]["descoid"] != null && isset($prob[$i]["descfilename"])) {
    //echo "  <td><a href=\"../filedownload.php?" . filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"]) .
	//	"\">" . basename($prob[$i]["descfilename"]) . "</a>&nbsp;&nbsp;<a href=\"#\" class=\"btn btn-primary\" style=\"font-weight:bold\" onClick=\"window.open('../filewindow0.php?".filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"])."', 'Ver - PROBLEMA', 'width=680,height=600,scrollbars=yes,resizable=yes')\">Ver Problema</a></td>\n";
		echo "  <td>".$prun["all"]."&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<button type=\"button\" class=\"btn btn-success\" data-toggle=\"modal\"data-target=\"#enviar".$prob[$i]["number"]."\"name=\"problem2_button".$prob[$i]["number"]."\">Enviar</button>";
		echo " </td>\n";
  }
  else
    echo "  <td>".$prun["all"]."</td>\n";
  include("enviarmodal.php");
  echo " </tr>\n";
	//include("enviarmodal.php");
}
echo "</tbody></table>";
if (count($prob) == 0) echo "<br><center><b><font color=\"#ff0000\">NO HAY PROBLEMAS DISPONIBLES TODAVIA</font></b></center>";

?>

<?php
//para problemas
if(isset($_GET["idproblem"])){
	//echo $prob[$_GET["idproblem"]]["descfilename"];
	//$c = DBConnect();
	//DBExec($c, "begin work");
	//chmod($prob[$_GET["idproblem"]]["descfilename"], 0755);
	//$file='datoss.txt';
	//if (!copy($prob[$_GET["idproblem"]]["descfilename"], 'datoss.txt')) {
    //	echo "Error al copiar $fichero...\n";
	//}
	//pg_lo_export ($c, $prob[$_GET["idproblem"]]["descoid"],"fileee.txt");
	//$file=$prob[$_GET["idproblem"]]["descfilename"];
	//echo filetype($file);
	//echo mime_content_type('../file.txt');
	//echo "<embed type=\"text/plain\" src=\"".$file."\" width=\"300\" height=\"300\">";
	//DBExec($c, "commit work");
	//DBClose($c);
?>
<!--dentro del if-->

<script type="text/javascript">
//document.querySelector('').addEventListener('change',())
let archivo='file.php';
//document.write('hola');
//let pdfFileUrl=URL.createObjectURL(archivo)+"#toolbar=0";

//document.querySelector('#vistaprevia').setAttribute('src',pdfFileUrl);
</script>

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
function enviar(problem){
	var problem = $('#problemnumber'+problem).val();
	var language = $('#language'+problem).val();
	var source = String($('#sourcefile'+problem).val());
	if(problem != '' && language != -1 && source.length > 1 ){

		//crea un nuevo objet de stipo FormData

		var formdata= new FormData($("#form_submit"+problem)[0]);
		//formdata.append("id",problem);

		$.ajax({
			 data: formdata,
			 url:"../include/i_enviar.php",
			 type:"POST",
			 contentType: false,
			 processData: false,

			 success:function(data)
			 {

				 if(data == "Yes"){
					 alert(".:YES:.");
					 $('#enviar'+problem).hide();
					 //location.reload();
					 location.href="status.php";
				 }else {
					  if(data == "No"){
						  alert("Error al subir");
						  $('#enviar'+problem).hide();
						  location.reload();
					  }else {
						  alert(data);
						  $('#enviar'+problem).hide();
						  location.reload();
					  }
				 }


			 }
		});

	}
	else
	{
		 alert("Both Fields are required");
	}

	/*$(document).ready(function () {
		var problemnumber = $('#problemnumber'+problem).val();
		var language = $('#language'+problem).val();
		var probleminput = String($('#probleminput'+problem).val());

		alert(problemnumber+" "+language+" "+probleminput);
    });*/
}


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
