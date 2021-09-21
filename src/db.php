<?php

if(isset($_SESSION["locr"]) && isset($_SESSION["loc"]) && !is_readable($_SESSION["locr"].'/private/conf.php')){
    MSGError('Permission problems in '.$_SESSION["locr"].'/private/conf.php - the file must be readable to the user running the web server');//alert
    exit;
}
require_once('hex.php');
require_once('globals.php');
require_once('private/conf.php');

//para compatibidad con versioes antiguas y nuevas de php, varias de las funciones han sido
//colocados qui para garantizar la portabilidad.Desafortunadamente algunos cambios de nombre
//y los parametros ocurrieron con la 4.2.0 de php.
//pg_lo_open () abre un objeto grande en la base de datos y devuelve un recurso
// de objeto grande para que pueda ser manipulado. pude ser w r rw el $mode
function DB_lo_open($conn,$file,$mode){
    if(strcmp(phpversion(),'4.2.0')<0)
        return pg_loopen($conn,$file,$mode);
    else
        return pg_lo_open($conn,$file,$mode);
}
//funcion para imprimir el contenido de oid inviado y retona true
function DB_lo_read_tobrowser($contest,$id,$c=null) {
    //pg_lo_read () lee como máximo lenbytes de un objeto grande y lo devuelve como una cadena .
    //Para utilizar la interfaz de objetos grandes, es necesario encerrarla dentro de un bloque de transacciones.
    $str = DB_lo_read($id,-1,$c);
    echo $str;
    return true;
}
//pg_lo_read () lee como máximo lenbytes de un objeto grande y lo devuelve como una cadena .
//Para utilizar la interfaz de objetos grandes, es necesario encerrarla dentro de un bloque de transacciones.
function DB_lo_read($id,$s=-1,$c=null) {

	if (strcmp(phpversion(),'4.2.0')<0) {
		if($s<0) {
			$str='';
			while (($buf = pg_loread ($id, 1000000)) != false) $str .= $buf;
		} else
			$str = pg_loread ($id, $s);
	}else {
		if($s<0) {
			$str='';
			while (($buf = pg_lo_read ($id, 1000000)) != false) $str .= $buf;
		} else
			$str = pg_lo_read ($id, $s);
	}

	return $str;
}

//pg_lo_import () crea un nuevo objeto grande en la base de datos usando un
//archivo en el sistema de archivos como fuente de datos. devuelve el oid. dependiendo de la version 7.2 o < 4.2.0
function DB_lo_import($conn,$file){
    if(strcmp(phpversion(),'4.2.0')<0)
        return pg_loimport($file,$conn);
    else
        return pg_lo_import($conn,$file);
}
//importa un texto a un archivo creado en la base de datos y devuelve el oid del archivo
function DB_lo_import_text($conn,$text){
    if(($oid=DB_lo_create($conn))===false) return false;
    if(($handle=DB_lo_open($conn,$oid,"w"))===false) return false;
    if(DB_lo_write($handle,$text)===false) $oid=false;
    DB_lo_close($handle);
    return $oid;
}
//pg_lo_export () toma un objeto grande en una base de datos PostgreSQL y guarda
//su contenido en un archivo en el sistema de archivos local.
function DB_lo_export($conn, $oid, $file) {
	if (strcmp(phpversion(),'4.2.0')<0)
		$stat= pg_loexport ($oid, $file, $conn);
	else
		$stat= pg_lo_export ($oid, $file, $conn);
	if($stat===false) return false;//
	if(!is_readable($file)) return false;//

	return 1;
}
//pg_lo_close () cierra un objeto grande. large_object es un recurso para el objeto
//grande de pg_lo_open () . devuelve true en caso de exito o false en caso de error
function DB_lo_close($id){
    if(strcmp(phpversion(),'4.2.0')<0)
        return pg_loclose($id);
    else
        return pg_lo_close($id);
}

//pg_lo_create () crea un objeto grande y devuelve el OID del objeto grande. y devuelve el oid
function DB_lo_create($conn){
    if(strcmp(phpversion(),'4.2.0')<0)
        return pg_locreate($conn);
    else
        return pg_lo_create($conn);
}
//pg_lo_write () escribe datos en un objeto grande en la posición de búsqueda actual.
//devuelve El número de bytes escritos en el objeto grande o false en caso de error.
function DB_lo_write($fp,$data){
    if(strcmp(phpversion(),'4.2.0')<0)
        return pg_lowrite($fp,$data);
    else
        return pg_lo_write($pf,$data);
}
//pg_lo_unlink () elimina un objeto grande con la oid . Rendimientos true en caso de éxito o false fracaso.
//Para utilizar la interfaz de objetos grandes, es necesario encerrarla dentro de un bloque de transacciones.
function DB_lo_unlink($conn, $data) {
	if(($fp = DB_lo_open ($conn, $data, "r"))===false) return false;
	DB_lo_close($fp);
	if (strcmp(phpversion(),'4.2.0')<0)
		return pg_lounlink ($conn, $data);
	else
		return pg_lo_unlink ($conn, $data);
}

//funcion para realizar una conexcion o su es true forcenew forza de nuevo la conexcion encaso de no tener existo
//lanza un error si existe dbclienteenc ejecuta set names encoding
function DBConnect($forcenew=false){
    $conf=globalconf();
    if($conf["dblocal"]=="true"){
        if($forcenew)
            $conn=@pg_connect("connect_timeout=10 dbname=".$conf["dbname"]." user=".$conf["dbuser"].
                        " password=".$conf["dbpass"],PGSQL_CONNECT_FORCE_NEW);
        else
            $conn=@pg_connect("connect_timeout=10 dbname=".$conf["dbname"]." user=".$conf["dbuser"].
                        " password=".$conf["dbpass"]);
    }else{
        if($forcenew)
            $conn=@pg_connect("connect_timeout=10 host=".$conf["dbhost"]." port=".$conf["dbport"]." dbname=".$conf["dbname"]." user=".$conf["dbuser"].
                        " password=".$conf["dbpass"],PGSQL_CONNECT_FORCE_NEW);
        else
            $conn=@pg_connect("connect_timeout=10 host=".$conf["dbhost"]." port=".$conf["dbport"]." dbname=".$conf["dbname"]." user=".$conf["dbuser"].
                    " password=".$conf["dbpass"]);
    }
    if(!$conn){
        //para error globals.php
        LOGError("Unable to connect to database (${conf["dbhost"]},${conf["dbname"]},${conf["dbuser"]}).");//funcion para generar el nivel de registro y prioridad para el sistema nivel 0
        MSGError("Unable to connect to database (${conf["dbhost"]}:${conf["dbport"]},${conf["dbname"]},${conf["dbuser"]}). ".
            "Is it running? is the db password in conf.php correct?");//lazan un mesaje de error con javascript alert este
        exit;
    }
    if(isset($conf["dbclientenc"]))
        DBExecNonStop($conn, "SET NAMES '${conf["dbclientenc"]}'","set client encoding");//ejecuta el sql en caso de no exito lanza un error
    return $conn;
}
//funcionn retorna un registro de una cosulta en array asociativo en caso de no existo lanza un error
function DBGetRow($sql, $i, $c=null, $txt=''){
    if($txt=='') $txt='unknown at '.getFunctionName();//desconocida en una parte de funcion
    if($c==null)
        $c=DBConnect();//realizar conexcion su no existe conexcion
    $r=DBExec($c,$sql,$txt);//ejecuta el sql y retorna el resultado en caso de no lanza un mesaje de error y la prioridad del mesaje
    //retorna el numero de filas de un resultado
    if(DBnlines($r)<$i+1) return null;
    $a=DBRow($r,$i);//retorna en array asocciotivo dando el resutado de la consulta y posicion
    if(!$a){
        DBClose($c);//funcion para cerrar la conexcion de la base de datos
        LOGError("Unable to get row $i from a query ($txt). SQL=(".$sql.")");///funcion para generar el nivel de registro y prioridad para el sistema nivel 0
        MSGError("Unable to get row from query ($txt).");//mesaje script
        exit;
    }
    return $a;
}
//funcion para eliminar la base de datos
function DBDropDatabase(){
    $conf=globalconf();
    if($conf["dblocal"]=="true")
        $conn=pg_connect("connect_timeout=10 dbname=template1 user=".$conf["dbsuperuser"]." password=".$conf["dbsuperpass"]);
    else
        $conn=pg_connect("connect_timeout=10 host=".$conf["dbhost"]." port=".$conf["dbport"]." dbname=template1 user=".$conf["dbsuperuser"].
                " password=".$conf["dbsuperpass"]);
    if(!$conn){
        MSGError("Unable to connect to template1 as ".$conf["dbsuperuser"]);
        exit;
    }
    //ejecuta el sql en caso de no exito lanza un error
    $r=DBExecNonStop($conn,"drop database ${conf["dbname"]}","DBDropDatabase(drop)");
}
//funcion para create la base de datos
function DBCreateDatabase(){
    $conf=globalconf();
    if($conf["dblocal"]=="true")
        $conn=pg_connect("connect_timeout=10 dbname=template1 user=".$conf["dbsuperuser"]." password=".$conf["dbsuperpass"]);
    else
        $conn=pg_connect("connect_timeout=10 host=".$conf["dbhost"]." port=".$conf["dbport"]." dbname=template1 user=".$conf["dbsuperuser"].
                " password=".$conf["dbsuperpass"]);
    if(!$conn){
        MSGError("Unable to connect to template1 as ".$conf["dbsuperuser"]);
        exit;
    }
    if(isset($conf["dbencoding"]))
        $r=DBExec($conn, "create database ${conf["dbname"]} with encoding = '${conf["dbencoding"]}'", "DBCreateDatabase(create)");
    else
        $r=DBExec($conn, "create database ${conf["dbname"]} with encoding = 'UTF8'", "DBCreateDatabase(create)");
}
//funcion para cerrar la conexcion de la base de datos
function DBClose($c){
    pg_close($c);
}
//funcion que retorna en array asocciotivo dando el resutado de la consulta y posicion
function DBRow($r,$i){
    return pg_fetch_array($r,$i, PGSQL_ASSOC);//esta devolvera el primer resultado con 0
}
//retorna el numero de filas de un resultado
function DBnlines($result){
    //return pg_numrows($result); para otras versiones
    return pg_num_rows ($result);
}
//ejecuta el sql y retorna el resultado en caso de no lanza un mesaje de error y la prioridad del mesaje
function DBExec($conn,$sql,$txt=''){
    if($txt=='') $txt='unknown at '.getFunctionName();//desconocida en una parte de funcion
    $result=DB_pg_exec($conn,$sql);//ejecutamos el sql pasando conexcion en caso de realizar false
    if(!$result){
        //funcion para generar el nivel de registro y prioridad para el sistema nivel 0
        LOGError("Unable to exec sql in the database ($txt). ".
            " SQL=(".sanitizeText(str_replace("\n"," ",$sql)) . "Error=(" . sanitizeText(str_replace("\n"," ",pg_errormessage($conn))) . ")");
        MSGError("Unable to exec SQL in the database ($txt). Aborting.");//lazan un mesaje de error con javascript alert este
        exit;
    }
    return $result;

}
//retona el resultado de la ejecucion del sql y realizarforzamineto de nuevo la conexcion
function DBExecNoSQLLog($sql,$txt=''){
    if($txt=='') $txt='unknown at '.getFunctionName();
    $conn=DBConnect(true);
    $result=DB_pg_exec($conn,$sql);//ejecutamos el sql pasando conexcion en caso de realizar false
    pg_close($conn);//Cierra una conexión PostgreSQL
    if(!$result){
        MSGError("Unable to exec SQL in the database ($txt).");//lazan un mesaje de error con javascript alert
    }
    return $result;
}

//funcion para ejecutar un sql con conexcion dada
function DB_pg_exec($conn, $data){
    if(strcmp(phpversion(),'4.2.0')<0){
        return pg_exec($conn,$data);
    }else{
        return pg_query($conn,$data);
    }
}
//funcion una cadena para query
function escape_string($str){
    if(strcmp(phpversion(),'4.2.0')<0)
        return addslashes($str);
    else
        return pg_escape_string($str);//pg_escape_string - Escape de una cadena para la consulta
}
//ejecuta el sql en caso de no exito lanza un error
function DBExecNonStop($conn, $sql, $txt=''){
    if($txt=='') $txt='unknown at '.getFunctionName();
    $result=@DB_pg_exec($conn,$sql);//para ejecutar un sql con conexcion dada
    if(!$result)
        LOGError("Unable to exec sql in tha database ($txt). " .
                    " Error=(".pg_errormessage($conn) .")");//pg_errormessage para version mas antigua
    return $result;
}
//funcion devuelve la informacion de oid enviado pg_lo_read en hash
function DBcrc($id, $c=null) {
	$docommit=false;
	if($c == null) {
		$docommit=true;
		$c = DBConnect();
		DBExec($c, "begin work", "DBcrc(begin)");
	}
    //pg_lo_open () abre un objeto grande en la base de datos y devuelve un recurso
    // de objeto grande para que pueda ser manipulado. pude ser w r rw el $mode
	if(($f = DB_lo_open($c, $id, "r")) === false) {
		if($docommit)
			DBExec($c, "commit work", "DBcrc(commit)");
        // just to return a unique string that will not match any other...
		return "no-HASH-" . rand() . "-" . rand() . "-" . time();
	}
    //pg_lo_read () lee como máximo lenbytes de un objeto grande y lo devuelve como una cadena .
    //Para utilizar la interfaz de objetos grandes, es necesario encerrarla dentro de un bloque de transacciones.
	$str = DB_lo_read($f,-1,$c);
	DB_lo_close($f);
	if($docommit)
		DBExec($c, "commit work", "DBcrc(commit)");
	return myshorthash($str);
}
require_once('flog.php');
require_once('fcontest.php');
require_once('fproblem.php');
require_once('fanswer.php');
//require_once('ftask.php'); //en desarrollo
require_once('flanguage.php');
require_once('frun.php');
//require_once('fclar.php');
//require_once('fbkp.php');

require_once('fscore.php');
//segun a lo abanzado incluir
require_once('fzip.php');
require_once('fballoon.php');
//https://www.youtube.com/watch?v=SKRmk_xDyRc
?>
