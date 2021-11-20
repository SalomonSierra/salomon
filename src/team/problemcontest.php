<?php

if (!isset($_POST["confirmation"]) || $_POST["confirmation"] != "confirm")
	unset($_POST['noflush']);

require('header2.php');
if($contestinfo["usernumber"]!=$_SESSION["usertable"]["usernumber"]){
	ForceLoad("contest.php");//index.php
}
//retorna la informacion de la competencia en caso no retorna null


if(($ct = DBContestInfo(0)) == null)
	ForceLoad("../index.php");//index.php
if(!isset($contestnumber)){
	ForceLoad("contest.php");//index.php
}else{
	$cf=DBContestInfo($contestnumber);
}

//funcion para obtener todos los problemas con sus datos y creando la archivos si no existe con los datos

$prob = DBGetFullProblemData(true);
//para actualizar un problema
for ($i=0; $i<count($prob); $i++) {

  	if($prob[$i]["fake"]!='t') {
    	if (isset($_POST["SubmitProblem" . $prob[$i]['number']]) && $_POST["SubmitProblem" . $prob[$i]['number']] == 'Update' &&
		isset($_POST["colorname" . $prob[$i]['number']]) && strlen($_POST["colorname" . $prob[$i]['number']]) <= 100 &&
		isset($_POST["color" . $prob[$i]['number']]) && strlen($_POST["color" . $prob[$i]['number']]) <= 6 &&
		isset($_POST["problemname" . $prob[$i]['number']]) && $_POST["problemname" . $prob[$i]['number']] != "" && strlen($_POST["problemname" . $prob[$i]['number']]) <= 20) {

			if(strpos(trim($_POST["problemname" . $prob[$i]['number']]),' ')!==false) {
				MSGError('Problem short name cannot have spaces');
	        } else {
				$param = array();
				$param['number'] = $prob[$i]['number'];
				$param['name'] = trim($_POST["problemname" . $prob[$i]['number']]);
				$param['fake'] = 'f';
				$param['colorname'] = trim($_POST["colorname" . $prob[$i]['number']]);
				$param['color'] = trim($_POST["color" . $prob[$i]['number']]);
				DBNewProblem ($_SESSION["usertable"]["usernumber"], $param);
		     }
		     ForceLoad("problem.php");
	    }
  	}
}

?>
<br>
<script language="javascript">
    function conf2(url) {
		//¿Confirma la SUPRESIÓN del PROBLEMA y TODOS los datos asociados a él?
      if (confirm("confirma la eliminacion del problema y todos los datos asociados a el")) {
		  //¿Estás REALMENTE seguro de lo que estás haciendo? ¡LOS DATOS NO SE PUEDEN RECUPERAR!
		  if (confirm("¿Estás REALMENTE seguro de lo que estás haciendo? ¡LOS DATOS NO SE PUEDEN RECUPERAR!")) {
			  document.location=url;
		  } else {
			  document.location='problem.php';
		  }
      } else {
        document.location='problem.php';
      }
    }
    function conf3(url) {
		//¿Confirma la SUBELECIÓN del PROBLEMA?Confirm the UNDELETION of the PROBLEM
      if (confirm("Estas seguro de Recuper el Problema?")) {
		  document.location=url;
	  } else {
		  document.location='problem.php';
	  }
    }
</script>


<div class="container">
	<span class="text-success">PARA AÑADIR UN PROBLEMA A UNA COMPETENCIA, SIMPLEMENTE DAR CLICK EN EL BOTON "Añadir"</span>
</div>

<form name="form0" enctype="multipart/form-data" method="post" action="problem.php">
	<table class="table table-bordered table-hover">
	  <thead>
	    <tr>
			<th scope="col">Problema #</th>
		    <th scope="col">Nombre Pequeño</th>
		    <th scope="col">Titulo</th>
		    <th scope="col">Nombre base</th>
		    <th scope="col">Archivo descripcion</th>
		    <th scope="col">Setter</th>
			<!--  <td><b>Compare file</b></td>
			  <td><b>Timelimit</b></td>-->
			<th scope="col">Color & Acciones</th>
	    </tr>
	  </thead>
	  <tbody>
		  <?php

		  for ($i=0; $i<count($prob); $i++) {

		    	echo " <tr>\n";
		    	if($prob[$i]["fake"]!='t') {
					//myrawurlencode
					//devuelve la cadena condifica, primero codifica a base64 y luego remplaza algunos caracteres especiales
					//% a delimitadores de urul especiales
				  	 if(strpos($prob[$i]["fullname"],"(DEL)") !== false) {
				  		  echo "<td>".$prob[$i]["number"]." (eliminado)";
				  	 } else {
				  		  echo "<td>".$prob[$i]["number"];
				  	 }

					  echo "</td>\n";
				  	  //echo "</a></td>\n";
				  	  echo "<input type=hidden name=\"problemname" . $prob[$i]['number'] . "\" value=\"" . $prob[$i]["name"] . "\" />";
				  	  echo "  <td>" . $prob[$i]["name"] . "</td>\n";
				  	  //echo "  <td nowrap>";
				  	  //echo "<input type=\"text\" name=\"problemname" . $prob[$i]['number'] . "\" value=\"" . $prob[$i]["name"] . "\" size=\"4\" maxlength=\"20\" />";
				  	  //echo "</td>\n";
				 } else {
				      echo "  <td>" . $prob[$i]["number"] . " (fake)</td>\n";
				      echo "  <td>" . $prob[$i]["name"] . "</td>\n";
				 }

			     echo "  <td>" . $prob[$i]["fullname"] . "&nbsp;</td>\n";
			     echo "  <td>" . $prob[$i]["basefilename"] . "&nbsp;</td>\n";

			     if (isset($prob[$i]["descoid"]) && $prob[$i]["descoid"] != null && isset($prob[$i]["descfilename"])) {
					 //para descargar descripcion del archivo filedownload esta en globals.php
					 //funcion para encriptar datos enviados devuelve dato encriptado tipo url &file=
			  	     //echo "  <td> <a href=\"../filedownload.php?" . filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"]) . "\">" .

					 //Dada una cadena que contiene una ruta a un archivo o directorio,
					 // esta función devolverá el último componente de nombre.</a>
					 //basename($prob[$i]["descfilename"]) . "</td>\n";

					 echo "  <td><a href=\"../filedownload.php?" . filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"]) .
				 		"\">" . basename($prob[$i]["descfilename"]) . "</a>&nbsp;&nbsp;<a href=\"#\" class=\"btn btn-primary\" style=\"font-weight:bold\" onClick=\"window.open('../filewindow0.php?".filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"])."', 'Ver - PROBLEMA', 'width=680,height=600,scrollbars=yes,resizable=yes')\">Ver Problema</a></td>\n";

			     }
			     else
			         echo "  <td>&nbsp;</td>\n";

			     /*if ($prob[$i]["inputoid"] != null) {
			          $tx = $prob[$i]["inputhash"];
			      	  echo "  <td><a href=\"../filedownload.php?" . filedownload($prob[$i]["inputoid"] ,$prob[$i]["inputfilename"]) ."\">" .
			  		          $prob[$i]["inputfilename"] . "</a> " .
			  		         //"<img title=\"hash: $tx\" alt=\"$tx\" width=\"25\" src=\"../images/bigballoontransp-hash.png\" />" .
			                 "</td>\n";
			     }
			     else
			          echo "  <td>&nbsp;</td>\n";*/

				 //para setter
				 $userinfo=DBUserInfo($prob[$i]["user"]);
				 echo "  <td>".$userinfo["username"]."</td>";
			     echo "  <td>";
			     if($prob[$i]["fake"]!='t') {
			      	  if ($prob[$i]["color"]!="") {
			               echo "<img title=\"".$prob[$i]["color"]."\" alt=\"".$prob[$i]["colorname"]."\" width=\"25\" src=\"" .
			  	           balloonurl($prob[$i]["color"]) . "\" />\n";
						   //balloonurl //retorna un globo creado con el color dado. ruta
			          }
					  //myrawurlencode
  					//devuelve la cadena condifica, primero codifica a base64 y luego remplaza algunos caracteres especiales
  					//% a delimitadores de urul especiales
  				  	 if(strpos($prob[$i]["fullname"],"(DEL)") === false) {
						 if(DBProblemContestInfo($prob[$i]["number"],$cf["contestnumber"])){
							  echo "<button type=\"button\" onclick=\"save(".$prob[$i]["number"].",".$cf["contestnumber"].",'deleted')\" class=\"btn btn-warning\" name=\"".$prob[$i]["number"]."\" id=\"".$prob[$i]["number"]."\">Quitar</button>";
						 }else{
							  echo "<button type=\"button\" onclick=\"save(".$prob[$i]["number"].",".$cf["contestnumber"].",'')\" class=\"btn btn-success\" name=\"".$prob[$i]["number"]."\" id=\"".$prob[$i]["number"]."\">Añadir</button>";
						 }

  				  	  }

				  } else echo "&nbsp;";

			      echo "</td>\n";

			      echo " </tr>\n";
		   }

		   echo "</tbody></table></form>";
		   if (count($prob) == 0) echo "<br><center><b><font color=\"#ff0000\">NO PROBLEMS DEFINED</font></b></center>";

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
<?php include '../updateform.php';?>
<script>

//var valor=true;
function save(problem,contest,data) {
	//alert(data+" "+user+" "+contest);
	var uno = document.getElementById(problem);
	//alert(uno.innerText);
	//uno.innerText=="Añadir"?uno.innerText = "Quitar":uno.innerText = "Añadir";
	var data=data;
	var id=problem;
	if(uno.innerText=="Añadir"){
		uno.innerText = "Quitar";
		uno.className="btn btn-warning";
	}else{
		uno.innerText = "Añadir";
		uno.className="btn btn-success";
	}

	$.ajax({

			 url:"../include/i_problemcontest.php",
			 method:"POST",
			 data: {id:id, data:data, contest:contest},

			 success:function(data)
			 {
				  alert(data);
				  /*if(data.indexOf('Data updated.') !== -1)
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

				  }*/

			 }
	});

}
</script>
