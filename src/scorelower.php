<?php

require_once("globals.php");
//funcion retorna true o false si no existe usertable en session false si es id diferente false
//si aun no inicio session FALSE
//de usuario es true su multi llogion  TRUE
//si el ip son diferentes FALSE
if(!ValidSession()) {
    ///funcion para expirar el session y registar 3= debug en logtable
        InvalidSession("scorelower.php");
        ForceLoad("index.php");//index.php
}
//retorna el resultado de la consulta del sitio tambien con algunas

if (($s = DBContestClockInfo($_SESSION["usertable"]["contestnumber"])) == null)
  ForceLoad("../index.php");//index.php

if ($_SESSION["usertable"]["usertype"]!="coach" &&
    $_SESSION["usertable"]["usertype"]!="admin") $ver=true;
else $ver=false;
if($_SESSION["usertable"]["usertype"]=="score") $des=false;
else $des=true;

// temp do carlinhos (placar de judge == placar de time)
//if ($_SESSION["usertable"]["usertype"]=="judge") $ver = true;

if ($s["currenttime"] >= $s["contestlastmilescore"] && $ver)
	echo "<br><center>Marcador congelado</center>";

require('scoretable.php');
?>

<!--PIE DE PAGINA......PAGINA........PAGINA-->
	<?php include 'footnote.php'; ?>



	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</body>
</html>
<script language="JavaScript" src="../sha256.js"></script>
<script language="JavaScript" src="../hex.js"></script>
<?php include '../updateform.php';?>
