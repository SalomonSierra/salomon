<?php

//funcion para la logtable en la base de datos
function DBDropLogTable(){
    $c=DBConnect();
    $r=DBExec($c,"drop table if exists \"logtable\"","DBDropLogTable(drop table)");
}
//funcion para crear la tabla logtable
function DBCreateLogTable(){
    $c=DBConnect();
    $conf=globalconf();
    if($conf["dbuser"]=="") $conf["dbuser"]="salomonuser";
    $r=DBExec($c,"
    CREATE TABLE \"logtable\"(
        \"lognumber\" serial,                       --(numero de id de logtable)
        \"loguser\" int4 NOT NULL,                  --(usuario involucrado con el registro)
        \"logip\" varchar(20) NOT NULL,             --(numero del ip de usuario involucrado)
        \"logdate\" int4 NOT NULL,                  --(dia/hora de registro)
        \"logtype\" varchar(20) NOT NULL,           --(tipo de registro: error,warn,info,debug)
        \"logdata\" text NOT NULL,                  --(descripcion del registro)
        \"logstatus\" varchar(20) DEFAULT '',       --(estado del registro)
        CONSTRAINT \"log_pkey\" PRIMARY KEY(\"lognumber\"),
        CONSTRAINT \"user_fk\" FOREIGN KEY (\"loguser\")
                REFERENCES \"usertable\" (\"usernumber\")
                ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )","DBCreateLogTable(create table)");

    $r=DBExec($c,"REVOKE ALL ON \"logtable\" FROM PUBLIC","DBCreateLogTable(revoke public)");
    $r=DBExec($c,"GRANT INSERT, SELECT ON \"logtable\" TO \"".$conf["dbuser"]."\"","DBCreateLogTable(grant salomonuser)");//asignando privilegio a dbuser

    $r=DBExec($c,"CREATE INDEX \"log_index\" ON \"logtable\" USING btree ".
    "(\"loguser\" int4_ops)",
    "DBCreateLogTable(create log_index)");

    $r=DBExec($c,"REVOKE ALL ON \"logtable_lognumber_seq\" FROM PUBLIC","DBCreateLogTable(revoke public seq)");
    $r=DBExec($c,"GRANT ALL ON \"logtable_lognumber_seq\" TO \"".$conf["dbuser"]."\"","DBCreateLogTable(grant salomonuser seq)");

    //creando index
}

//function prar realizar la ejecucion del insertar un nuevo registro a la tabla e error
//Ac1
function DBNewLog($user, $type, $ip, $data, $status){
    $t=time();
    $data=str_replace("'","\"", $data);
    DBExecNoSQLLog("insert into logtable (loguser, logip, logdate, logtype, ".
        "logdata, logstatus) values ($user, '$ip', $t, '$type', '$data', '$status')",
        "DBNewLog(insert log)");//db.php retona el resultado de la ejecucion del sql y realizarforzamineto de nuevo la conexcion

}
//funcion para capturar los registros ordenados
function DBGetLogs($o,$user, $type, $ip, $limit) {
	$c = DBConnect();
	$where = "";

    if($user != ""){
        $where .= "where loguser=$user";
    }
    if($type!=""){
        if($where!=""){
            $where .= " and logtype='$type'";
        }else{
            $where = "where logtype='$type'";
        }
    }
    if($ip!=""){
        if($where!=""){
            $where.=" and logip='$ip'";
        }else{
            $where="where logip='$ip'";
        }
    }


	switch ($o) {
		case "user": $order="lognumber, loguser, logdate desc"; break;
		case "type": $order="lognumber, logtype, logdate desc"; break;
		case "ip": $order="lognumber, logip, logdate desc"; break;
		default: $order="lognumber, logdate desc"; break;
	}
	$r = DBExec ($c, "select lognumber as number, loguser as user, logdate as date, " .
			"logtype as type, logip as ip, logdata as data, logstatus as status from logtable " .
			" $where order by $order limit $limit", "DBGetLogs(get logs)");
	$n = DBnlines($r);
	$a = array();
	for ($i=0;$i<$n;$i++)
		$a[$i] = DBRow($r,$i);
	return $a;
}
//realizar un update al la tabla usertable campos userssion y otros si es admin verifica el tiempo y realizar un
//update si es menor a -600 el clocktime y limpia la carpeta problemtmp
function DBLogOut($user, $isadmin=false){
    //funcion para realizar una conexcion o su es true forcenew forza de nuevo la conexcion encaso de no tener existo
    //lanza un error si existe dbclienteenc ejecuta set names encoding
    $c=DBConnect();
    $r=DBExec($c,"update usertable set usersession='',usersessionextra='', updatetime=".time().", ".
        "userlastlogout=".time()." where usernumber=$user","DBLogOut(update user)");
        //ejecuta el sql y retorna el resultado en caso de no lanza un mesaje de error y la prioridad del mesaje
    if ($isadmin) {
        //list — Asignar variables como si fueran un array
        //retorna el tiempo de la competencia en d h s cuanto falta o no esta corriendo (str y num)(2 second(s), tiempo actual)
        /*list($clockstr,$clocktime)=siteclock();//fcontest.php ...
        if($clocktime<-600){

            DBExec($c,"update problemtable set problemfullname='', problembasefilename='' where problemfullname !~ '(DEL)' and contestnumber=$contest","DBLogOut(update problems)");
            $ds=DIRECTORY_SEPARATOR;
            if($ds=="") $ds="/";
            $dir=$_SESSION["locr"].$ds."private".$ds."problemtmp".$ds;
            foreach (glob($dir.'*') as $file) {//glob - busca coincidencias de nombres de ruta con en patron
                cleardir($file,false,true);//funcion para eliminar el direcctorio junto con los archivos en recursividad
            }
        }*/
    }
    //funcion para generar el nivel de registro y prioridad para el sistema o puede
    // registar el registro a logtable si es true el $dodb, en este caso es true pero en session debe estar usertable
    LOGLevel("Usuario $user desconectado. ",2);
}
// función para iniciar sesión en un usuario. Buscará un concurso activo, comprobará en qué sitio
// local y luego busque el usuario en el sitio local del concurso activo. Además, comprueba otros
// banderas, como inicios de sesión habilitados, ip correcta, si el usuario ya inició sesión, etc.
// $ name es el nombre de usuario
// $ pasa eh la contraseña
function DBLogIn($name, $pass, $msg=true){
    ////retorna un registro de una cosulta en array asociativo en caso de no existo lanza un error
    $b=DBGetRow("select *from contesttable where contestnumber=0", 0, null, "DBLogIn(get template contest)");
    if($b == null){
        //revisa si hay contest y site y user tambien los ips de user y lo actualiza
        //retorna la informacion del usuario
        return false;
    }

    ///retorna un registro de una cosulta en array asociativo en caso de no existo lanza un error
    $a=DBGetRow("select *from usertable where username='$name'", 0, null, "DBLogIn(get user)");
    if($a == null){
        if($msg){
            //funcion para generar el nivel de registro y prioridad par el sistema y si registra a logtable
            LOGLevel("Usuario $name intento iniciar sesion pero el password es incorrecto",2);
            echo "Usuario no existe o password incorrecto";
            //MSGError("User does not exist or incorrect password");//lanza el mesaje en alert('')
        }
        return false;
    }
    if($a["userenabled"]=='f'){
        if($msg){
            //funcion para generar el nivel de registro y prioridad par el sistema y si registra a logtable
            LOGLevel("Usuario $name intento iniciar sesion esta inactivo",2);
            echo "Usuario Inactivo";
            //MSGError("User does not exist or incorrect password");//lanza el mesaje en alert('')
        }
        return false;
    }
    ////esta funcion retorna el registro de usuario y tambien si cambio o no hashpass = true
    $a =DBUserInfo($a['usernumber'],null,false);
    //$a =DBUserInfo($b["contestnumber"], 1,$a['usernumber'],null,false);
    $_SESSION['usertable']=$a;
    $_SESSION['usertable']['usersession']='';
    $_SESSION['usertable']['userip']='';

    $p=myhash($a["userpassword"].session_id());////esta funcion retorna un hash de sha256 normal
    $_SESSION['usertable']['userpassword'] = $p;

    if($a["userpassword"] !="" && $p!=$pass){
        LOGLevel("Usuario $name intento iniciar sesion pero el pasword es incorrecto.",2);
        if($msg) MSGError("Incorrect password.");//alert()
        unset($_SESSION["usertable"]);
        return false;
    }
    $gip=getIP();////funcion para capturar el ip del cliente

    $_SESSION['usertable']['usersession']=session_id();
    $_SESSION['usertable']['userip']=$gip;
    //funcion para realizar una conexcion o su es true forcenew forza de nuevo la conexcion encaso de no tener existo
    //lanza un error si existe dbclienteenc ejecuta set names encoding
    $c=DBConnect();
    $t=time();
    //DBExec() ejecuta el sql y retorna el resultado en caso de no lanza un mesaje de error y la prioridad del mesaje

    if($a["usertype"] == "team") {
        $r=DBExec($c,"update usertable set userip='".$gip."', updatetime=".time().",".
            "userlastlogin=$t, usersession='".session_id()."' where username='$name'", "DBLogIn(update session)");
    }else{
        DBExec($c,"begin work");
        $sql="update usertable set usersessionextra='".session_id()."' where username='$name' and (usersessionextra='' or userip != '".$gip."' or userlastlogin<=".($t-86400).")";
        DBExec($c,$sql);
        DBExec($c,"update usertable set userip='".$gip."', updatetime=".time().", userlastlogin=$t, ".
                "usersession='".session_id()."' where username='$name'", "DBLogIn(update user)");

        DBExec($c,"commit work");

    }
    //funcion para generar el nivel de registro y prioridad par el sistema y si registra a logtable
    LOGLevel("Usuario $name autenticado (".$gip.")",2);

    return $a;


}
//



//revisa que hayga contest y site y user tambien los ips de user y lo actualiza
//retorna la informacion del usuario
//DBLogInContest($name, $pass, $b["contestnumber"],false);
function DBLogInContest($name,$pass,$contest,$msg=true){
    ///retorna un registro de una cosulta en array asociativo en caso de no existo lanza un error
    $b = DBGetRow("select *from contesttable where contestnumber=$contest", 0, null, "DBLogIn(get active contest)");
    if ($b==null){
        /////funcion para generar el nivel de registro y prioridad par el sistema y si registra a logtable
        LOGLevel("There is no contest $contest.",0);
        if($msg) MSGError("There is no contest $contest, contact an admin.");//lanza un script mensaje aler
        return false;
    }
    //retorna el resultado de la consulta del sitio tambien con algunas
    //mejoras de sitetimetable s e si es posible o null si no hay constest y site o mesaje si es true
    /*$d=DBSiteInfo($b["contestnumber"],1,null,false);//db.php
    if($d == null){//
        if($msg) MSGError("There is no active site, contact an admin.");//lanza un mesaje de javascript alert('')
        return false;
    }*/
    ///retorna un registro de una cosulta en array asociativo en caso de no existo lanza un error
    $a=DBGetRow("select *from usertable where username='$name'", 0, null, "DBLogIn(get user)");
    if($a == null){
        if($msg){
            //funcion para generar el nivel de registro y prioridad par el sistema y si registra a logtable
            LOGLevel("Usuario $name intento iniciar sesion en la competencia $contest pero no existe.",2);
            echo "Usuario no existe o password incorrecto";
            //MSGError("User does not exist or incorrect password");//lanza el mesaje en alert('')
        }
        return false;
    }
    ////esta funcion retorna el registro de usuario y tambien si cambio o no hashpass = true
    $a =DBUserInfo($a['usernumber'],null,false);
    //$a =DBUserInfo($b["contestnumber"], 1,$a['usernumber'],null,false);
    $_SESSION['usertable']=$a;
    $_SESSION['usertable']['usersession']='';
    $_SESSION['usertable']['userip']='';

    $p=myhash($a["userpassword"].session_id());////esta funcion retorna un hash de sha256 normal
    $_SESSION['usertable']['userpassword'] = $p;
    if($d["sitepermitlogins"]=="f" && $a["usertype"]!="admin" && $a["usertype"]!="coach"){
        //funcion para generar el nivel de registro y prioridad par el sistema y si registra a logtable
        LOGLevel("Usuario $name intento iniciar sesion en la competencia $contest pero se niegan los inicios de sesion. ",2);
        if($msg) MSGError("No se permiten inicios de sesion");//mesaje de alert() JavaScript
        //unset — Destruye una variable especificada
        unset($_SESSION["usertable"]);
        return false;
    }
    if($a["userenabled"] != "t"){
        //funcion para generar el nivel de registro y prioridad par el sistema y si registra a logtable
        LOGLevel("User $name tried to log in contest $contest but it is disabled.",2);
        if($msg) MSGError("User disabled.");//alert()
        unset($_SESSION["usertable"]);
        return false;
    }
    if($a["userpassword"] !="" && $p!=$pass){
        LOGLevel("Usuario $name intento iniciar sesion en la competencia $contest pero el pasword es incorrecto.",2);
        if($msg) MSGError("Incorrect password.");//alert()
        unset($_SESSION["usertable"]);
        return false;
    }
    $gip=getIP();////funcion para capturar el ip del cliente
    if($a["userip"] !=$gip && $a["userip"] != "" && $a["usertype"] !="score"){
        LOGLevel("Usuario $name está usando dos diferentes IPs: ".$a["userip"].
            "(".dateconv($a["userlastlogin"]).") and ".$gip,1);
        if($msg && $a["usertype"] != "admin" && $a["usermultilogin"] !="t") MSGError("You are using two distinct IPs. Admin notified.");//alert()
        //dateconv date — Dar formato a la fecha/hora del parametro pasado
    }
    if($a["userpermitip"] != ""){
        $ips=explode(';',$a["userpermitip"]);
        $gips=explode(';',$gip);
        //count — Cuenta todos los elementos de un array o algo de un objeto
        if(count($gips) < count($ips)){
            IntrusionNotify("Invalid IP: ".$gip);//una notificacion de violacion de seguridad
            //como index.php
            return false;
            //ForceLoad("index.php");//...........
        }
        for($ipss=0;$ipss<count($ips);$ipss++){
            $gipi=$gips[$ipss];
            $ipi=$ips[$ipss];
            if(!match_network($ipi, $gipi)){
                IntrusionNotify("Invalid IP: ".$gip);//una notificacion de violacion de seguridad
                //como index.php
                return false;
                //ForceLoad("index.php");
            }
        }
    }
    $_SESSION['usertable']['usersession']=session_id();
    $_SESSION['usertable']['userip']=$gip;
    //funcion para realizar una conexcion o su es true forcenew forza de nuevo la conexcion encaso de no tener existo
    //lanza un error si existe dbclienteenc ejecuta set names encoding
    $c=DBConnect();
    $t=time();
    //DBExec() ejecuta el sql y retorna el resultado en caso de no lanza un mesaje de error y la prioridad del mesaje

    if($a["usertype"] == "team" && $a["usermultilogin"] !="t" && $a["userpermitip"] == "") {
        $r=DBExec($c,"update usertable set userip='".$gip."', updatetime=".time().", userpermitip='".$gip."',".
            "userlastlogin=$t, usersession='".session_id()."' where username='$name' and contestnumber=".
            $b["contestnumber"]." and usersitenumber=1", "DBLogIn(update session)");
    }else{
        DBExec($c,"begin work");
        $sql="update usertable set usersessionextra='".session_id()."' where username='$name' and contestnumber=".
            $b["contestnumber"]." and usersitenumber=1 and (usersessionextra='' or userip != '".$gip."' or userlastlogin<=".($t-86400).")";
        DBExec($c,$sql);
        DBExec($c,"update usertable set userip='".$gip."', updatetime=".time().", userlastlogin=$t, ".
                "usersession='".session_id()."' where username='$name' and contestnumber=".
                $b["contestnumber"]." and usersitenumber=1", "DBLogIn(update user)");

        DBExec($c,"commit work");

    }
    //funcion para generar el nivel de registro y prioridad par el sistema y si registra a logtable
    LOGLevel("Usuario $name autenticado (".$gip.")",2);

    return $a;
}

















?>
