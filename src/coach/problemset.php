<?php
if (!isset($_POST["confirmation"]) || $_POST["confirmation"] != "confirm")
	unset($_POST['noflush']);

require('header.php');
//retorna la informacion de la competencia en caso no retorna null

if(($ct = DBContestInfo(0)) == null)
	ForceLoad("../index.php");//index.php

if (isset($_GET["delete"]) && is_numeric($_GET["delete"]) && isset($_GET["input"])) {
	$param = array();
	$param['number']=$_GET["delete"];
    //myrawurldecode funcion devuelve decodificado primero decodifica signos de porcentaje url y luego base_64
	$param['inputfilename']=myrawurldecode($_GET["input"]);
    //retorna true si todo esta bien actulizar la tabla problemtable problemfullname=..(DEL)
    //actualizar la tabla runtable a status deleted y guarda una nueva tarea old en tasktable
    //obtenido datos de tabla problemtable y answertable del mismo usuario
	//mejorado
	if(!DBDeleteProblem ($param)) {
		MSGError('Error deleting problem');
		LogError('Error deleting problem');
	}
	ForceLoad("problemset.php");
}

if(isset($_POST['Submit5']) && $_POST['Submit5']=='Descargar') {
	if(isset($_POST['basename']) &&
	   isset($_POST['fullname']) &&
	   isset($_POST['timelimit']) &&
	   $_POST["confirmation"] == "confirm") {
		   if ($_FILES["probleminput"]["name"] != "") {
    			$type=myhtmlspecialchars($_FILES["probleminput"]["type"]);
    			$size=myhtmlspecialchars($_FILES["probleminput"]["size"]);
    			$name=myhtmlspecialchars($_FILES["probleminput"]["name"]);
    			$temp=myhtmlspecialchars($_FILES["probleminput"]["tmp_name"]);
    			if (!is_uploaded_file($temp)) {
    				ob_end_flush();
    				IntrusionNotify("file upload problem.");
    				ForceLoad("../index.php");//index.php
    			}
    		} else $name = "";

            if ($_FILES["problemsol"]["name"] != "") {
    			$type1=myhtmlspecialchars($_FILES["problemsol"]["type"]);
    			$size1=myhtmlspecialchars($_FILES["problemsol"]["size"]);
    			$name1=myhtmlspecialchars($_FILES["problemsol"]["name"]);
    			$temp1=myhtmlspecialchars($_FILES["problemsol"]["tmp_name"]);
    			if (!is_uploaded_file($temp1)) {
    				ob_end_flush();
    				IntrusionNotify("file upload problem.");
    				ForceLoad("../index.php");//index.php
    			}
    		} else $name1 = "";

    		if (isset($_FILES["problemdesc"]) && $_FILES["problemdesc"]["name"] != "") {
    			$type2=myhtmlspecialchars($_FILES["problemdesc"]["type"]);
    			$size2=myhtmlspecialchars($_FILES["problemdesc"]["size"]);
    			$name2=myhtmlspecialchars($_FILES["problemdesc"]["name"]);
    			$temp2=myhtmlspecialchars($_FILES["problemdesc"]["tmp_name"]);
    			if (!is_uploaded_file($temp2)) {
    				ob_end_flush();
    				IntrusionNotify("file upload problem.");
    				ForceLoad("../index.php");//index.php
    			}
    		} else $name2 = "";

    		$ds = DIRECTORY_SEPARATOR;
    		if($ds=="") $ds = "/";
    		$tmpdir = getenv("TMP");//getenv — Obtiene el valor de una variable de entorno
    		if($tmpdir=="") $tmpdir = getenv("TMPDIR");
    		if($tmpdir[0] != $ds) $tmdir = $ds . "tmp";// /tmp
    		if($tmpdir=="") $tmpdir = $ds . "tmp";
    		$locr = $_SESSION["locr"];
            //Crea un fichero con un nombre de fichero único, cuyo permiso de acceso
            //está establecido a 0600, en el directorio especificado. Si el directorio
            //no existe o no es escribible, tempnam() puede generar un fichero en el
            //directorio temporal del sistema, y devolver la ruta completa de este fichero, incluyendo su nombre.
    		$tfile = tempnam($tmpdir, "problem");

    		if(@mkdir($tfile . "_d", 0700)) {
    			$dir = $tfile . "_d";
    			@mkdir($dir . $ds . 'limits');
    			@mkdir($dir . $ds . 'compare');
    			@mkdir($dir . $ds . 'compile');
    			@mkdir($dir . $ds . 'run');
    			@mkdir($dir . $ds . 'input');
    			@mkdir($dir . $ds . 'output');
    			@mkdir($dir . $ds . 'tests');
    			@mkdir($dir . $ds . 'description');

    			$filea = array('compare' . $ds . 'c','compare' . $ds . 'cc','compare' . $ds . 'java','compare' . $ds . 'py2','compare' . $ds . 'py3',
    						   'compile' . $ds . 'c','compile' . $ds . 'cc','compile' . $ds . 'java','compile' . $ds . 'py2','compile' . $ds . 'py3',
    						   'run' . $ds . 'c','run' . $ds . 'cc','run' . $ds . 'java','run' . $ds . 'py2','run' . $ds . 'py3');
    			foreach($filea as $file) {
    				$rfile=$locr . $ds . '..' . $ds . 'doc' . $ds . 'problemexamples' . $ds . 'problemtemplate' . $ds . $file;
    				if(is_readable($rfile)) {
    					@copy($rfile, $dir . $ds . $file);
    				} else {
    					@unlink($tfile);
                        //cleardir($file,false,true); puede ser directorio o archivo
                        //funcion para eliminar el direcctorio junto con los archivos en recursividad
    					cleardir($dir);
    					ob_end_flush();
                        //No se pudo leer el archivo de la plantilla del problema
    					MSGError('Could not read problem template file ' . $rfile);
    					ForceLoad('problemset.php');
    				}
    			}
    			$tl = explode(',',$_POST['timelimit']);
    			if(!isset($tl[1]) || !is_numeric(trim($tl[1]))) $tl[1]='1';

    			$str = "echo " . trim($tl[0]) . "\necho " . trim($tl[1]) . "\necho 512\necho " . floor(10 + $size1 / 512) . "\nexit 0\n";
    			file_put_contents($dir . $ds . 'limits' . $ds . 'c',$str);
    			file_put_contents($dir . $ds . 'limits' . $ds . 'cc',$str);
    			file_put_contents($dir . $ds . 'limits' . $ds . 'java',$str);
                file_put_contents($dir . $ds . 'limits' . $ds . 'py2',$str);
                file_put_contents($dir . $ds . 'limits' . $ds . 'py3',$str);
    			$str = "basename=" . trim($_POST['basename']) . "\nfullname=" . trim($_POST['fullname']);
    			if($name2) {
    				@copy($temp2, $dir . $ds . 'description' . $ds . $name2);
    				@unlink($temp2);
    				$str .= "\ndescfile=" . $name2;
    			}
    			$str .= "\n";
                //Si filename no existe, se crea el fichero. De otro modo, el fichero
                //existente se sobrescribe, a menos que la bandera FILE_APPEND esté establecida.
    			file_put_contents($dir . $ds . 'description' . $ds . 'problem.info',$str);
    			if($name && $name1) {
    				@copy($temp, $dir . $ds . 'input' . $ds . 'file1');
    				@unlink($temp);
    				@copy($temp1, $dir . $ds . 'output' . $ds . 'file1');
    				@unlink($temp1);
    			} else {
    				@unlink($tfile);
    				cleardir($dir);
    				ob_end_flush();
                    //No se pudieron leer los archivos de entrada / salida del problema
    				MSGError('Could not read problem input/output files');
    				ForceLoad('problemset.php');
    			}
                //La función glob() busca todos los nombres de ruta que coinciden con pattern según
                //las reglas usadas por la función glob() de la biblioteca estándar de C, las cuales
                // son similares a las reglas usadas por intérpretes de comandos comunes.
    			$ret=create_zip($dir, glob($dir . $ds . '*'),$dir . '.zip');//funcion para empaquetar a .zip
    			cleardir($dir);
    			if($ret <= 0) {
    				@unlink($tfile);
    				@unlink($dir . '.zip');
    				ob_end_flush();
                    //No se pudo escribir en el archivo zip
    				MSGError('Could not write to zip file');
    				ForceLoad('problemset.php');
    			}
    			$str = file_get_contents($dir . '.zip');
    			@unlink($dir . '.zip');
    			@unlink($tfile);
    			header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
    			header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    			header ("Cache-Control: no-cache, must-revalidate");
    			header ("Pragma: no-cache");
    			header ("Content-transfer-encoding: binary\n");
    			header ("Content-type: application/force-download");
    			header ("Content-Disposition: attachment; filename=" . basename($dir . '.zip'));
    			ob_end_flush();
    			echo $str;
    			exit;
    		} else {
    			@unlink($tfile);
    			ob_end_flush();
                //No se pudo escribir en el directorio temporal
    			MSGError('Could not write to temporary directory');
    		}
    	}
	ForceLoad('problemset.php');
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
		     ForceLoad("problemset.php");
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
			  document.location='problemset.php';
		  }
      } else {
        document.location='problemset.php';
      }
    }
    function conf3(url) {
		//¿Confirma la SUBELECIÓN del PROBLEMA?Confirm the UNDELETION of the PROBLEM
      if (confirm("Estas seguro de Recuper el Problema?")) {
		  document.location=url;
	  } else {
		  document.location='problemset.php';
	  }
    }
</script>
<!--PARA SUBIR PROBLEMA Y CONSTRUIR-->
<!--para subir problema-->

<?php $n=DBProblemMax(); ?>
<button type="button" class="btn btn-success" data-toggle="modal"data-target="#subproblem"name="problem2_button" onclick="mostrar(<?php echo $n; ?>,'t')">Subir problema</button>
<!--para construir problema-->
<a href="buildproblem.php" class="btn btn-outline-primary">Construir problema</a>

<div class="modal fade" role="dialog" id="subproblem">
    <div class="modal-dialog">
        <div class="modal-content">

			<form name="form_submit" id="form_submit" enctype="multipart/form-data" method="post">
                <div class="modal-header">

					<!--<h3 class="modal-title" id="tituloes" value="Problem Submit"></h3>-->
					<input type="button" style="border: none; outline: none;background-color: transparent !important;" name="tituloes" id="tituloes" class="h3 modal-title" value="">

					<button type="button" class="close" data-dismiss="modal" name="bu">&times;</button>
                </div>

                <div class="modal-body">

				  <input type="hidden"  name="problemnumber" id="problemnumber" value="">
                  <br>
                  <div class="from-group">
                    <label for="problemname">Nombre pequeño(solo una letra):</label>
                    <input type="text" name="problemname" id="problemname" class="form-control" value="" placeholder="Short" maxlength="1">
                  </div>

                  <br>
                  <div class="from-group">
                    <label for="probleminput">Problema packete(zip) file:</label>
                    <input type="file" name="probleminput" id="probleminput" class="form-control" value="">
                  </div>
                  <br>
                  <div class="from-group">
                     <label for="colorname">Nombre Color:</label>
                    <input type="text" name="colorname" id="colorname"class="form-control" value="" placeholder="Color">
                  </div>
                  <br>
                  <div class="from-group">
                    <!-- lista desplegable
                    <button class="from-control dropdown-toggle btn btn-outline-primary" type="button" id="dropdownMenuBoton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Country</button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuBoton">
                                        <a class="dropdown-item" href="#">grover</a>
                                        <a class="dropdown-item" href="#">grover</a>
                                        <a class="dropdown-item" href="#">grover</a>
                                    </div>
                  -->
                  <label for="color">Color(Formato Hexadecimal): <a href="https://htmlcolorcodes.com/es/" target="_blank">Buscar Color</a></label>
                  <input type="text" name="color" id="color"class="form-control" value="" placeholder="">

                  </div>


                </div>

                <div class="modal-footer">

                  <button type="button" class="mx-5 btn btn-danger" data-dismiss="modal" name="cancel">Cancel</button>
                  <button type="button" class="btn btn-success" id="Submit3" name="Submit3">Enviar</button>

				  <!--<input type="submit" class="btn btn-primary" name="Submit3" value="Enviar" onClick="conf()">&nbsp;
				  -->
                </div>
            </form>
        </div>

    </div>
</div>



<form name="form0" enctype="multipart/form-data" method="post" action="problemset.php">
	<table class="table table-bordered table-hover">
	  <thead>
	    <tr>
			<th scope="col">Problema #</th>
		    <th scope="col">Nombre Pequeño</th>
		    <th scope="col">Titulo</th>
		    <th scope="col">Nombre base</th>
		    <th scope="col">Archivo descripcion</th>
		    <th scope="col">Packete zip</th>
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
			  	     echo "  <td> <a href=\"../filedownload.php?" . filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"]) . "\">" .
					 //Dada una cadena que contiene una ruta a un archivo o directorio,
					 // esta función devolverá el último componente de nombre.</a>
					 basename($prob[$i]["descfilename"]) . "</td>\n";

			     }
			     else
			         echo "  <td>&nbsp;</td>\n";

			     if ($prob[$i]["inputoid"] != null) {
			          $tx = $prob[$i]["inputhash"];
			      	  echo "  <td><a href=\"../filedownload.php?" . filedownload($prob[$i]["inputoid"] ,$prob[$i]["inputfilename"]) ."\">" .
			  		          $prob[$i]["inputfilename"] . "</a> " .
			  		         //"<img title=\"hash: $tx\" alt=\"$tx\" width=\"25\" src=\"../images/bigballoontransp-hash.png\" />" .
			                 "</td>\n";
			     }
			     else
			          echo "  <td>&nbsp;</td>\n";

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
					if($prob[$i]["user"]==$_SESSION["usertable"]["usernumber"]){
	  				  	 if(strpos($prob[$i]["fullname"],"(DEL)") !== false) {
							 echo "<div class=\"btn-group btn-group-toggle\" data-toggle=\"buttons\"><a href=\"javascript: conf3('problemset.php?delete=" . $prob[$i]["number"] . "&input=" . myrawurlencode($prob[$i]["inputfilename"]) .
	  				  			  "')\" class=\"btn btn-warning\">Recuperar</a>";
								  echo "<button type=\"button\" onclick=\"mostrar(".$prob[$i]["number"].",'f')\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#subproblem\" name=\"".$prob[$i]["number"]."\" id=\"".$prob[$i]["number"]."\" disabled>Actualizar</button></div>";
	  				  	  } else {
							  echo "<div class=\"btn-group btn-group-toggle\" data-toggle=\"buttons\"><a href=\"javascript: conf2('problemset.php?delete=" . $prob[$i]["number"] . "&input=" . myrawurlencode($prob[$i]["inputfilename"]) .
	  				  			  "')\" class=\"btn btn-danger\">Eliminar</a>";
							  echo "<button type=\"button\" onclick=\"mostrar(".$prob[$i]["number"].",'f')\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#subproblem\" name=\"".$prob[$i]["number"]."\" id=\"".$prob[$i]["number"]."\">Actualizar</button></div>";
						  }
				    }
					  //echo "<a href=\"#\" class=\"btn btn-primary\">Actualizar</a></div>";

					  //<button type="button" class="btn btn-success" data-toggle="modal"data-target="#subproblem"name="problem2_button">Subir problema</button>

					  //echo "<a href=\"?up=".$prob[$i]["number"]."\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#subproblem\" name=\"".$prob[$i]["number"]."\" id=\"".$prob[$i]["number"]."\">Actulizar</a></div>";
			          //echo "<input type=\"text\" name=\"colorname" . $prob[$i]['number'] . "\" value=\"" . $prob[$i]["colorname"] . "\" size=\"10\" maxlength=\"100\" />";
			          //echo "<input type=\"text\" name=\"color" . $prob[$i]['number'] . "\" value=\"" . $prob[$i]["color"]. "\" size=\"6\" maxlength=\"6\" />";
			          //echo "<input type=\"submit\" name=\"SubmitProblem" . $prob[$i]["number"] . "\" value=\"Update\">";

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
<script>
function mostrar(id,insert) {
    $(document).ready(function () {
		if(insert=='t'){
			$('#tituloes').val("Subir Problema");
			$('#problemnumber').val(id);
			$("#problemnumber").prop('readonly', false);
		}else{
			$('#tituloes').val("Actulizar Problema");
			$('#problemnumber').val(id);
			$("#problemnumber").prop('readonly', true);
		}

    });
}
$(document).ready(function(){
	 //para subir problema

	$('#Submit3').click(function(){

		 var problemnumber = $('#problemnumber').val();
		 var problemname = $('#problemname').val();
		 var probleminput = String($('#probleminput').val());
		 var colorname = $('#colorname').val();
		 var color = $('#color').val();


		 if(problemnumber != '' && problemname != '' && probleminput.length > 1 && colorname != '' && color != ''){

			 //crea un nuevo objet de stipo FormData

			 var formdata= new FormData($("#form_submit")[0]);
			 //alert(formdata);
			 $.ajax({
				  data: formdata,
				  url:"../include/i_problem.php",
				  type:"POST",
				  contentType: false,
				  processData: false,

				  success:function(data)
				  {
					  if(data == "Yes"){
						  alert(".:YES:.");
						  $('#subproblem').hide();
						  location.reload();
					  }else {
					  	   if(data == "No"){
							   alert("Error al subir");
							   $('#subproblem').hide();
							   location.reload();
						   }else {
						   	   alert(data);
							   $('#subproblem').hide();
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


	});
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
