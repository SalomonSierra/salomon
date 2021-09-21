<?php
$ds = DIRECTORY_SEPARATOR;
if($ds=="") $ds = "/";
//es legible
if(is_readable('/etc/salomon.conf')) {
    //Las configuraciones son devueltas como un array asociativo si se tiene éxito, y false si falla.
    $pif=parse_ini_file('/etc/salomon.conf');
    $salomondir = trim($pif['salomondir']) . $ds . 'src';
} else {
    //donde estemos ahorita
    $salomondir = getcwd();
}

if(is_readable($salomondir . $ds . '..' .$ds . 'db.php')) {
    //incluimos los archivos php
    require_once($salomondir . $ds . '..' .$ds . 'db.php');
    require_once($salomondir . $ds . '..' .$ds . 'version.php');
} else {

    if(is_readable($salomondir . $ds . 'db.php')) {
        require_once($salomondir . $ds . 'db.php');
        require_once($salomondir . $ds . 'version.php');
    } else {
        //incapaz de encontrar db.php
        echo "unable to find db.php";
        exit;
    }
}

if (getIP()!="UNKNOWN" || php_sapi_name()!=="cli") exit;
//comprueba si esta ejecutando como root
if(system('test "`id -u`" -eq "0"',$retval)===false || $retval!=0) {
    //Debe ejecutarse como root
    echo "Must be run as root\n";
    exit;
}
//seteamos las variables
ini_set('memory_limit','1200M');
ini_set('output_buffering','off');
ini_set('implicit_flush','on');
@ob_end_flush();
echo "max memory set to " . ini_get('memory_limit'). "\n";

$tmpdir = getenv("TMP");
if($tmpdir=="") $tmpdir = getenv("TMPDIR");
if($tmpdir[0] != '/') $tmdir = "/tmp";
if($tmpdir=="") $tmpdir = "/tmp";

$basdir=$ds;
//mas que todo es para utilizar salomonjail o no
//existe /salomonjail/tmp
if(file_exists($ds . 'salomonjail' . $tmpdir)) {
    //salomonjail/tmp
    $tmpdir=$ds . 'salomonjail' . $tmpdir;
    //salomonjail/
    $basdir=$ds . 'salomonjail' . $ds;
    //salomonjail environment seems to exist - trying to use it
    echo "El entorno salomonjail parece existir, tratando de usarlo\n";
} else {
    //salomonjail no encontrado - tratando de continuar sin usarlo
    echo "salomonjail not found - trying to proceed without using it\n";
}

if($ds=='/') {
    //encuantra ficheros de ese usuario y elimina
    system("find $basdir -user salomonjail -delete >/dev/null 2>/dev/null");
    //encuentra ficheros de ese usuario y elimina
    system("find $basdir -user nobody -delete >/dev/null 2>/dev/null");
    //encuentra de ese usuario y cambian el grupo a root
    system("find $basdir -group users -exec chgrp root '{}' \\; 2>/dev/null");
    //busca el /salomonjail/ directorio con permiso escribible -perm /1002 y guarda en tmp/salomon.writablesdirs.tmp
    system("find $basdir -perm /1002 -type d > /tmp/salomon.writabledirs.tmp 2>/dev/null");
    //asigna permiso de solo lectura al archivo
    system('chmod 400 /tmp/salomon.writabledirs.tmp 2>/dev/null');
}
//umask es para asignar permisos de creacion de archivos de solo escritura grupo y otro usuario
umask(0022);
//para cache /salomonjail/tmp
$cache = $tmpdir . $ds . "salomoncache.d";
// /salomonjail/tmp/salomoncache.d
//cleardir($file,false,true); puede ser directorio o archivo
//funcion para eliminar el direcctorio junto con los archivos en recursividad
cleardir($cache);
//para crear el directorio
@mkdir($cache);
//mt_rand es numeros dandomicos sara vuelta a md5
$key=md5(mt_rand() . rand() . mt_rand());
//funcion de configuracion
$cf = globalconf();
$ip = $cf["ip"];//Local
////funcion para que devuelve la informacion de la competencia activa

//$activecontest=DBGetActiveContest();
//pre dormir 0
$prevsleep=0;
//$dodebug=1;
while(42) {
    //capturamos la informacion de runtable problemtable langtable de una competencia donde autoip de vacio
    //y luego actulizamos la tabla runtable autoip=ip autobegindate=$t, autoenddate=null, autoanswer=null,
    // autostdout=null, autostderr=null
    if(($run = DBGetRunToAutojudging($ip)) === false) {
        if($prevsleep==0)
            echo "Nada que hacer. Estoy Durmiendo...";//Nada que hacer. Dormido...Nothing to do. Sleeping...
        else
            echo ".";
        flush();//flush — Vaciar el búfer de salida del sistema
        // dormir durante 10 segundos
        sleep(10);
        $prevsleep=1;
        continue;//continuar
    }
    if(!isset($dodebug)) {
        //primero no existe $dir ni $name
        if(isset($dir)) cleardir($dir);//eliminar dir
        if(isset($name)) unlink($name);//elimina la variable
    }
    echo "\n";
    flush();//flush — Vaciar el búfer de salida del sistema
    $prevsleep=0;//dormir es igual a cero

    $number=$run["number"];

    $contest=$run["contest"];
    //Eliminar posibles archivos de ejecuciones anteriores
    echo "Removing possible files from previous runs\n";
    //file — Transfiere un fichero completo a un array
    $dirs=file('/tmp/salomon.writabledirs.tmp');
    for($dir=0;$dir<count($dirs);$dir++) {
        $dirn=trim($dirs[$dir]) . $ds;
        if($dirn[0] != '/') continue;
        //elimina directorios ese usuarios
        system("find \"$dirn\" -user salomonjail -delete >/dev/null 2>/dev/null");
        system("find \"$dirn\" -user nobody -delete >/dev/null 2>/dev/null");
    }
    //Ingresando al directorio /salomonjail/tmp
    echo "Entering directory $tmpdir (contest=$contest, run=$number)\n";
    //chdir — Cambia de directorio
    chdir($tmpdir);
    for($i=0; $i<5; $i++) {
        //crea un fichero con un nombre unico prefijo salomon
        $name = tempnam($tmpdir, "salomon");
        $dir = $name . ".d";
        //crea un directorio
        if(@mkdir($dir, 0755)) break;
        @unlink($name);
        @rmdir($dir);
    }
    if($i>=5) {
        //No fue posible crear un directorio temporal único
        echo "It was not possible to create a unique temporary directory\n";
        //Autojugar: no se puede crear el directorio temporal
        LogLevel("Autojudging: no puede crear el directorio temporal (run=$number, contest=$contest)",1);
        ////funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
        DBGiveUpRunAutojudging($contest, $number, $ip, "error: problem creating temp directory");
        continue;
    }
    //cambia de directorio a /salomonjail/tmp/asldkfsalomon.d
    chdir($dir);
    //Usando directorio
    echo "Using directory $dir (contest=$contest, run=$number)\n";

    if($run["sourceoid"]=="" || $run["sourcename"]=="") {
        //Evaluación automática: archivo de origen no definido
        LogLevel("Autojudging: Source file not defined (run=$number, contest=$contest)",1);
        //Archivo fuente no definido
        echo "Source file not defined (contest=$contest, run=$number)\n";
        ////funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
        DBGiveUpRunAutojudging($contest, $number, $ip, "error: source file not defined");
        continue;
    }
    if($run["inputoid"]=="" || $run["inputname"]=="") {
        //Autojuzgar: paquete de problemas no definido
        LogLevel("Autojudging: problem package not defined (run=$number, contest=$contest)",1);
        //Archivo de paquete no definido
        echo "Package file not defined (contest=$contest, run=$number)\n";
        ////funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
        DBGiveUpRunAutojudging($contest, $number, $ip, "error: problem package file not defined");
        continue;
    }

    $c = DBConnect();
    DBExec($c, "begin work", "Autojudging(exporttransaction)");
    //pg_lo_export () toma un objeto grande en una base de datos PostgreSQL y guarda
    //su contenido en un archivo en el sistema de archivos local. /salomonjail/tmp/asldkfsalomon.d
    if(DB_lo_export($c, $run["sourceoid"], $dir . $ds . $run["sourcename"]) === false) {
        //Evaluación automática: no se puede exportar el archivo de origen
        DBExec($c, "rollback work", "Autojudging(rollback-source)");
        LogLevel("Autojudging: Unable to export source file (run=$number, contest=$contest)",1);
        //Error al exportar el archivo de origen
        echo "Error exporting source file ${run["sourcename"]} (contest=$contest, run=$number)\n";
        ////funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
        DBGiveUpRunAutojudging($contest, $number, $ip, "error: unable to export source file");
        DBExec($c, "commit", "Autojudging(exportcommit)");
        continue;
    }
    //elimina los direcectorios problemdatalocal y problemdata
    cleardir($dir . $ds . "problemdatalocal");
    cleardir($dir . $ds . "problemdata");
    ///salomonjail/tmp/salomoncache.d/inputoid.inputname
    if(is_readable($cache . $ds . $run["inputoid"] . "." . $run["inputname"])) {
        echo "FABIAN SIERRA ACARAPI8888888";
        DBExec($c, "commit", "Autojudging(exportcommit)");
        //Obtener el archivo del paquete problemático de la caché local
        //Getting problem package file from local cache
        echo "Obteniedo el archivo del paquete problematico de la cache local: " . $cache . $ds . $run["inputoid"] . "." . $run["inputname"] . "\n";
        //captura el contenido del archivo
        $s = file_get_contents($cache .	$ds . $run["inputoid"]	. "." . $run["inputname"]);
        //crea y escribe en inputname lo que obtiene de el key
        file_put_contents($dir . $ds . $run["inputname"], decryptData($s,$key));
        $basename=$basenames[$run['inputoid']. "." . $run["inputname"]];//basename igual a inputoid inpuname
    } else {//hasta 390
        echo "FABIAN SIERRA ACARAPI7777";
        ///root/icpc-latam-packages/problemname.zip
        //para utilizar paquete de icpc
        //$flocal = '/root/icpc-latam-packages/' . trim($run["problemname"]) . ".zip"; //cassiopc: HARDCODED FOR ICPC 2017
        //if(!is_readable($flocal)) $flocal = '/root/icpc-latam-packages/' . trim($run["problemname"]) . ".ZIP";
        //if(!is_readable($flocal)) $flocal = '';
        //si es diferente de vacio entra al if
        $flocal = '';
        if($flocal != '') {
            //Obteniendo el archivo del paquete problemático de la versión local:
            echo "Getting problem package file from local version: " . $flocal . "\n";
            $zip = new ZipArchive;//nuevo zip
            if ($zip->open($flocal) === true) {
	            $zip->extractTo($dir . $ds . "problemdatalocal");
	            $zip->close();
            } else {
	            DBExec($c, "rollback work", "Autojudging(zipfailed)");
                //No se pudo descomprimir el archivo del paquete; verifique el paquete problemático
	            echo "Failed to unzip the package file -- please check the problem package (maybe it is encrypted?)\n";
                ////funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
	            DBGiveUpRunAutojudging($contest, $number, $ip, "error: problem package file is invalid (1)");
	            cleardir($dir . $ds . "problemdata");
	            continue;
            }
        }
        ///salomonjail/tmp/asldkfsalomon.d/inputname elimina archivo
        @unlink($dir . $ds . $run["inputname"]);
        //Descargando el archivo del paquete problemático de db a:
        //Downloading problem package file from db into: /home/salomonjail/tmp/salomonvjpy3b.d/myproblemG.zip
        echo "Descargando el archivo del paquete problemático de db a: " . $dir . $ds . $run["inputname"] . "\n";
        if(DB_lo_export($c, $run["inputoid"], $dir . $ds . $run["inputname"]) === false) {
            DBExec($c, "rollback work", "Autojudging(rollback-input)");
            //Evaluación automática: no se puede exportar el archivo del paquete problemático
            LogLevel("Autojudging: Unable to export problem package file (run=$number, contest=$contest)",1);
            //Error al exportar el archivo del paquete problemático
            echo "Error exporting problem package file ${run["inputname"]} (contest=$contest, run=$number)\n";
            //////funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
            DBGiveUpRunAutojudging($contest, $number, $ip, "error: unable to export problem package file");
            continue;
        }
        DBExec($c, "commit", "Autojudging(exportcommit)");
        @chmod($dir . $ds . $run["inputname"], 0600);
        @chown($dir . $ds . $run["inputname"],"root");
        //Paquete de problemas obtenido: ejecución de scripts de inicio para obtener límites y otra información
        //Problem package obtained -- running init scripts to obtain limits and other information
        echo "Paquete de problemas obtenido -- ejecucion de scripts de inicio para obtener limites y otra informacion\n";
        $zip = new ZipArchive;
        if ($zip->open($dir . $ds . $run["inputname"]) === true) {
            $zip->extractTo($dir . $ds . "problemdata");//como problemdata
            $zip->close();
        } else {
            //No se pudo descomprimir el archivo del paquete; verifique el paquete problemático (¿tal vez esté encriptado?)
            echo "No se pudo descomprimir el archivo del paquete -- porfavor verifique el paquete problematico(¿tal vez este encriptado?)\n";
            //////funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
            DBGiveUpRunAutojudging($contest, $number, $ip, "error: problem package file is invalid (1)");
            cleardir($dir . $ds . "problemdata");
            continue;
        }
        //parse_ini_file — Analiza un fichero de configuración /salomonjail/tmp/asldkfsalomon.d/problemdatalocal/description/problem.info
        if(($info=@parse_ini_file($dir . $ds . "problemdatalocal" . $ds . "description" . $ds . 'problem.info'))===false) {
            if(($info=@parse_ini_file($dir . $ds . "problemdata" . $ds . "description" . $ds . 'problem.info'))===false) {
                //Falta el contenido del problema (descripción / problema.info): compruebe el paquete del problema
                echo "Falta el contenido del problema (descripcion/problema.info): compruebe el paquete del problema\n";
                ///funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
                DBGiveUpRunAutojudging($contest, $number, $ip, "error: problem package file is invalid (2)");
	            cleardir($dir . $ds . "problemdata");
	            cleardir($dir . $ds . "problemdatalocal");
	            continue;
            }
        } else echo "Informacion del problema obtenida del archivo de paquete local\n";//Información del problema obtenida del archivo de paquete local
        //si existe descfile entra
        if(isset($info['descfile']))
            $descfile=trim(sanitizeFilename($info['descfile']));
        $basename=trim(sanitizeFilename($info['basename']));
        $fullname=trim(sanitizeText($info['fullname']));
        if($basename=='') {
            //Falta el contenido del problema (descripción / problema.info): compruebe el paquete del problema
            echo "Falta el contenido del problema (descripcion/problema.info) -- compruebe el paquete del problema\n";
            ///funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
            DBGiveUpRunAutojudging($contest, $number, $ip, "error: problem package file is invalid (3)");
            cleardir($dir . $ds . "problemdata");
            cleardir($dir . $ds . "problemdatalocal");
            continue;
        }
        //basenames[inputoid.inputname]=basename
        $basenames[$run['inputoid']. "." . $run["inputname"]]=$basename;
        //limits no es un directorio?
        if(!is_dir($dir . $ds . "problemdata" . $ds . "limits")) {
            //Falta contenido del problema (límites): consulte el paquete del problema
            echo "Falta contenido del problema (limites): verifique el paquete del problema\n";
            ///funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
            DBGiveUpRunAutojudging($contest, $number, $ip, "error: problem package file is invalid (4)");
            cleardir($dir . $ds . "problemdata");
            cleardir($dir . $ds . "problemdatalocal");
            continue;
        }
        $pd = 'problemdata';//problemdata
        if(is_dir($dir . $ds . "problemdatalocal" . $ds . "limits")) {
            //Obtención de límites del archivo de paquete local
            echo "Obtaining limits from local package file\n";
            $pd = 'problemdatalocal';
        }
        ///salomonjail/tmp/asldkfsalomon.d/problemdata/limits
        chdir($dir . $ds . $pd . $ds . "limits");
        //limits[basename]=array()
        $limits[$basename]=array();
        $cont=false;
        foreach(glob($dir . $ds . $pd . $ds . "limits" .$ds . '*') as $file) {
            chmod($file,0700);//asignamos permisos
            //escapeshellcmd — Escapar meta-caracteres del intérprete de comandos
            $ex = escapeshellcmd($file);
            $ex .= " >stdout 2>stderr";
            @unlink('stdout');
            @unlink('stderr');
            //Ejecutando INIT SCRIPT ex en ruta ...
            echo "Ejecutando INIT SCRIPT " . $ex . " at " . getcwd() . "\n";
            //ejecuta el comando retval guarda
            if(system($ex, $retval)===false) $retval=-1;
            //0
            if($retval != 0) {
                //Error al ejecutar el script: compruebe el paquete problemático
	            echo "Error al ejecutar el script -- porfavor verifique el paquete problematico\n";
                ///funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
                DBGiveUpRunAutojudging($contest, $number, $ip, "error: problem package file is invalid (5)");
	            $cont=true;
	            break;
            }
            //limits[basename][basenamefile]
            //limits[A][cpp]=[4][10][512][1024][0]
            //basename — Devuelve el último componente de nombre de una ruta
            $limits[$basename][basename($file)] = file('stdout',FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }
        //si fue de exito en foreach entra a if
        if(!$cont) {
            $pd = 'problemdata';
            if(is_dir($dir . $ds . "problemdatalocal" . $ds . "tests")) {
                //Ejecución de scripts de prueba desde el archivo de paquete local
                echo "Running test scripts from local package file\n";
	            $pd = 'problemdatalocal';
            }
            ///salomonjail/tmp/asldkfsalomon.d/problemdata/tests/*
            foreach(glob($dir . $ds . $pd . $ds . "tests" .$ds . '*') as $file) {
	            chdir($dir . $ds . $pd . $ds . "tests");//cambiamos el diretorio aqui
	            chmod($file,0700);//asignamos permiso de ejecucion
	            $ex = escapeshellcmd($file);
	            $ex .= " >stdout 2>stderr";
	            @unlink('stdout');
	            @unlink('stderr');
	            echo "Ejecutando TEST SCRIPT " . $ex . " en " . getcwd() . "\n";
	            if(system($ex, $retval)===false) $retval=-1;
	            if($retval != 0) {
                    //Error al ejecutar el script de prueba: verifique el paquete problemático o su instalación
	                echo "Error al ejecutar el script de prueba: verifique el paquete problemático o su instalación\n";
	                echo "=====stderr======\n";
	                echo file_get_contents('stderr');
	                echo "\n=====stdout======\n";
	                echo file_get_contents('stdout');
	                echo "\n===========\n";
                    ///funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
	                DBGiveUpRunAutojudging($contest, $number, $ip, "error: internal test script failed (" . $file . ")");
	                $cont=true;
	                break;
	            }
            }
        }
        //
        if(is_dir($dir . $ds . "problemdatalocal" . $ds . "output")) {
            //Usando scripts y entradas / salidas del archivo de paquete local
            echo "Using scripts and inputs/outputs from local package file\n";
            @copy($flocal, $dir . $ds . $run["inputname"]);
        }
        ///salomonjail/tmp/asldkfsalomon.d/inputname
        $s = file_get_contents($dir . $ds . $run["inputname"]);
        cleardir($dir . $ds . "problemdata");
        cleardir($dir . $ds . "problemdatalocal");
        if($cont) {
            //Abortar la evaluación debido a problemas en el paquete
            echo "Aborting judging because of issues in the package\n";
            continue;
        }
        ///salomonjail/tmp/salomoncache.d/inputoid.intname es encriptado el
        //contenido problemdata con key aleatorio
        file_put_contents($cache . $ds . $run["inputoid"] . "." . $run["inputname"], encryptData($s,$key));
    }
    //solo para probar el sistema, devolviendo sí a cada envío ...
    // just to test the system, returning yes to every single submission...
    if(false) {
        //este archivo vacío es para probar
        @file_put_contents('/tmp/salomon.empty','this empty file is for testing');
        //siempre si
        ////actulizar la respuesta en runtable en autoended todo y los stderr stdout devuelve true
        //falta
        DBUpdateRunAutojudging($contest, $number, $ip, 'Always yes', '/tmp/salomon.empty', '/tmp/salomon.empty', '/tmp/salomon.empty', 1);
        //Autojuzgar respondió 'Siempre sí'
        echo "Autojudging answered 'Always yes' (contest=$contest, run=$number)\n";
        continue;
    }
    ////limits[A][cpp]=[4][10][512][1024][0]
    //limits[A][cpp][0]
    if(!isset($limits[$basename][$run["extension"]][0]) || !is_numeric($limits[$basename][$run["extension"]][0]) ||
        !isset($limits[$basename][$run["extension"]][1]) || !is_numeric($limits[$basename][$run["extension"]][1]) ||
        !isset($limits[$basename][$run["extension"]][2]) || !is_numeric($limits[$basename][$run["extension"]][2]) ||
        !isset($limits[$basename][$run["extension"]][3]) || !is_numeric($limits[$basename][$run["extension"]][3]) ) {
            //No se pudo encontrar la información de límites adecuada para el problema; consulte el paquete del problema
            echo "Failed to find proper limits information for the problem -- please check the problem package\n";
            DBGiveUpRunAutojudging($contest, $number, $ip, "error: problem package file is invalid (6)");
            continue;
    }

  // COMPILATION
  //# parameters are:
  //# $1 source_file
  //# $2 exe_file (default ../run.exe)
  //# $3 timelimit (optional, limit to run all the repetitions, by default only one repetition)
  //# $4 maximum allowed memory (in MBytes)

    $zip = new ZipArchive;
    //extraemos nuestro problema
    if ($zip->open($dir . $ds . $run["inputname"]) === true) {
        //compile/extension
        $zip->extractTo($dir, array("compile" . $ds . $run["extension"]));
        $zip->close();
    } else {
        //No se pudo descomprimir el archivo del paquete; verifique el paquete problemático
        echo "No se pudo descomprimir el archivo del paquetee -- verifique el paquete problemático\n";
        //////actulizar la respuesta en runtable en autoended todo y los stderr stdout devuelve true
        DBGiveUpRunAutojudging($contest, $number, $ip, "error: problem package file is invalid (7)");
        continue;
    }
    ///salomonjail/tmp/asldkfsalomon.d/compile/cpp
    $script = $dir . $ds . 'compile' . $ds . $run["extension"];
    if(!is_file($script)) {
        //Error (no encontrado) al compilar el script para
        echo "Error (not found) compile script for ".$run["extension"]." -- please check the problem package\n";
        DBGiveUpRunAutojudging($contest, $number, $ip, "error: compile script failed (".$run["extension"].")");
        continue;
    }
    ///salomonjail/tmp/asldkfsalomon.d/
    chdir($dir);
    @unlink('allout');
    system('touch allout');
    @unlink('allerr');
    system('touch allerr');

    chmod($script, 0700);
    //escapeshellarg — Escapar una cadena a ser usada como argumento del intérprete de comandos
    $ex = escapeshellcmd($script) ." ".
        escapeshellarg($run["sourcename"])." ".
        escapeshellarg($basename) . " ".
        escapeshellarg(trim($limits[$basename][$run["extension"]][0]))." ".
        escapeshellarg(trim($limits[$basename][$run["extension"]][2]));
    $ex .= " >stdout 2>stderr";
    @unlink('stdout');
    @unlink('stderr');
    ///para $team
    @unlink('fabians7');
    //ejecutando codigo en ruta..
    echo "Ejecutando " . $ex . " at " . getcwd() . "\n";
    if(system($ex, $retval)===false) $retval=-1;

    if(is_readable('stdout')) {
        system('/bin/echo ##### COMPILATION STDOUT: >> allerr');
        system('/bin/cat stdout >> allerr');
        system('/bin/cat stdout > fabians7');//para team
    }
    if(is_readable('stderr')) {
        system('/bin/echo ##### COMPILATION STDERR: >> allerr');
        system('/bin/cat stderr >> allerr');
    }
    //si no fallo en la ejecucion por else
    if($retval != 0) {
        //funcion para devolver el msg de error y retval correspondiente
        list($retval,$answer) = exitmsg($retval);
        //(MIENTRAS SE COMPILA)
        $answer = "(WHILE COMPILING) " . $answer;
    } else {
        system('/bin/echo ####fabianss: >>fabians7');
        //# parameters are:
        //# $1 exe_file
        //# $2 input_file
        //# $3 timelimit (limit to run all the repetitions, by default only one repetition)
        //# $4 number_of_repetitions_to_run (optional, can be used for better tuning the timelimit)
        //# $5 maximum allowed memory (in MBytes)
        //# $6 maximum allowed output size (in KBytes)
        //para $team
        @unlink('fabians7');
        system('touch fabians7');//creamos el archivo
        $zip = new ZipArchive;
        $inputlist = array();
        $ninputlist = 0;
        $outputlist = array();
        $noutputlist = 0;
        if ($zip->open($dir . $ds . $run["inputname"]) === true) {
            for($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                //dirname — Devuelve la ruta de un directorio padre
                $pos = strrpos(dirname($filename),"input");
                if($pos !== false && $pos==strlen(dirname($filename))-5) {
                    //inputlist[ninputlist]=input/filenamebase
                    //inputlist[noutputlist]=output/basename,link
                    $inputlist[$ninputlist++] = 'input' . $ds . basename($filename);
                    $outputlist[$noutputlist++] = 'output' . $ds . basename($filename,'.link');
                }
            }
            if($ninputlist == 0) {
                //ADVERTENCIA: NO hay archivos de entrada en el paquete ZIP. ¿Debería verificar el paquete problemático?
                echo "WARN: There are NO input files in ZIP package -- should check the problem package?\n";
                //actulizar la respuesta en runtable en autoended todo y los stderr stdout devuelve true
                DBGiveUpRunAutojudging($contest, $number, $ip, "warning: problem package has no input files");
                continue;
            }
            $zip->extractTo($dir, array_merge(array("run" . $ds . $run["extension"]),array("compare" . $ds . $run["extension"]),$inputlist,$outputlist));
            $zip->close();
            if(chmod($dir . $ds . 'output', 0700)==false || chown($dir . $ds . 'output','root') == false) {
                //No se pudo establecer chown / chdir en la carpeta de salida; verifique el sistema y el paquete del problema
                echo "Failed to chown/chdir the output folder -- please check the system and problem package\n";
                DBGiveUpRunAutojudging($contest, $number, $ip, "error: chown/chmod failed for output (99)");
                continue;
            }
            if(chmod($dir . $ds . 'compare', 0700)==false || chown($dir . $ds . 'compare','root') == false) {
                //No se pudo establecer chown / chdir en la carpeta de salida; verifique el sistema y el paquete del problema
                echo "Failed to chown/chdir the output folder -- please check the system and problem package\n";
                DBGiveUpRunAutojudging($contest, $number, $ip, "error: chown/chmod failed for output (99)");
                continue;
            }
        } else {
            //No se pudo descomprimir el archivo (entradas) - verifique el paquete problemático
            echo "Failed to unzip the file (inputs) -- please check the problem package\n";
            //actulizar la respuesta en runtable en autoended todo y los stderr stdout devuelve true
            DBGiveUpRunAutojudging($contest, $number, $ip, "error: problem package file is invalid (8)");
            continue;
        }
        $retval = 0;
        $script = $dir . $ds . 'run' . $ds . $run["extension"];
        if(!is_file($script)) {
            //No se pudo descomprimir el script de ejecución; verifique el paquete del problema
            echo "Failed to unzip the run script -- please check the problem package\n";
            DBGiveUpRunAutojudging($contest, $number, $ip, "error: problem package file is invalid (9)");
            continue;
        }
        chdir($dir);
        chmod($script, 0700);
        mkdir('team', 0755);

        $scriptcomp = $dir . $ds . 'compare' . $ds . $run["extension"];
        $answer='(Contact staff) nothing compared yet';//(Personal de contacto) nada comparado todavía
        chmod($scriptcomp, 0700);

        if($ninputlist == 0) {
            //ADVERTENCIA: NO hay archivos de entrada en el paquete ZIP. ¿Debería verificar el paquete problemático?
            echo "WARN: There are NO input files in ZIP package -- should check the problem package?\n";
            DBGiveUpRunAutojudging($contest, $number, $ip, "warning: problem package has no input files");
            continue;
        } else {
            $errp=0; $ncor=0; $showcor=false;
            sort($inputlist);
            //dentro de for esta todo en esta parte
            foreach($inputlist as $file) {
                $file = basename($file);
                if(is_file($dir . $ds . "input" . $ds . $file)) {
                    //si el nombre termina en .link este sera recortado tambien
                    $file1=basename($file,'.link');
                    if($file != $file1) {
                        $fnam = trim(file_get_contents($dir . $ds . "input" . $ds . $file));
                        //El archivo de entrada $file es un enlace. Intentando leer el archivo vinculado:
                        echo "Input file $file is a link. Trying to read the linked file: ($fnam)\n";
                        if(is_readable($fnam)) {
                            @unlink($dir . $ds . "input" . $ds . $file);
                            $file = basename($file,".link");
                            @copy($fnam,$dir . $ds . "input" . $ds . $file);
                        } else {
                            //No se pudieron leer los archivos de entrada del enlace indicado en el ZIP; verifique el paquete del problema
                            echo "Failed to read input files from link indicated in the ZIP -- please check the problem package\n";
                            DBGiveUpRunAutojudging($contest, $number, $ip, "error: problem package file is invalid (11) or missing files on the autojudge");
                            $errp=1; break;
                        }
                    }

                    $ex = escapeshellcmd($script) ." ".
                    escapeshellarg($basename) . " ".
                    escapeshellarg($dir . $ds . "input" . $ds . $file)." ".
                    escapeshellarg(trim($limits[$basename][$run["extension"]][0]))." ".
                    escapeshellarg(trim($limits[$basename][$run["extension"]][1]))." ".
                    escapeshellarg(trim($limits[$basename][$run["extension"]][2]))." ".
                    escapeshellarg(trim($limits[$basename][$run["extension"]][3]));
                    $ex .= " >stdout 2>stderr";

                    chdir($dir);
                    if(file_exists($dir . $ds . 'tmp')) {
                        cleardir($dir . $ds . 'tmp');
                    }
                    mkdir($dir . $ds . 'tmp', 0777);
                    @chown($dir . $ds . 'tmp',"nobody");
                    if(is_readable($dir . $ds . $basename)) {
                        @copy($dir . $ds . $basename, $dir . $ds . 'tmp' . $ds . $basename);
                        @chown($dir . $ds . 'tmp' . $ds . $basename,"nobody");
                        @chmod($dir . $ds . 'tmp' . $ds . $basename,0755);
                    }
                    if(is_readable($dir . $ds . 'run.jar')) {
                        @copy($dir . $ds . 'run.jar', $dir . $ds . 'tmp' . $ds . 'run.jar');
                        @chown($dir . $ds . 'tmp' . $ds . 'run.jar',"nobody");
                        @chmod($dir . $ds . 'tmp' . $ds . 'run.jar',0755);
                    }
                    if(is_readable($dir . $ds . 'run.exe')) {
                        @copy($dir . $ds . 'run.exe', $dir . $ds . 'tmp' . $ds . 'run.exe');
                        @chown($dir . $ds . 'tmp' . $ds . 'run.exe',"nobody");
                        @chmod($dir . $ds . 'tmp' . $ds . 'run.exe',0755);
                    }
                    chdir($dir . $ds . 'tmp');
                    echo "Ejecutando " . $ex . " en " . getcwd() . " para entrada " . $file . "\n";
                    if(system($ex, $localretval)===false) $localretval=-1;
                    foreach (glob($dir . $ds . 'tmp' . $ds . '*') as $fne) {
                        @chown($fne,"nobody");
                        @chmod($fne,0755);
                    }
                    if(is_readable('stderr0'))
                        system('/bin/cat stderr0 >> stderr');
                    system('/bin/echo ##### STDERR FOR FILE ' . escapeshellarg($file) . ' >> ' . $dir . $ds . 'allerr');
                    system('/bin/cat stderr >> ' . $dir . $ds . 'allerr');
                    system('/bin/cat stdout > ' . $dir . $ds . 'team' . $ds . escapeshellarg($file));
                    system('/bin/echo ##### STDOUT FOR FILE ' . escapeshellarg($file) . ' >> ' . $dir . $ds . 'allout');
                    system('/bin/cat stdout >> ' . $dir . $ds . 'allout');
                    //para team
                    if($localretval != 0) {
                        system('/bin/echo ##### STDOUT FOR FILE ' . escapeshellarg($file) . ' >> ' . $dir . $ds . 'fabians7');
                        system('/bin/cat stdout >> ' . $dir . $ds . 'fabians7');
                    }

                    chdir($dir);
                    if($localretval != 0) {
                        list($retval,$answer) = exitmsg($localretval);
                        $answer = "(WHILE RUNNING) " . $answer;
                        break;
                    }

                    //para error en el tiempo de ejecucion tiene error
                    if(is_file($dir . $ds . 'output' . $ds . $file)) {
                        @unlink($dir . $ds . 'compout');
                        $ex = escapeshellcmd($scriptcomp) ." ".
                        escapeshellarg($dir . $ds . "team" . $ds . $file)." ".
                        escapeshellarg($dir . $ds . "output" . $ds . $file)." ".
                        escapeshellarg($dir . $ds . "input" . $ds . $file) . " >compout 2>&1";
                        echo "Executing " . $ex . " at " . getcwd() . " for output file $file\n";
                        if(system($ex, $localretval)===false)
                            $localretval = -1;

                        $fp = fopen($dir . $ds . "allerr", "a+");
                        //para teamSIGUE LA SALIDA DEL GUIÓN DE COMPARACIÓN PARA EL ARCHIVO file1 (VACÍO SIGNIFICA QUE NO HAY DIFERENCIA)
                        $f7 = fopen($dir . $ds . "fabians7", "a+");
                        fwrite($fp, "\n\n===OUTPUT OF COMPARING SCRIPT FOLLOWS FOR FILE " .$file ." (EMPTY MEANS NO DIFF)===\n");
                        fwrite($f7, "\n\n===EL PRIMERO ES TU SALIDA Y EL SEGUNDO DEL JUEZ (VACIO, SIGNIFICA QUE NO HAY DIFERENCIA)===\n");
                        $dif = file($dir . $ds . "compout");
                        $difi = 0;
                        for(; $difi < count($dif)-1 && $difi < 5000; $difi++){
                            fwrite($fp, $dif[$difi]);
                            //para team
                            if(strpos($dif[$difi], "/home/salomonjail") === false){
                                fwrite($f7, $dif[$difi]);
                            }
                        }
                        if($difi >= 5000) fwrite($fp, "===OUTPUT OF COMPARING SCRIPT TOO LONG - TRUNCATED===\n");
                        else{
                            fwrite($fp, "===OUTPUT OF COMPARING SCRIPT ENDS HERE===\n");
                            fwrite($f7, "===SALIDA DE COMPARACION TERMINA AQUI===\n");
                        }
                        $answertmp = '';
                        if(count($dif) > 0)
                            $answertmp = substr(trim($dif[count($dif)-1]),0,200);
                        $answertmp = sanitizeText($answertmp);
                        fclose($fp);
                        fclose($f7);
                        /*
                        foreach (glob($dir . $ds . '*') as $fne) {
                          if(is_file($fne)) {
                        @chown($fne,"nobody");
                        @chmod($fne,0755);
                          }
                        }
                        */
                        // retval 5 (presentation) and retval 6 (wronganswer) are already compatible with the compare script
                        if($localretval < 4 || $localretval > 6) {
                            // contact staff
                            $retval = 7;
                            $answer='(Contact staff)' . $answertmp;
                            if($showcor) $answertmp .= ' (' . $ncor . '/' . $ninputlist . ' OKs)';
                            break;
                        }
                        if($localretval == 6) {
                            $retval=$localretval;
                            $answer='(Wrong answer)'. $answertmp;
                            if($showcor) $answertmp .= ' (' . $ncor . '/' . $ninputlist . ' OKs)';
                            break;
                        }
                        if($localretval == 5) {
                            $retval=$localretval;
                            $answer='(Presentation error)'. $answertmp;
                            if($showcor) $answertmp .= ' (' . $ncor . '/' . $ninputlist . ' OKs)';
                        } else {
                            if($localretval != 4) {
                                $retval = 7;
                                $answer='(Contact staff)' . $answertmp;
                                if($showcor) $answertmp .= ' (' . $ncor . '/' . $ninputlist . ' OKs)';
                                    break;
                            }
                            $ncor++;
                            if($retval == 0 || $retval == 1) {
                                // YES!
                                $answer='(YES)' . $answertmp;
                                if($showcor) $answertmp .= ' (' . $ncor . '/' . $ninputlist . ' OKs)';
                                $retval = 1;
                            }
                        }
                    } else {
                        echo "==> ERROR reading output file " . $dir . $ds . 'output' . $ds . $file . " - skipping it!\n";
                    }

                } else {
                    //==> ERROR leyendo archivo de entrada
                    echo "==> ERROR reading input file " . $dir . $ds . "input" . $ds . $file . " - skipping it!\n";
                }
            }
            if($errp==1) continue;
        }
        /*
          if($retval==0) {
          echo "Processing results\n";
          $zip = new ZipArchive;
          if ($zip->open($dir . $ds . $run["inputname"]) === true) {
          $zip->extractTo($dir, array_merge(array("compare" . $ds . $run["extension"]),$outputlist));
          $zip->close();
          } else {
          echo "Failed to unzip the file (outputs) -- please check the problem package\n";
          DBGiveUpRunAutojudging($contest, $site, $number, $ip, "error: problem package file is invalid (12)");
          continue;
          }
          $script = $dir . $ds . 'compare' . $ds . $run["extension"];
          $retval = 0;
          $answer='(Contact staff) nothing compared yet';
          chmod($script, 0700);
          foreach($outputlist as $file) {
          $file = basename($file);
          if(is_file($dir . $ds . 'output' . $ds . $file)) {
          @unlink($dir . $ds . 'compout');
          $ex = escapeshellcmd($script) ." ".
          escapeshellarg($dir . $ds . "team" . $ds . $file)." ".
          escapeshellarg($dir . $ds . "output" . $ds . $file)." ".
          escapeshellarg($dir . $ds . "input" . $ds . $file) . " >compout";
          echo "Executing " . $ex . " at " . getcwd() . " for output file $file\n";
          if(system($ex, $localretval)===false)
          $localretval = -1;

          $fp = fopen($dir . $ds . "allerr", "a+");
          fwrite($fp, "\n\n===OUTPUT OF COMPARING SCRIPT FOLLOWS FOR FILE " .$file ." (EMPTY MEANS NO DIFF)===\n");
          $dif = file($dir . $ds . "compout");
          $difi = 0;
          for(; $difi < count($dif)-1 && $difi < 5000; $difi++)
          fwrite($fp, $dif[$difi]);
          if($difi >= 5000) fwrite($fp, "===OUTPUT OF COMPARING SCRIPT TOO LONG - TRUNCATED===\n");
          else fwrite($fp, "===OUTPUT OF COMPARING SCRIPT ENDS HERE===\n");
          $answertmp = trim($dif[count($dif)-1]);
          fclose($fp);
          foreach (glob($dir . $ds . '*') as $fne) {
          @chown($fne,"nobody");
          @chmod($fne,0755);
          }
          // retval 5 (presentation) and retval 6 (wronganswer) are already compatible with the compare script
          if($localretval < 4 || $localretval > 6) {
          // contact staff
          $retval = 7;
          $answer='(Contact staff)' . $answertmp;
          break;
          }
          if($localretval == 6) {
          $retval=$localretval;
          $answer='(Wrong answer)'. $answertmp;
          break;
          }
          if($localretval == 5) {
          $retval=$localretval;
          $answer='(Presentation error)'. $answertmp;
          } else {
          if($localretval != 4) {
          $retval = 7;
          $answer='(Contact staff)' . $answertmp;
          break;
          }
          if($retval == 0) {
          // YES!
          $answer='(YES)' . $answertmp;
          $retval = 1;
          }
          }
          } else {
          echo "==> ERROR reading output file " . $dir . $ds . 'output' . $ds . $file . " - skipping it!\n";
          }
          }
          }
        */
    }

    if($retval >= 7 && $retval <= 9) {
        $ans = file("allout");
        $anstmp = '';
        if(count($ans) > 0)
            $anstmp = substr(trim(escape_string($ans[count($ans)-1])),0,100);
        unset($ans);
        if(strpos(file_get_contents('allerr'),'Error: Could not find or load main class') === false) {
            $answer = "(probably runtime error - unusual code: $retval) " . $anstmp;
            // runtime error
            $retval = 3;
        } else {
            $answer = "(probably wrong name of class - unusual code: $retval) "; // . $anstmp;
            $retval = 8;
        }
    }
    if($retval == 0 || $retval > 9) {
        $ans = file("allout");
        $anstmp = substr(trim(escape_string($ans[count($ans)-1])),0,100);
        unset($ans);
        LogLevel("Autojudging: Script returned unusual code: $retval ($anstmp)".
            "(run=$number, contest=$contest)",1);
        echo "Autojudging script returned unusual code $retval ($anstmp)".
            "(contest=$contest, run=$number)\n";
        $answer = "(check output files - unusual code: $retval) " . $anstmp;
        // contact staff
        $retval = 7;
    }
    //Enviando resultados al servidor ...
    echo "Enviando resultado al servidor...\n";
    //echo "out==> "; system("tail -n1 ". $dir.$ds.'allout');
    //echo "err==> "; system("tail -n1 ". $dir.$ds.'allerr');
    $answer=substr($answer,0,200);

    ////actulizar la respuesta en runtable en autoended todo y los stderr stdout devuelve true
    DBUpdateRunAutojudging($contest, $number, $ip, $answer, $dir.$ds.'allout', $dir.$ds.'allerr', $dir.$ds.'fabians7', $retval);

    LogLevel("Autojuez: respondio $retval '$answer' (run=$number, contest=$contest)",3);
    echo "Autojuez respondido $retval '$answer' (contest=$contest, run=$number)\n";

}

?>
