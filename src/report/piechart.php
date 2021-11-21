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
header("Content-type: image/png");
ob_end_flush();

$v = explode(chr(1),rawurldecode($_GET['dados']),100);
$cor = null;
if(isset($_GET['color']))
  $cor = explode("-",rawurldecode($_GET['color']),100);

if(count($v)/2 > 8)
  $chart = new PieChart(450, 300);
else
  $chart = new PieChart(400, 250);

if(isset($_GET['order'])) $chart->order=true;
else $chart->order=false;

$tot=0;
for($i=1;$i<count($v); $i+=2) {
	$tot += $v[$i+1];
}
for($i=1;$i<count($v); $i+=2) {
  $color = null;
  if($cor != null) {
    $r = hexdec( substr($cor[($i-1)/2], 0, 2) );
    $g = hexdec( substr($cor[($i-1)/2], 2, 2) );
    $b = hexdec( substr($cor[($i-1)/2], 4, 2) );
    $color = array($r, $g, $b);
  }
  if($v[$i+1] > $tot/100)
	  $chart->addPoint(new Point($v[$i], $v[$i+1], $color));
  else
	  $chart->addPoint(new Point($v[$i], $tot/100, $color));
}

$chart->setTitle($v[0]);
$chart->setLogo($locr. "/images/vjudge.png");
$chart->render();

?>
