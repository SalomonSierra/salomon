<?php
require_once('db.php');
define("dbcompat_1_4_1",true);
//limpia si tiene se utilizo addcshashes()  \D\F\SAS
function sanitizeVariables(&$item, $key){
    if(!is_array($item)){
        //deshaciendo la directiva magic-quotes_gpc
        if(get_magic_quotes_gpc())
            $item=stripcslashes($item);//stripcslashes — Desmarca la cadena marcada con addcslashes()
        $item=sanitizeText($item);//METODO para mofigicar algunos caracteres especiales para htmlentities
    }
}
//devuelve la cadena condifica, primero codifica a base64 y luego remplaza algunos caracteres especiales
//% a delimitadores de urul especiales
function myrawurlencode($txt) {
    //base64_encode — Codifica datos con MIME base64
    //Devuelve una cadena en donde todos los caracteres no-alfanuméricos, excepto -_.~,
    //son reemplazados con un signo de porcentaje (%) seguido de dos dígitos hexadecimales.
    //Este es el tipo de codificación descrito en el » RFC 3986 para evitar que caracteres
    //literales sean interpretados como delimitadores de URL especiales, y para evitar que las
    //URLs sean modificadas por medios de transmisión con conversiones de caracteres (como algunos
    //sistemas de correo electrónico).
    return(rawurlencode(base64_encode($txt)));
}
//funcion devuelve decodificado primero decodifica signos de porcentaje url y luego base_64
function myrawurldecode($txt){
    //Devuelve los datos decodificados o false en caso de error. Los datos devueltos
    //pueden estar en formato binario.string
    //Devuelve una cadena en donde las con secuencias con signos de porcentaje (%)
    //seguidos de dos dígitos hexadecimales, son reemplazados con caracteres literales.
    return base64_decode(rawurldecode($txt));
}
//funcion para encriptar datos enviados devuelve dato encriptado tipo url &file=
function filedownload($oid,$fname,$msg='') {

	$cf = globalconf();
    //myrawurlencode devuelve la cadena condifica, primero codifica a base64 y luego remplaza algunos caracteres especiales
    //% a delimitadores de urul especiales
    ////CONTENIDO , FfHf7nMddw33E9fEzcWw, 2
    //encripta el dato con key
	$if = myrawurlencode(encryptData($fname, session_id() . $cf['key'],false));

    $p = myhash($oid . $fname . $msg . session_id() . $cf["key"]);
	$str = "oid=". $oid . "&filename=". $if . "&check=" . $p."#toolbar=0";//editado
	if($msg != '') $str .= "&msg=" . myrawurlencode($msg);
	return $str;
}

//cleardir($file,false,true); puede ser directorio o archivo
//funcion para eliminar el direcctorio junto con los archivos en recursividad
function cleardir($dir,$cddir=true,$secure=true,$removedir=true){
    //file_exists — Comprueba si existe un fichero o directorio
    if(file_exists($dir)){
        //Devuelve true si el nombre de archivo existe y es un directorio, false si no.
        if(is_dir($dir)){
            $ds=DIRECTORY_SEPARATOR;
            if($ds=="") $ds="/";
            if($cddir){//true
                //cambia de directorio
                chdir($dir);
                chdir('..');
            }
            //Abre un gestor de directorio para ser usado con llamadas posteriores
            // como closedir(), readdir(), y rewinddir().
            $d=@opendir($dir);
            while(($file=@readdir($d))!==false){
                if($file!='.' && $file!='..')
                    cleardir($dir.$ds.$file,false,$secure,true);
            }
            //closedir — Cierra un gestor de directorio
            @closedir($d);
            if($removedir)
                @rmdir($dir);//rmdir — Elimina un directorio

        }else{
            //is_link — Indica si el nombre de archivo es un enlace simbólico
            if($secure && !is_link($dir)){
                //Si filename no existe, se crea el fichero. De otro modo, el fichero
                //existente se sobrescribe, a menos que la bandera FILE_APPEND esté establecida.
                file_put_contents($dir,str_repeat('XXXXXXXXXX',10000));//str_repeat es repitir la cadena 10000
            }
            //unlink — Borra un fichero
            @unlink($dir);
        }
    }
}



//funcion retorna true o false si no existe usertable en session false si es id diferente false
//si aun no inicio session FALSE
//de usuario es true su multi llogion  TRUE
//si el ip son diferentes FALSE
function ValidSession(){

    if(!isset($_SESSION["usertable"]))  return (FALSE);//si no existe la variable globas usertable return false
    $gip=getIP();//guardamos el ip del cliente con double

    if($_SESSION["usertable"]["usersession"]!=session_id()) return (FALSE);
    $tmp=DBUserInfo($_SESSION["usertable"]["usernumber"]);//metodo retorna el registro de usuario y tambien si cambio o no hashpass = true

    if($tmp['usersession']=='')return (FALSE);//si no inicio session retorna false
    if($_SESSION["usertable"]["usermultilogin"]=='t') return (TRUE);//los equipos si tienen falso o los otros no
    //si los ips no coiciden retorna false

    if($tmp["userip"]!=$gip) return(FALSE);//ELLOS PUEDEN CREAR UN PROBLEM HERE TAMBIEN
    //si no ingreso a nada retorna true

    return(TRUE);

}
//funcion para capturar el ip del cliente
function getIP(){
    if(getenv("REMOTE_ADDR"))//ES PARA CAPTURA EL IP
        $ip= getenv("REMOTE_ADDR");
    else
        return "UNKNOWN";//ERROR DE LA OBTENCION DE LA variable local
    if(defined("dbcompat_1_4_1") && dbcompat_1_4_1==true) return $ip;//Comprueba si existe la constante ddada y esta definida
    $ip1='';
    if(getenv("HTTP_X_FORWARDED_FOR")){
        $ip1=getenv("HTTP_X_FORWARDED_FOR");//ALGO MAS GENERAL DEL IP
        $ip1=strtok($ip1, ",");//es un delimitador
        if($ip1 !=$ip) $ip.= ';'.$ip1;
    }
    if(getenv("HTTP_CLIENT_IP")){
        $ip1a=getenv("HTTP_CLIENT_IP");
        $ip1a=strtok($ip1a, ",");
        if($ip1a != $ip1 && $ip1a!=getenv("REMOTE_ADDR")) $ip.=';'.$ip1a;
    }else{
        if(getenv('HTTP_X_FORWARDED')){
            $ip.=';'.getenv('HTTP_X_FORWARDED');
        }else {
            if(getenv('HTTP_FORWARDED')){
                $ip.=';'.getenv("HTTP_FORWARDED");
            }
        }
    }
    return sanitizeText($ip);//es para
}
// gen cadena alfanum aleatoria
function randstr($len=8,$from='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') {
	$str='';
	$fromlen=strlen($from);
	while($len > 0) {
        //rand — Genera un número entero aleatorio
		$str .= substr($from,rand(0,$fromlen-1),1);
		$len--;
	}
	return $str;
}
////funcion para mofigicar algunos caracteres especiales para htmlentities sin &
function myhtmlspecialchars($text){
    return sanitizeText($text,false);
}
//funcion para mofigicar algunos caracteres especiales para htmlentities
function sanitizeText($text, $doamp=true){
    if($doamp)
        $text=str_replace("&","&amp;", $text);
    $text=str_replace("<","&lt;", $text);
    $text=str_replace(">","&gt;", $text);
    $text=str_replace("\"","&qout;", $text);
    $text=str_replace("'","&#39;", $text);
    $text=str_replace("`","&#96;", $text);
    $text=addslashes($text);
    return $text;
}
//elimina los caracteres que no son ASSCI y replaza caracteres como *"$&().. con _ y otro ' " lo remplaza con barra invertida
function sanitizeFilename($text){
    //Si desea eliminar todo, excepto los caracteres ASCII básicos imprimibles
    //(se eliminarán todos los caracteres de ejemplo anteriores), puede usar:
    //preg_replace Busca en subject coincidencias de pattern y las reemplaza con replacement.
    $text=preg_replace('/[^[:print:]]/','',$text);
    $text=str_replace(" ", "_", $text);
    $text=str_replace("*", "_", $text);
    $text=str_replace("$", "_", $text);
    $text=str_replace(")", "_", $text);
    $text=str_replace("(", "_", $text);
    $text=str_replace(";", "_", $text);
    $text=str_replace("&", "_", $text);
    $text=str_replace("<", "_", $text);
    $text=str_replace(">", "_", $text);
    $text=str_replace("\"", "_", $text);
    $text=str_replace("'", "_", $text);
    $text=str_replace("`", "_", $text);
    //Devuelve un string con barras invertidas delante de los caracteres que necesitan
    //ser escapados. Estos caracteres son la comilla simple ('), comilla doble ("), barra invertida (\) y NUL (el byte null).
    $text= addslashes($text);
    return $text;
}
//retorna el nombre de la funcion de donce se ejecuta..
function getFunctionName($num=2){
    if(strcmp(phpversion(), '5.3.5')<0){//Devuelve < 0 si str1 es menor que str2; > 0 si str1 es mayor que str2 y 0 si son iguales.
        $backtrace=debug_backtrace();
    }else{
        if(strcmp(phpversion(),'5.4.0')<0)
            $backtrace=debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
        else
            $backtrace=debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,$num+5);
    }
    $ret='';
    for($i=0;$i<$num;$i++){
        if(isset($backtrace[$i])&&isset($backtrace[$i]['function']))
            $ret.= " ".$backtrace[$i]['function'];
    }
    if($ret=='') $ret='undef';
    return $ret;


}
//retornamos el ip y hostname del cliente
function getIPHost(){
    $ips=explode(';',getIP());//delimitador
    $s='';
    for($ipn=0;$ipn<count($ips);$ipn++){
        $ip=$ips[$ipn];
        //las siguientes lineas donde se sugirio que las elimine
        //mario sanchez(ing. de sistemas y computacion, universidad de los andes bogota colombia)
        //porque son muy lentos de ejecutar dependiendo de la red
        //$host=@gethostbyaddr($ip);
        //if($host!=$ip&&$host!="") $s.=$ip."(".$host.")";
        //else
        $s.=$ip.' ';
    }
    return $s;
}


//funcion para generar el nivel de registro y prioridad para el sistema nivel 0
function LOGError($msg){
    LOGLevel($msg,0,false);//funcion para generar el nivel de registro y prioridad par el sistema y no registra a logtable
}
//funcion para generar el nivel de registro y prioridad para el sistema de info. pero no inserta en db logtable
function LOGInfo($msg) {
	LOGLevel($msg,2,false);
}
//funcion para generar el nivel de registro y prioridad par el sistema o puede resitar el registro a logtable si es true el $dodb
function LOGLevel($msg,$level,$dodb=true){
    //LOGLevel("User $user (contest=$contest,site=$site) logged out.",2);
    $msga=sanitizeText(str_replace("\n"," ",$msg));
    $msg=now().": ";
    //si php verssion llega a 5.10 entonces esto no funcionara
    if(strcmp(phpversion(),'5.4.0')<0) define_syslog_variables();
    $prior=LOG_CRIT;//2
    switch ($level) {
        case 0: $msg .= "ERROR: ";
            $type="error";
            $prior=LOG_ERR;//3
            break;
        case 1: $msg .= "WARN: ";
            $type="warn";
            $prior=LOG_WARNING;//4
            break;
        case 2: $msg .= "INFO: ";
            $type="info";
            $prior=LOG_INFO;//6
            break;
        case 3: $msg .= "DEBUG: ";
            $type="debug";
            $prior=LOG_DEBUG;//7
            break;
    }

    $msg .=getIPHost(). ": ".$msga;
    openlog("SALOMON", LOG_ODELAY,LOG_USER);//Open connection to system logger
    syslog($prior,$msg); //Genera un mensaje log de sistema
    closelog();//para cerrar
    if($dodb && isset($_SESSION["usertable"]))
        DBNewLog($_SESSION["usertable"]["usernumber"],$type,getIP(),$msga,"");//insertamos un nuevo log a la tabla logtable

}
// devuelve la fecha y la hora en segundos convertidos a valores predeterminados simples
function dateconvsimple ($d) {
	return date('H\:i', $d);
}
//transforma segundos para minutos
function dateconvminutes ($d) {
	return (int)($d/60);
}
//lazan un mesaje de error con javascript alert
function MSGError($msg){
    $msg=str_replace("\n"," ",$msg);

    echo "<script language=\"JavaScript\">\n";
    echo "alert('".$msg."');\n";
    echo "</script>\n";
}
//para competencias privadas
function ValidContest($contest){

    echo "<script language=\"JavaScript\">\n";
    echo "var pass=prompt(\"Introduzca la contraseña de la competencia\");";
    echo "document.location='problem.php?contest=$contest&id='+pass;\n";
    echo "</script></html>\n";

}

//Devuelve la fecha Unix actual con microsegundos con una formula retorna un numero entero
//un valor unico en el tiempo que esta ejecutando
function myunique($val=0){
    //microtime —microtime — Devuelve la fecha Unix actual con microsegundos
    return (((int)(100*microtime(true)))%10000000)*100+($val%100);
}
//date — Dar formato a la fecha/hora local
function now(){
    return date('H\:i:s T \- d/M/Y');
}
//date — Dar formato a la fecha/hora del parametro pasado
//devuelve la fecha y la hora en segundos convertidos a valores predeterminados
function dateconv($d){
    return date('H\:i:s T \- d/M/Y',$d);
}


//genera script para redireccion con el parametro dada
function ForceLoad($where){
    echo "<script language=\"JavaScript\">\n";
    echo "document.location='".$where."';\n";
    echo "</script></html>\n";
}
//funcion para expirar el session y registar 3= debug en logtable
function InvalidSession($where){
    $msg="Sesion expirada en $where";
    LOGLevel($msg,3);//registrar 3 = debug en la base de datos logtable
    unset($_SESSION["usertable"]);
    MSGError("session expirada debes iniciar session nuevamente.");//session expirada debes iniciar session nuevamente.
}
//una notificacion de violacion de seguridad
function IntrusionNotify($where){
    $msg = "Security Violation: $where";
    if(isset($_SESSION["usertable"]["username"]))
        $msg .= " (".$_SESSION["usertable"]["username"].")";
    unset($_SESSION["usertable"]);
    LOGLevel($msg,1);//funcion para generar el nivel de registro y prioridad par el sistema y si registra a logtable
    MSGError("Violation ($where). Admin Warned.");//alert(Administrador advertido)
}


/**
 * Compare an IP address to network(s)
 *
 * The network(s) argument may be a string or an array. A negative network
 * match must start with a "!". Depending on the 3rd parameter, it will
 * return true or false on the first match, or any negative rule will have
 * absolute priority (default).
 *
 * Samples:
 * match_network ("192.168.1.0/24", "192.168.1.1") -> true
 *
 * match_network (array ("192.168.1.0/24",  "!192.168.1.1"), "192.168.1.1")       -> false
 * match_network (array ("192.168.1.0/24",  "!192.168.1.1"), "192.168.1.1", true) -> true
 * match_network (array ("!192.168.1.0/24", "192.168.1.1"),  "192.168.1.1")       -> false
 * match_network (array ("!192.168.1.0/24", "192.168.1.1"),  "192.168.1.1", true) -> false
 *
 * @param mixed  Network to match
 * @param string IP address
 * @param bool   true: first match will return / false: priority to negative rules (default)
 * @see http://php.benscom.com/manual/en/function.ip2long.php#56373

* Compare una dirección IP con la (s) red (es)
 *
 * El argumento de la (s) red (es) puede ser una cadena o una matriz. Una red negativa
 * la coincidencia debe comenzar con un "!". Dependiendo del tercer parámetro,
 * devolver verdadero o falso en la primera coincidencia, o cualquier regla negativa tendrá
 * prioridad absoluta (por defecto).
 *
 * Muestras:
 * match_network ("192.168.1.0/24", "192.168.1.1") -> verdadero
 *
 * match_network (matriz ("192.168.1.0/24", "! 192.168.1.1"), "192.168.1.1") -> falso
 * match_network (matriz ("192.168.1.0/24", "! 192.168.1.1"), "192.168.1.1", verdadero) -> verdadero
 * match_network (matriz ("! 192.168.1.0/24", "192.168.1.1"), "192.168.1.1") -> falso
 * match_network (matriz ("! 192.168.1.0/24", "192.168.1.1"), "192.168.1.1", verdadero) -> falso
 *
 * @param Red mixta para que coincida
 * @param string dirección IP
 * @param bool verdadero: la primera coincidencia devolverá / falso: prioridad a las reglas negativas (predeterminado)
 * @see http://php.benscom.com/manual/en/function.ip2long.php#56373
 */
function match_network ($nets, $ip) {
    if (!is_array ($nets)) $nets = explode(",",$nets);

    foreach ($nets as $net) {
	    $net = trim($net);
        $rev = (preg_match ("/^\!/", $net)) ? true : false;
        $net = preg_replace ("/^\!/", "", $net);

        $ip_arr   = explode('/', $net);
        $net_long = ip2long(trim($ip_arr[0]));
		if(count($ip_arr) > 1 && trim($ip_arr[1]) != '') {
			$x        = ip2long(trim($ip_arr[1]));
			$mask     = long2ip($x) == ((int) trim($ip_arr[1])) ? $x : 0xffffffff << (32 - ((int) trim($ip_arr[1])));
        } else {
			$mask=0xffffffff;
		}
		$ip_long  = ip2long($ip);

        if ($rev) {
            if (($ip_long & $mask) != ($net_long & $mask)) return true;
        } else {
            if (($ip_long & $mask) == ($net_long & $mask)) return true;
        }
    }
    return false;
}

?>
