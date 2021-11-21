<?php

require('header.php');
?>
<br>
<center><h2>Lista de Ejecuciones</h2></center>
<table class="table table-hover table-bordered table-sm">
    <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">User</th>
      <th scope="col">Time</th>
      <th scope="col">Problem</th>
      <th scope="col">Language</th>
      <th scope="col">Filename</th>
      <th scope="col">Status</th>
      <th scope="col">Judge</th>
      <th scope="col">Answer</th>
    </tr>
  </thead>
  <tbody>

<?php
$s = $st;
// forca aparecer as runs do proprio contest
if (trim($s["contestjudging"])!="") $s["contestjudging"].=",".$_SESSION["usertable"]["usernumber"];
else $s["contestjudging"]=$_SESSION["usertable"]["usernumber"];

$run = DBAllRunsInContest($_SESSION["usertable"]["contestnumber"], 'report');

for ($i=0; $i<count($run); $i++) {
  echo " <tr>\n";
  echo "  <td>" . $run[$i]["number"] . "</td>\n";
  if ($run[$i]["user"] != "") {
	$u = DBUserInfo ($run[$i]["user"]);
	echo "  <td>" . $u["userfullname"] . "</td>\n";
  }
  echo "  <td>" . dateconvminutes($run[$i]["timestamp"]) . "</td>\n";

  if($run[$i]["status"] == "deleted") {
    echo "<td>&nbsp;</td>\n";
    echo "<td>&nbsp;</td>\n";
    echo "<td>&nbsp;</td>\n";
    echo "  <td>" . $run[$i]["status"] . "</td>\n";
    if ($run[$i]["judge"] != "") {
	$u = DBUserInfo ($run[$i]["judge"]);
	echo "  <td>" . $u["username"] . " (" . $run[$i]["judge"] . ")</td>\n";
    } else
	echo "  <td>&nbsp;</td>\n";
    echo "<td>&nbsp;</td>\n";
    echo "</tr>";
    continue;
  }

  echo "  <td nowrap>" . $run[$i]["problem"];
  if($run[$i]["colorname"] != "")
    echo "(".$run[$i]["colorname"].")";
  echo "</td>\n";
  echo "  <td>" . $run[$i]["language"] . "</td>\n";
  echo "  <td>" . $run[$i]["filename"] . "</td>\n";

  echo "  <td>" . $run[$i]["status"] . "</td>\n";
  if ($run[$i]["judge"] != "") {
	$u = DBUserInfo ($run[$i]["judge"]);
	echo "  <td>" . $u["username"] . " (" . $run[$i]["judge"] . ")</td>\n";
  } else
	echo "  <td>&nbsp;</td>\n";

  if ($run[$i]["answer"] == "") $run[$i]["answer"] = "&nbsp;";
  echo "  <td>" . $run[$i]["answer"] . "</td>\n";
  echo " </tr>\n";
}

echo "</tbody></table>";
if (count($run) == 0) echo "<br><center><b><font color=\"#ff0000\">NO RUNS AVAILABLE</font></b></center>";

include("$locr/footnote.php");
?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

</body>
</html>
