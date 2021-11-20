<?php

require('header.php');

?>
<br>
<ol class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="index.php">Inicio</a>
	</li>
	<li class="breadcrumb-item active">Problemas</li>
</ol>
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
    echo "  <td><a href=\"#\" class=\"btn btn-primary\" style=\"font-weight:bold\" onClick=\"window.open('filewindow2.php?".filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"])."', 'Ver - PROBLEMA', 'width=680,height=600,scrollbars=yes,resizable=yes')\">Ver Problema</a></td>\n";
  }
  else
    echo "  <td>no description file available</td>\n";
  echo "  <td>" . $prun["ac"] . "</td>\n";

  if (isset($prob[$i]["descoid"]) && $prob[$i]["descoid"] != null && isset($prob[$i]["descfilename"])) {
    //echo "  <td><a href=\"../filedownload.php?" . filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"]) .
	//	"\">" . basename($prob[$i]["descfilename"]) . "</a>&nbsp;&nbsp;<a href=\"#\" class=\"btn btn-primary\" style=\"font-weight:bold\" onClick=\"window.open('../filewindow0.php?".filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"])."', 'Ver - PROBLEMA', 'width=680,height=600,scrollbars=yes,resizable=yes')\">Ver Problema</a></td>\n";
		echo "  <td>".$prun["all"]."&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<button type=\"button\" class=\"btn btn-success\" data-toggle=\"modal\"data-target=\"#loginModal\"name=\"problem2_button\">Enviar</button>";
		echo " </td>\n";
  }
  else
    echo "  <td>".$prun["all"]."</td>\n";

  echo " </tr>\n";

}
echo "</tbody></table>";
if (count($prob) == 0) echo "<br><center><b><font color=\"#ff0000\">NO HAY PROBLEMAS DISPONIBLES TODAVIA</font></b></center>";

?>





<!--PIE DE PAGINA......PAGINA........PAGINA-->
		<br>
		<font size="-5">Desarrollado por FabianS7</font>



		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

	    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

	</body>
</html>

<script language="JavaScript" src="sha256.js"></script>
<script language="JavaScript" src="hex.js"></script>
<?php include 'formvalid.php';?>
