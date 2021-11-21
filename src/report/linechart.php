<?php

ob_start();
header ("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
session_start();
$locr = $_SESSION['locr'];
$loc = $_SESSION['loc'];

require_once($locr . "/libchart/libchart.php");
header ("Content-type: image/png");
ob_end_flush();

$v = explode(chr(1),rawurldecode($_GET['dados']),100);

$chart = new VerticalChart(1000, 300);

$chart->setUpperBound($v[1]);

for($i=2;$i<count($v); $i+=2)
  $chart->addPoint(new Point($v[$i], $v[$i+1]));

$chart->setTitle($v[0]);
$chart->setLogo($locr. "/images/poweredbyboca.png");
$chart->render();
?>
