<?php

require('header2.php');

if(($ct = DBContestInfo($contestnumber)) == null)
	ForceLoad("index.php");

?>
<br>
<table class="table table-bordered table-hover">
	<thead>
		<tr class="d-flex">
			<th class="col-2" scope="col">Nombre</th>
			<th class="col-2" scope="col">Nombre Base</th>
			<th class="col-5" scope="col">Titulo</th>
			<th class="col-3" scope="col">Archivo Descripcion</th>
		</tr>
	</thead>
	<tbody>

<?php
$prob = DBGetProblems($contestnumber);
for ($i=0; $i<count($prob); $i++) {
  echo " <tr class=\"d-flex\">\n";
//  echo "  <td nowrap>" . $prob[$i]["number"] . "</td>\n";
  echo "  <td class=\"col-2\">" . $prob[$i]["problem"];
  if($prob[$i]["color"] != "")
          echo " <img alt=\"".$prob[$i]["colorname"]."\" width=\"20\" ".
			  "src=\"" . balloonurl($prob[$i]["color"]) ."\" />\n";
  echo "</td>\n";
  echo "  <td class=\"col-2\">" . $prob[$i]["basefilename"] . "&nbsp;</td>\n";
  //$fabian="fabian";
  echo "  <td class=\"col-5\">" . $prob[$i]["fullname"] . "&nbsp;</td>\n";
  if (isset($prob[$i]["descoid"]) && $prob[$i]["descoid"] != null && isset($prob[$i]["descfilename"])) {
    echo "  <td class=\"col-3\"><a href=\"../filedownload.php?" . filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"]) .
		"\">" . basename($prob[$i]["descfilename"]) . "</a>&nbsp;&nbsp;<a href=\"#\" class=\"btn btn-primary\" style=\"font-weight:bold\" onClick=\"window.open('../filewindow0.php?".filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"])."', 'Ver - PROBLEMA', 'width=680,height=600,scrollbars=yes,resizable=yes')\">Ver Problema</a></td>\n";
  }
  else
    echo "  <td class=\"col-2\">no description file available</td>\n";
  echo " </tr>\n";
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
<?php include '../updateform.php';?>
