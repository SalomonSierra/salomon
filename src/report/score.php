<?php

require('header.php');

$final = true;
$s = $st;
$des = true;
$detail=true;
$ver=false;
if($_GET["p"] == "0") $ver = false;
else if($_GET["p"] == "2") $detail=false;
else {
  $ver = true;
  $des = false;
}
if(isset($_GET["hor"])) $hor = $_GET["hor"];
else $hor = -1;

if ($s["currenttime"] >= $s["contestlastmilescore"] && $ver) {
	$togo = (int) (($s['contestduration'] - $s["contestlastmilescore"])/60);
	echo"<br /><center><h2>Scoreboard (a partir de $togo minutos para el final)</h2></center>\n";
} else
	echo"<br /><center><h2>Final Scoreboard</h2></center>\n";

require("$locr/scoretable.php");
?>
<!--PIE DE PAGINA......PAGINA........PAGINA-->
    <?php require("$locr/footnote.php"); ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

</body>
</html>
