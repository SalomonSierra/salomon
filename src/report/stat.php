<?php

require('header.php');

$d = DBRunReport($_SESSION["usertable"]["contestnumber"]);

echo "<div class=\"bg-secondary text-white\"><center><h2>Estadística</h2></center></div>\n";

//----------------------------------------------------------
echo "<center><h3>Ejecuciones por Problema</h3></center>\n";
echo "<center><table style=\"width: 50%;\"class=\"table table-hover table-bordered table-sm\">\n";
echo "<tr><td><b><u>Problemas</u></b></td>";

echo "<td>Total</td><td>Aceptado</td>";
echo "</tr>\n";

$str="Todas las Ejecuciones por Problema";
$str2="Ejecuciones aceptadas por problema";
reset($d['problem']);
$cor = "";
//$d["problem"]=array('key'=>'fabian');
while (list($keya, $val) = each($d['problem'])) {

  if(isset($d['problemyes'][$keya])) {
      $val = $d['problemyes'][$keya]; if($val=="") $val=0;
      $cor .= "-" . $d['color'][$keya];
  }
  $str2 .= chr(1) . $keya . "(" . $val . ")" . chr(1) . $val;

}

$cor = substr($cor,1);

reset($d['problem']);
while (list($keya, $val) = each($d['problem'])) {
    if(isset($d['problem'][$keya])){
  $str .= chr(1) . $keya . "(" . $val . ")" . chr(1) . $val;
  echo "<tr><td>$keya ";
  if(isset($d['color'][$keya])){
  echo "<img alt=\"balloon\" width=\"15\" ".

	  "src=\"" . balloonurl($d['color'][$keya]) ."\" />\n";
  }
  echo "</td>";
  echo "<td>$val</td>";
  if(isset($d['problemyes'][$keya])) {
    echo "<td nowrap>".$d['problemyes'][$keya];
    if($val != 0) {
      $p = round(100*$d['problemyes'][$keya] / $val);
      echo " (".$p."%)";
    }
    echo "</td>";

  }
  else
    echo "<td nowrap>0 (0%)</td>";
  echo "</tr>";
    }
}
echo "</table></center>";

echo "<center><table><tr>";
echo "<td><img alt=\"\" src=\"piechart.php?dados=".rawurlencode($str)."&color=".rawurlencode($cor)."\" /></td>\n";
echo "<td><img alt=\"\" src=\"piechart.php?dados=".rawurlencode($str2)."&color=".rawurlencode($cor)."\" /></td></tr></table></center>\n";

//----------------------------------------------------------
echo "<center><h3>Ejecuciones por Problema y Respuesta</h3></center>\n";
echo "<center><table style=\"width: 50%;\"class=\"table table-hover table-bordered table-sm\">\n";
echo "<tr><td><b><u>Problemas x Respuestas</u></b></td>";
reset($d['answer']);
while (list($key, $val) = each($d['answer']))
  echo "<td>$key</td>";
echo "<td>Total</td></tr>\n";

reset($d['problem']);
while (list($keya, $vala) = each($d['problem'])) {
  echo "<tr><td>$keya ";
  echo "<img alt=\"balloon\" width=\"15\" ".
	  "src=\"" . balloonurl($d['color'][$keya]) ."\" />\n";
  echo "</td>";
  reset($d['answer']);
  while (list($key, $val) = each($d['answer'])) {
    if(!isset($d['pa'][$keya][$key]))
	echo "<td>0</td>";
    else {
        $p = round(100*$d['pa'][$keya][$key] / $vala);
        echo "<td nowrap>".$d['pa'][$keya][$key]." (".$p."%)</td>";
    }
  }
  echo "<td>$vala</td>";
  echo "</tr>";
}
echo "</table></center>";

//----------------------------------------------------------
echo "<center><h3>Ejecuciones por Problema y Lenguaje</h3></center>\n";
echo "<center><table style=\"width: 50%;\"class=\"table table-hover table-bordered table-sm\">\n";
echo "<tr><td><b><u>Problemas x Lenguajes</u></b></td>";
reset($d['language']);
while (list($key, $val) = each($d['language']))
  echo "<td>$key</td>";
echo "<td>Total</td></tr>\n";

reset($d['problem']);
while (list($keya, $vala) = each($d['problem'])) {
  echo "<tr><td>$keya ";
  echo "<img alt=\"balloon\" width=\"15\" ".
	  "src=\"" . balloonurl($d['color'][$keya]) ."\" />\n";
  echo "</td>";
  reset($d['language']);
  while (list($key, $val) = each($d['language'])) {
    if(!isset($d['pl'][$keya][$key]))
	echo "<td>0</td>";
    else {
        $p = round(100*$d['pl'][$keya][$key] / $vala);
        echo "<td nowrap>".$d['pl'][$keya][$key]." (".$p."%)</td>";
    }
  }
  echo "<td>$vala</td>";
  echo "</tr>";
}
echo "</table></center>";

//----------------------------------------------------------
echo "<br />";
echo "<hr />";
echo "<center><h3>Ejecuciones por Lenguaje</h3></center>\n";
echo "<center><table style=\"width: 50%;\"class=\"table table-hover table-bordered table-sm\">\n";
echo "<tr><td><b><u>Lenguajes</u></b></td>";

echo "<td>Total</td><td>Aceptado</td>";
echo "</tr>\n";

$str="Todas las Ejecuciones por Lenguaje";
$str2="Ejecuciones Aceptadas por Lenguaje";
reset($d['language']);
while (list($keya, $val) = each($d['language'])) {

    if(isset($d['languageyes'][$keya])) {
        $val = $d['languageyes'][$keya]; if($val=="") $val=0;
        $str2 .= chr(1) . $keya . "(" . $val . ")" . chr(1) . $val;
  }

}

reset($d['language']);
while (list($keya, $val) = each($d['language'])) {
  $str .= chr(1) . $keya . "(" . $val . ")" . chr(1) . $val;
  echo "<tr><td>$keya</td>";
  echo "<td>$val</td>";
  if(isset($d['languageyes'][$keya])) {
    $p = round(100*$d['languageyes'][$keya] / $val);
    echo "<td nowrap>".$d['languageyes'][$keya]." (".$p."%)</td>";
  }
  else
    echo "<td nowrap>0 (0%)</td>";
  echo "</tr>";
}
echo "</table></center>";

echo "<center><table><tr>";
echo "<td><img alt=\"\" src=\"piechart.php?dados=".rawurlencode($str)."\" /></td>\n";
echo "<td><img alt=\"\" src=\"piechart.php?dados=".rawurlencode($str2)."\" /></td></tr></table></center>\n";

//----------------------------------------------------------
echo "<center><h3>Ejecuciones por Lenguaje y Respuesta</h3></center>\n";
echo "<center><table style=\"width: 50%;\"class=\"table table-hover table-bordered table-sm\"\n";
echo "<tr><td><b><u>Lenguajes x Respuestas</u></b></td>";
reset($d['answer']);
while (list($key, $val) = each($d['answer']))
  echo "<td>$key</td>";
echo "<td>Total</td></tr>\n";

reset($d['language']);
while (list($keya, $vala) = each($d['language'])) {
  echo "<tr><td>$keya</td>";
  reset($d['answer']);
  while (list($key, $val) = each($d['answer'])) {
    if(!isset($d['la'][$keya][$key]))
	echo "<td>0</td>";
    else {
        $p = round(100*$d['la'][$keya][$key] / $vala);
        echo "<td nowrap>".$d['la'][$keya][$key]." (".$p."%)</td>";
    }
  }
  echo "<td>$vala</td>";
  echo "</tr>";
}
echo "</table></center>";

//----------------------------------------------------------
echo "<br />";
echo "<hr />";
echo "<center><h3>Ejecuciones por Respuesta</h3></center>\n";

echo "<center><table><tr>";
echo "<td>";

echo "<center><table class=\"table table-hover table-bordered table-sm\">\n";
echo "<tr><td><b><u>Respuestas</u></b></td>";

echo "<td>Respuestas</td>";
echo "</tr>\n";

$str="Todas las Ejecuciones por Respuesta";
reset($d['answer']);
while (list($keya, $val) = each($d['answer'])) {
  $str .= chr(1) . $keya . "(" . $val . ")" . chr(1) . $val;
  echo "<tr><td>$keya</td>";
  echo "<td>$val</td>";
  echo "</tr>";
}
echo "</table></center>";

echo "</td>";
echo "<td><img alt=\"\" src=\"piechart.php?order=1&dados=".rawurlencode($str)."\" /></td></tr></table></center>\n";

//----------------------------------------------------------
echo "<br />";
echo "<hr />";
echo "<center><h3>Ejecuciones por Usuario y Problema</h3></center>\n";
echo "<center><table style=\"width: 50%;\"class=\"table table-hover table-bordered table-sm\">\n";
echo "<tr><td><b><u>Usuarios x Problemas</u></b></td>";
reset($d['problem']);
while (list($key, $val) = each($d['problem'])) {
  echo "<td>$key ";
  echo "<img alt=\"balloon\" width=\"15\" ".
	  "src=\"" . balloonurl($d['color'][$key]) ."\" />\n";
  echo "</td>";
}
echo "<td>Total</td><td>Aceptado</td></tr>\n";

reset($d['username']);
while (list($keya, $vala) = each($d['username'])) {
  $keya = $d['username'][$keya];
  if(isset($d['user'][$keya]))
	  $vala = $d['user'][$keya];
  else $vala=0;
  echo "<tr><td>".$d['userfull'][$keya]."</td>";
  reset($d['problem']);
  while (list($key, $val) = each($d['problem'])) {
    if(!isset($d['up'][$keya][$key]))
	echo "<td bgcolor=\"ffff88\">0</td>";
    else {
	$q = $d['up'][$keya][$key];
	$color = "ff5555";
        if($q < 0) {
		$q = - $q;
		$color = "22ee22";
	}
        echo "<td nowrap bgcolor=\"$color\">".$q;
	if($vala != 0) {
          $p = round(100*$q / $vala);
          echo " (".$p."%)";
	}
	echo "</td>";
    }
  }
  if($vala != "")
    echo "<td>$vala</td>";
  else
    echo "<td>0</td>";
  if(isset($d['useryes'][$keya])) {
    if($vala != 0) {
      $p = round(100*$d['useryes'][$keya] / $vala);
      echo "<td nowrap>".$d['useryes'][$keya]." (".$p."%)</td>";
    } else
      echo "<td>".$d['useryes'][$keya]."</td>";
  } else
    echo "<td>0</td>";

  echo "</tr>";
}
echo "</table></center>";

//----------------------------------------------------------
echo "<br />";
echo "<hr />";
echo "<center><h3>Ejecuciones por Período de Tiempo</h3></center>\n";

$vezes = 30;
$passo = $st['contestduration']/$vezes;
$atual = 0;
$pos = 0;
$res = array();
$m = 0;
sort($d['timestamp']);
reset($d['timestamp']);

while (list($keya, $val) = each($d['timestamp'])) {
  while($atual+$passo < $val) {
    $atual += $passo;
    $pos++;
  }
  if(isset($res[$pos]))
	  $res[$pos]++;
  else $res[$pos]=1;
  if($res[$pos] > $m) $m=$res[$pos];
}

$str="Ejecuciones por Período de Tiempo" . chr(1) . $m;
$atual=0;
for($pos=0; $pos<$vezes; $pos++) {
  if(!isset($res[$pos]) || $res[$pos]=="") $res[$pos] = 0;
  $q = (int) ($atual/60);
  $atual += $passo;
  $qq = (int) ($atual/60);
  $str .= chr(1) . $q . "-" .$qq . chr(1) . $res[$pos];
}

echo "<center><img alt=\"\" src=\"linechart.php?dados=".rawurlencode($str)."\" /></center>\n";

//------------------------------------------------
$vezes = 30;
$passo = $st['contestduration']/$vezes;
$atual = 0;
$pos = 0;
$res = array();
sort($d['timestampyes']);
reset($d['timestampyes']);
while (list($keya, $val) = each($d['timestampyes'])) {
  while($atual+$passo < $val) {
    $atual += $passo;
    $pos++;
  }
  if(isset($res[$pos]))
	  $res[$pos]++;
  else $res[$pos]=1;
}

$str="Ejecuciones Aceptadas por Período de Tiempo" . chr(1) . $m;
$atual=0;
for($pos=0; $pos<$vezes; $pos++) {
  if(!isset($res[$pos]) || $res[$pos]=="") $res[$pos] = 0;
  $q = (int) ($atual/60);
  $atual += $passo;
  $qq = (int) ($atual/60);
  $str .= chr(1) . $q . "-" .$qq . chr(1) . $res[$pos];
}

echo "<center><img alt=\"\" src=\"linechart.php?dados=".rawurlencode($str)."\" /></center>\n";

include("$locr/footnote.php");
?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

</body>
</html>
