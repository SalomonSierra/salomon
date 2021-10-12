
<?php
//prueba de email
ini_set('display_errors',1);
error_reporting( E_ALL );
$from ="pruebas@salomon.com";
$to="queviva777vivaque@gmail.com";
$subject="Prueba de envio de email con PHP";
$mensaje="Esto es un email de prueba enviado con PHP";
$header="From:".$from;
$me=mail($to,$subject,$mensaje,$header);
echo $me."que";
echo "enviado";











//http://repositorio.utp.edu.co/dspace/bitstream/handle/11059/4131/0058J61.PDF?sequence=1&isAllowed=y
//otro proyecto.
//include 'db.php';
//include 'globals.php';
//echo "---------\n";

//$c=DBConnect();

//pg_query($c, "begin work");

//$lo = DB_lo_open ($c, 19352, "r");
//$str=DB_lo_read_tobrowser(6,$lo,$c);
//echo "FAIBAN";
//DB_lo_close($lo);
//pg_query($c, "commit work");

//DBClose($c);

//$cad="fabian sierra acarapi";
//$sub="rra acad";
//$pos = strpos($cad, $sub);

//if ($pos !== false) {
//  echo "si se encotro";
//}else{
//    echo "no se encotro";
//}
//include_once('/private/conf.php');
// el numero de la competencia y true
//funcion para obtener todos los problemas con sus datos y creando la archivos si no existe con los datos




//echo time()."<br>";
//echo microtime(true)."<br>";
//echo myunique(1);
// $a['userdesc']="[unsxx][bo,Local]soy programdor";
// // $a['userdesc']="[unsxx]soy programdor";
// //$a['userdesc']="soy programdor";
// $r=cleanuserdesc($a);
//
// echo $r['userdesc']."<br>";
// echo $r['usershortinstitution']."<br>";
// echo $r['userflag']."<br>";
// echo $r['usersitename']."<br>";

//$h=3600*2;
//$r=time()+$h;
//$t=$r-time();
//echo dateconv(time())." ahorita<br>";
//echo dateconv($r)." iniciar<br>";
//echo dateconv($t);
//echo " en segundos dife. $t";


/*@mkdir("/var/www/salomon/src/private/problemtmp/ff",0770,true);
$database = DBConnect();
   pg_query($database, "begin");
   $oid=18037;
   $file="/var/www/salomon/src/private/problemtmp/ff/tmp.zip";
 //  $oid = pg_lo_import($database, '/var/ww/salomon/src/ejemplos.php');
 //  pg_query($database, "commit");
 //  echo $oid;//18038*/
//$stat= pg_lo_export ($oid, $file, $database);
/*$stat=DB_lo_export(1, $database, $oid, $file);
pg_query($database, "commit");
if($stat===false)
    echo "nooooo.$stat";
else {
    echo "yes.$stat";
}
if(is_readable("/var/www/salomon/src/private/problemtmp/ff/tmp.zip"))
    echo "yes";
else
    echo "no";*/
//hexdec( substr($get_color, 2, 2) );
//hexdec — Hexadecimal a decimal

//$compressed = gzcompress('Compress me', 9);
//echo $compressed;
//echo LOCK_EX;
//echo LOCK_SH;
//echo LOCK_UN;
//echo pg_escape_string("fabian +}+*sierra \acarapi");
//$str="fabiansierraasdfasdf";
//echo substr($str,7);
//echo tempnam("/home/fabians7", "FABIANS");
//echo substr($str,3,0);
//echo "&nbsp;";//espacios en blanco
/*$param['contest']=0;
$param['fabian']='grover';
$param['sin']=1;
if(!isset($param[0])){
    $tmp = $param;
    $param=array();
    $param[0]= $tmp;
}
for ($i=0; isset($param[$i]) ; $i++) {
    // code...
    echo $i;
}*/
//echo $param[]

/*$isadmin=false;
if($isadmin)
    echo "yes";
else {
    echo "no";
}
$isadmin="";
if($isadmin)
    echo "yes";
else {
    echo "nonn";
}

echo "<br>";
$r=2;
$r -= 5;
echo $r."<br>";
$r = - 5;
echo $r;
echo "-------------------------<br>";
foreach (glob("/var/www/salomon/src/*") as $file) {
    echo $file."<br>";
}
echo "<br>";
echo str_repeat("o",2);*/
/*
echo LOG_CRIT." ";//2
echo LOG_ERR." ";//3
echo LOG_WARNING." ";//4
echo LOG_INFO." ";//6
echo LOG_DEBUG." ";//7
echo "<br>";
$facilities = array(
    LOG_AUTH,
    LOG_AUTHPRIV,
    LOG_CRON,
    LOG_DAEMON,
    LOG_KERN,
    LOG_LOCAL0,
    LOG_LPR,
    LOG_MAIL,
    LOG_NEWS,
    LOG_SYSLOG,
    LOG_USER,
    LOG_UUCP,
);

for ($i = 0; $i < 10000; $i++) {
    foreach ($facilities as $facility) {
        openlog('test', LOG_PID, $facility);
        syslog(LOG_ERR, "This is a test: " . memory_get_usage(true));
    }
}

*/

?>
