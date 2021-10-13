<?php

//funcion para la tabla en la base de datos
function DBDropContestTable(){
    $c=DBConnect();
    $r=DBExec($c,"drop table if exists \"contesttable\"","DBDropContestTable(drop table)");
}
//funcion para crear la tabla competencia
function DBCreateContestTable(){
    $c=DBConnect();
    $conf=globalconf();
    if($conf["dbuser"]=="") $conf["dbuser"]="salomonuser";
    $r=DBExec($c,"
    CREATE TABLE \"contesttable\"(
        \"contestnumber\" int4 NOT NULL,            --(id de competencia)
        \"usernumber\" int4 NOT NULL,          -- (el id del creador de la competencia)
        \"contestname\" varchar(100) NOT NULL,      --(nombre de la competencia)
        \"conteststartdate\" int4 NOT NULL,           --(dia/horario de inicio)
        \"contestduration\" int4  NOT NULL,         --(duracion en segundo de la competencia)
        \"contestlastmileanswer\" int4,             --(qtd segundos a partir de inicio para responder)(hora (en seg para inicio) que este competencia deja de responder a los equipos)
        \"contestlastmilescore\" int4,             --(qtd segundos a partir de inicio para actualizar score)
        \"contestpenalty\" int4 NOT NULL,           --(qtd de segundos perdidos para cada ejecucio erronea)
        \"contestmaxfilesize\" int4 NOT NULL,       --(tamaño max en bytes de los archivos subidos)
        \"contestactive\" bool NOT NULL,            --(indica se la competencia esta activa)
        \"contestprivate\" bool NOT NULL,            --(indica se la competencia es privada o publica)
        \"contestpassword\" varchar(50) DEFAULT '' NOT NULL, --(pasword para competencia privada)
        \"contestactiveresult\" bool NOT NULL,        --(indica si en la competencia se puede ver resultados)

        \"contestip\" varchar(200) NOT NULL,                  -- (ip publico del servidor del contest)
        \"contestpermitlogins\" bool NOT NULL,                   -- (se aceptan inicios de session?)
        \"contestautoend\" bool,                                       -- (? para auto finalizacion)
        \"contestjudging\" text,                                       -- (indica que sitios se evaluan en este sitio)
        \"contestglobalscore\" varchar(50) DEFAULT '' NOT NULL,        -- (indica si este sitio debe mostrar una puntuacion global)
        \"contestscorelevel\" int4 DEFAULT 0 NOT NULL,                 -- (indica el nivel de detalles del marcador que se muestra a los equipos)
        \"contestnextuser\" int4 DEFAULT 0 NOT NULL,
        \"contestnextrun\" int4 DEFAULT 0 NOT NULL,
        \"contestautojudge\" bool DEFAULT 'f',
        \"contestmaxruntime\" int4 DEFAULT 600 NOT NULL,
        \"contestmaxjudgewaittime\" int4 DEFAULT 900 NOT NULL,

        \"updatetime\" int4 DEFAULT EXTRACT(EPOCH FROM now()) NOT NULL, --(indica la ultima actualizacion)

        CONSTRAINT \"contest_pkey\" PRIMARY KEY (\"contestnumber\"),
        CONSTRAINT \"user_fk\" FOREIGN KEY(\"usernumber\") REFERENCES \"usertable\" (\"usernumber\")
                ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )","DBCreateContestTable(create table)");


    $r=DBExec($c,"REVOKE ALL ON \"contesttable\" FROM PUBLIC","DBCreateConstestTable(revoke public)");
    $r=DBExec($c,"GRANT ALL ON \"contesttable\" TO \"".$conf["dbuser"]."\"","DBCreateContestTable(grant salomonuser)");//asignando privilegio a dbuser
    $r=DBExec($c,"CREATE INDEX \"contest_index\" ON \"contesttable\" USING btree ".
    "(\"contestnumber\" int4_ops,\"usernumber\" int4_ops)","DBCreateContestTable(create index)");
    //$r=DBExec($c,"CREATE INDEX \"contest_index2\" ON \"contesttable\" USING btree ".
    //    "(\"usernumber\" int4_ops, \"contestname\" varchar_ops)","DBCreateContestTable(create contest_index2)");
    //creando index
    //CAMPOS ELIMINADOS
    //\"contestlocalsite\" int4 NOT NULL,         --(id del sitio local de la competencia a este servidor)
    //\"contestmainsite\" int4 NOT NULL,          --(id del sitio principal contest)
    //\"contestkeys\" text NOT NULL,              --(list de llaves de la competencia)
    //\"contestunlockkey\" varchar(100) NOT NULL, --(key para decritar archivos del problema)
    //\"contestmain.siteurl\" varchar(200) NOT NULL, --(id de sitio principal de la competencia url)

}
//function para eliminar la tabla sitetable de la base de datos
function DBDropSiteTable(){
    $c=DBConnect();
    $r=DBExec($c,"drop table if exists \"sitetable\"","DBDropSiteTable(Drop table)");
}
//funcion para crear la tabla sitetable en la base de datos
function DBCreateSiteTable(){
    $c=DBConnect();
    $conf=globalconf();
    if($conf["dbuser"]=="") $conf["dbuser"]="salomonuser";
    $r=DBExec($c, "
    CREATE TABLE \"sitetable\" (
        \"contestnumber\" int4 NOT NULL,                            -- (id de competencia)
        \"sitenumber\" int4 NOT NULL,                               -- (id de local)
        \"siteip\" varchar(200) NOT NULL,                           -- (ip publico del servidor del sitio)
        \"sitename\" varchar(50) NOT NULL,                          -- (nombre de local)
        \"siteactive\" bool NOT NULL,                               -- (sitio esta activo?)
        \"sitepermitlogins\" bool NOT NULL,                         -- (se aceptan inicios de session?)
        \"sitelastmileanswer\" int4,                      -- (hora (en seg para inicio) que este sitio deja de responder a los equipos)
        \"sitelastmilescore\" int4,                                 -- (tiempo (en segundos desde el principio) que el marcador se congela en este sitio)
        \"siteduration\" int4,                                      -- (tiempo de competencia en segundos)
        \"siteautoend\" bool,                                       -- (?)
        \"sitejudging\" text,                                       -- (indica que sitios se evaluan en este sitio)

        \"siteglobalscore\" varchar(50) DEFAULT '' NOT NULL,        -- (indica si este sitio debe mostrar una puntuacion global)
        \"sitescorelevel\" int4 DEFAULT 0 NOT NULL,                 -- (indica el nivel de detalles del marcador que se muestra a los equipos)
        \"sitenextuser\" int4 DEFAULT 0 NOT NULL,

        \"sitenextrun\" int4 DEFAULT 0 NOT NULL,


        \"updatetime\" int4 DEFAULT EXTRACT(EPOCH FROM now()) NOT NULL, -- (indica la ultimo actualicion del registro)
        \"sitechiefname\" varchar(20) DEFAULT '' NOT NULL,          -- (el nombre de usuario del juez principal, si lo hubiera)
    -- (esta tabla contiene un fila para cada sitio web del concurso)
        \"siteautojudge\" bool DEFAULT 'f',
        \"sitemaxruntime\" int4 DEFAULT 600 NOT NULL,
        \"sitemaxjudgewaittime\" int4 DEFAULT 900 NOT NULL,
        CONSTRAINT \"site_pkey\" PRIMARY KEY (\"contestnumber\",\"sitenumber\"),
        CONSTRAINT \"contest_fk\" FOREIGN KEY(\"contestnumber\") REFERENCES \"contesttable\" (\"contestnumber\")
                ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )","DBCreateSiteTable(create table)");
        $r=DBExec($c,"REVOKE ALL ON \"sitetable\" FROM PUBLIC","DBCreateSiteTable(revoke public)");
        $r=DBExec($c,"GRANT ALL ON \"sitetable\" TO \"".$conf["dbuser"]."\"","DBCreateSiteTable(grant salomonuser)");//asignando privilegio a dbuser
        $r=DBExec($c,"CREATE UNIQUE INDEX \"site_index\" ON \"sitetable\" USING btree ".
        "(\"contestnumber\" int4_ops,\"sitenumber\" int4_ops)","DBCreateSiteTable(create index)");

}
//funcion para eliminar la tabla contesttimetable
function DBDropContestTimeTable(){
    $c=DBConnect();
    $r=DBExec($c,"drop table if exists \"contesttimetable\"","DBDropContestTimeTable(Drop table)");
}
//funcion para create la tabla sitetimetable
function DBCreateContestTimeTable(){
    $c=DBConnect();
    $conf=globalconf();
    if($conf["dbuser"]=="") $conf["dbuser"]="salomonuser";
    $r=DBExec($c, "
    CREATE TABLE \"contesttimetable\" (
        \"contestnumber\" int4 NOT NULL,                            -- (id de competencia)

        \"conteststartdate\" int4 NOT NULL,                            -- (hora en que comenzo la competencia)
        \"contestenddate\" int4 NOT NULL,                              -- (hora local debe terminar coro no terminado)
        \"updatetime\" int4 DEFAULT EXTRACT(EPOCH FROM now()) NOT NULL,     -- (indica la ultima actulizacion del registro)
    -- (esta tabla contiene una fila para cada reinicio de un sitio de concurso)
        CONSTRAINT \"contesttime_pkey\" PRIMARY KEY (\"contestnumber\", \"conteststartdate\"),
        CONSTRAINT \"contest_fk\" FOREIGN KEY(\"contestnumber\")
                REFERENCES \"contesttable\" (\"contestnumber\")
                ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )","DBCreateContestTimeTable(create table)");
        $r=DBExec($c,"REVOKE ALL ON \"contesttimetable\" FROM PUBLIC","DBCreateContestTimeTable(revoke public)");
        $r=DBExec($c,"GRANT ALL ON \"contesttimetable\" TO \"".$conf["dbuser"]."\"","DBCreateContestTimeTable(grant salomonuser)");//asignando privilegio a dbuser
        $r=DBExec($c,"CREATE UNIQUE INDEX \"contesttime_index\" ON \"contesttimetable\" USING btree ".
        "(\"contestnumber\" int4_ops,\"conteststartdate\" int4_ops)",
        "DBCreateContestTimeTable(create index)");
        $r=DBExec($c,"CREATE INDEX \"contesttimecontest_index\" ON \"contesttimetable\" USING btree ".
            "(\"contestnumber\" int4_ops)","DBCreateContestTimeTable(create contest_index)");
        //creando index

}
//funcion para eliminar la tabla sitetimetable
function DBDropSiteTimeTable(){
    $c=DBConnect();
    $r=DBExec($c,"drop table if exists \"sitetimetable\"","DBDropSiteTimeTable(Drop table)");
}
//funcion para create la tabla sitetimetable
function DBCreateSiteTimeTable(){
    $c=DBConnect();
    $conf=globalconf();
    if($conf["dbuser"]=="") $conf["dbuser"]="salomonuser";
    $r=DBExec($c, "
    CREATE TABLE \"sitetimetable\" (
        \"contestnumber\" int4 NOT NULL,                            -- (id de competencia)
        \"sitenumber\" int4 NOT NULL,                               -- (id de local)
        \"sitestartdate\" int4 NOT NULL,                            -- (hora en que comenzo la competencia)
        \"siteenddate\" int4 NOT NULL,                              -- (hora local debe terminar coro no terminado)
        \"updatetime\" int4 DEFAULT EXTRACT(EPOCH FROM now()) NOT NULL,     -- (indica la ultima actulizacion del registro)
    -- (esta tabla contiene una fila para cada reinicio de un sitio de concurso)
        CONSTRAINT \"sitetime_pkey\" PRIMARY KEY (\"contestnumber\", \"sitenumber\", \"sitestartdate\"),
        CONSTRAINT \"site_fk\" FOREIGN KEY(\"contestnumber\", \"sitenumber\")
                REFERENCES \"sitetable\" (\"contestnumber\", \"sitenumber\")
                ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )","DBCreateSiteTimeTable(create table)");
        $r=DBExec($c,"REVOKE ALL ON \"sitetimetable\" FROM PUBLIC","DBCreateSiteTimeTable(revoke public)");
        $r=DBExec($c,"GRANT ALL ON \"sitetimetable\" TO \"".$conf["dbuser"]."\"","DBCreateSiteTimeTable(grant salomonuser)");//asignando privilegio a dbuser
        $r=DBExec($c,"CREATE UNIQUE INDEX \"sitetime_index\" ON \"sitetimetable\" USING btree ".
        "(\"contestnumber\" int4_ops, \"sitenumber\" int4_ops,\"sitestartdate\" int4_ops)",
        "DBCreateSiteTimeTable(create index)");
        $r=DBExec($c,"CREATE INDEX \"sitetimesite_index\" ON \"sitetimetable\" USING btree ".
            "(\"contestnumber\" int4_ops, \"sitenumber\" int4_ops)","DBCreateSiteTimeTable(create site_index)");
        //creando index

}
//funcion para eliminar la tabla usertable
function DBDropUserTable(){
    $c=DBConnect();
    //drop table if exists libros;
    $r=DBExec($c,"drop table if exists \"usertable\"","DBDropUserTable(drop table)");
}
//function para crear la tabla usertable
function DBCreateUserTable(){
    $c=DBConnect();
    $conf=globalconf();
    if($conf["dbuser"]=="") $conf["dbuser"]="salomonuser";
//nosotros no tenemos contest number todavia
    $r=DBExec($c, "
    CREATE TABLE \"usertable\" (
        \"usernumber\" int4 NOT NULL,                       -- (id de usuario)
        \"username\" varchar(20) NOT NULL,                  -- (nombre del usuario)
        \"userfullname\" varchar(200) NOT NULL,             -- (nombre completo del usuario or nick_name )
        \"useremail\" varchar(100) NOT NULL,                -- (email de usuario)
        \"userdesc\" varchar(300),                          -- (descripcion del usuario or university)
        \"usertype\" varchar(20) NOT NULL,                  -- (system, coach, competidor)
        \"userenabled\" bool DEFAULT 't' NOT NULL,          -- (usuario activo)
        \"usermultilogin\" bool DEFAULT 'f' NOT NULL,       -- (el usuario puede loguearse multiples veces)
        \"userpassword\" varchar(200) DEFAULT '',           -- (contrasena)
        \"userip\" varchar(300),                            -- (ip del ultimo acceso)
        \"userlastlogin\" int4,                             -- (dato en segundo la ultima session)
        \"usersession\" varchar(50) DEFAULT '',             -- (sesion del usuario)
        \"usersessionextra\" varchar(50) DEFAULT '',        -- (sesion del usuario)
        \"userlastlogout\" int4,                            -- (dato en segundo desde la ultima salida)
        \"userpermitip\" varchar(300),                      -- (ip permitido para el acceso)
        \"userinfo\" varchar(300) DEFAULT '',               -- (informacion del usuario)
        \"updatetime\" int4 DEFAULT EXTRACT(EPOCH FROM now()) NOT NULL, -- (indica la ultima actualizacion del registro)
    -- (esta tabla contiene una fila para cada usuario, ya sea administrador, juez o equipo. )
        CONSTRAINT \"user_pkey\" PRIMARY KEY (\"usernumber\")
    )","DBCreateUserTable(create table)");
    $r=DBExec($c,"REVOKE ALL ON \"usertable\" FROM PUBLIC","DBCreateUserTable(revoke public)");
    $r=DBExec($c,"GRANT ALL ON \"usertable\" TO \"".$conf["dbuser"]."\"","DBCreateUserTable(grant salomonuser)");
    $r=DBExec($c,"CREATE UNIQUE INDEX \"usernumber_index\" ON \"usertable\" USING btree ".
    "(\"usernumber\" int4_ops, \"username\" varchar_ops)",
    "DBCreateUserTable( create user_index)");

}
////////.....DATOS PARA EL COMPETENCIA CERO, INICIALIZACION DEL SISTEMA----/////////
function DBFakeContest(){
    $c=DBConnect();
    DBExec($c,"begin work");
    $cf=globalconf();
    $pass= myhash($cf["basepass"]);
    DBExec($c,"insert into usertable (usernumber, username, userfullname, useremail, ".
        "userdesc, usertype, userenabled, usermultilogin, userpassword, userip, userlastlogin, usersession, ".
        "userlastlogout, userpermitip) ".
        "values (0, 'admin', 'Administrador', 'juezsalomon@gmail.com', NULL, 'admin', 't', ".
            "'t', '$pass', NULL, NULL, '', NULL, NULL)","DBFakeContest(insert admin user)");

    DBExec($c,"insert into contesttable (contestnumber, usernumber, contestname, conteststartdate, contestduration, ".
    "contestlastmileanswer, contestlastmilescore, contestpenalty, contestmaxfilesize, ".
    "contestactive, contestprivate, contestactiveresult, contestip, contestpermitlogins, contestautoend, contestjudging, ".
    "contestglobalscore, contestscorelevel, contestautojudge) ".
    "values (0,0,'competencia falso (solo para propositos iniciales)', ".
            "EXTRACT(EPOCH FROM now()), ".
            "0, 0, 0, 20*60, 100000, 't', 'f', 't', '127.0.0.1/salomon', 't', 't', '0', '0', 4, 't')","DBFakeContest(insert contest)");

    /*DBExec($c, "insert into sitetable (contestnumber, sitenumber, siteip, sitename, siteactive, sitepermitlogins, ".
        "sitelastmileanswer, sitelastmilescore, siteduration, siteautoend, sitejudging, ".
        "siteglobalscore, sitescorelevel) ".
        "values (0, 1, '', 'Fake Site (just for initial purposes)', ".
                "'t', 't', 0, 0, 1, 't', '1', ".
                "'0', 4)","DBFakeContest(insert site)");*/

    $param['contest']=0;
    //$param['user']=0;//site 1
    $param["start"]=1;
    DBRenewContestTime($param, $c);//funcion para verificar los datos de contest,site,sitetimetable, si hay registro en la tabla inserta new
    //DBRenewSiteTime($param, $c);//funcion para verificar los datos de contest,site,sitetimetable, si hay registro en la tabla inserta new

    insertanswers($c);////funcion para registrar todas las respuestas posibles en una competencia
    insertlanguages($c);//insertamos a la tabla langtable 5 lenguajes principales
    //DBinsertfakeproblem($n,$c);

    DBExec($c,"commit work");
}

//saca todos los username de los usuario guardando en un matriz $a[i][usersitenu-usernumber]='username'
//modificado abajo
/*function DBAllUserNames($contest=-1,$site=-1) {
	$sql = "select * from usertable ";//modificado
    if($contest > -1) $sql.= "where contestnumber=$contest ";
    if($site > 0) $sql .= "and usersitenumber=$site ";
	$c = DBConnect();
	$r = DBExec ($c, $sql, "DBAllUserNames(get users)");
	$n = DBnlines($r);
	if ($n == 0) {
		LOGError("Unable to find users in the database. SQL=(" . $sql . ")");
		MSGError("Unable to find users in the database!");
	}

	$a = array();
	for ($i=0;$i<$n;$i++) {
	  $tmp = DBRow($r,$i);
      //$a[usersitenu-usernumber]=username;
	  $a[$tmp['usersitenumber'] . '-' . $tmp['usernumber']] = $tmp['username'];
	}
	return $a;
}*/
function DBAllUserNames($contest=-1) {

    if($contest>-1){
        $sql = "select distinct usernumber from runtable where contestnumber=$contest";
    }else{
        $sql = "select distinct usernumber from runtable";//modificado
    }

	$c = DBConnect();
	$r = DBExec ($c, $sql, "DBAllUserNames(get users)");
	$n = DBnlines($r);
	if ($n == 0) {
		LOGError("Aun los usuatio no enviaron a la competencia. SQL=(" . $sql . ")");
		MSGError("Aun los usuatio no enviaron a la competencia.!");
	}

	$a = array();
	for ($i=0;$i<$n;$i++) {
        $ru=DBRow($r,$i);
        $sql="select *from usertable where usernumber=".$ru["usernumber"];
        $u = DBExec ($c, $sql, "DBAllUserNames(get user)");
        $tmp = DBRow($u,0);
      //$a[usersitenu-usernumber]=username;
	  $a[$tmp['usernumber']] = $tmp['username'];
	}
    //para el entrenador
    if($contest==-1)
        return $a;
    $sql="select distinct usernumber from contesttable where contestnumber=$contest";
    $r = DBExec ($c, $sql, "DBAllUserNames(get chief)");
	$n = DBnlines($r);
	if ($n == 0) {
		LOGError("el creador de la competencia no registrado en la base de datos SQL=(" . $sql . ")");
		MSGError("el creador de la competencia no registrado en la base de datos.");
	}else{
        $ru=DBRow($r,0);
        $sql="select *from usertable where usernumber=".$ru["usernumber"];
        $u = DBExec ($c, $sql, "DBAllUserNames(get user)");
        $tmp = DBRow($u,0);
      //$a[usersitenu-usernumber]=username;
	   $a[$tmp['usernumber']] = $tmp['username'];
    }
    //para usuario admin
    $sql="select *from usertable where usernumber=0";
    $u = DBExec ($c, $sql, "DBAllUserNames(get user admin)");
    $tmp = DBRow($u,0);
  //$a[usersitenu-usernumber]=username;
    $a[$tmp['usernumber']] = $tmp['username'];


	return $a;
}
//seleccion la todos los usuario de la base de datos si pasa sitio de ese
function DBAllUserInfo() {

	$sql = "select * from usertable where usernumber!=0 ";

	$sql .= "order by usernumber";
	$c = DBConnect();
	$r = DBExec ($c, $sql, "DBAllUserInfo(get users)");
	$n = DBnlines($r);
	if ($n == 0) {
		LOGError("Unable to find users in the database. SQL=(" . $sql . ")");
		MSGError("¡No se pueden encontrar usuarios en la base de datos!");
	}

	$a = array();
	for ($i=0;$i<$n;$i++) {
		$a[$i] = DBRow($r,$i);
		$a[$i]['changepassword']=true;
		if(substr($a[$i]['userpassword'],0,1)=='!') {
			$a[$i]['userpassword'] = substr($a[$i]['userpassword'],1);
			$a[$i]['changepassword']=false;
		}
		$a[$i]['userpassword'] = myhash($a[$i]['userpassword'] . $a[$i]['usersessionextra']);
	}
	return $a;
}
//retorna toda la informacion del de la tabla sitetimetable pasando los parametros de contest y sitio
function DBAllSiteTime($contest, $site) {
	$sql = "select * from sitetimetable where contestnumber=$contest and sitenumber=$site order by sitestartdate";
	$c = DBConnect();
	$r = DBExec ($c, $sql, "DBAllSiteTime(get times)");
	$n = DBnlines($r);
	if ($n == 0) {
		LOGError("Unable to find Site times in the database. SQL=(" . $sql . ")");
		MSGError("Unable to find site times in the database!");
	}

	$a = array();
	for ($i=0;$i<$n;$i++) {
		$a[$i] = DBRow($r,$i);
	}

	return $a;
}

//(contest,site,usernumber,c=null,hashpass=true)
//esta funcion retorna la info. del usuario, si hashpass es true userpassword= userpassword.usersessionextra
//ac1
function DBUserInfo($user, $c=null, $hashpass=true){
    $sql="select *from usertable where usernumber=$user";
    $a=DBGetRow($sql, 0, $c);//retorna un registro de una cosulta en array asociativo en caso de no existo lanza un error
    if($a==null){
        LOGError("Unable to find the user in the database. SQL=(".$sql.")");//funcion para generar el nivel de registro y prioridad para el sistema nivel 0
        MSGError("Unable to find the user in the database. Contact an admin now!");//lanza un script mensaje aler
    }
    $a['changepassword']=true;//es para cambiar el password
    if(substr($a['userpassword'],0,1)=='!'){
        $a['userpassword']=substr($a['userpassword'],1);//echo substr('abcdef', 1);     bcdef
        $a['changepassword']=false;
    }
    if($hashpass)
        $a['userpassword']=myhash($a['userpassword'].$a['usersessionextra']);//retorna un hash de sha256 normal
    return cleanuserdesc($a);//retorna con la modificacion del descuser separando si en caso que exista
}
//por name
function DBUserInfoName($user, $c=null){
    $sql="select *from usertable where username='$user' or useremail='$user'";
    $a=DBGetRow($sql, 0, $c);//retorna un registro de una cosulta en array asociativo en caso de no existo lanza un error
    if($a==null){
        LOGError("Unable to find the user in the database. SQL=(".$sql.")");//funcion para generar el nivel de registro y prioridad para el sistema nivel 0
        MSGError("Unable to find the user in the database. Contact an admin now!");//lanza un script mensaje aler
    }

    return cleanuserdesc($a);//retorna con la modificacion del descuser separando si en caso que exista
}
//function que retorna con la modificacion del descuser separando si en caso que exista
function cleanuserdesc($a){

    $inst=explode(']',$a['userdesc']);// unsxx  [unsxx][umsa]
    $a['userflag']='';
    $a['usershortinstitution']='';
    $a['usersitename']='';
    if(isset($inst[1])){
        $inst2=explode('[',$inst[0]);//delimitador devuelve un array
        if(isset($inst2[1]))
            $a['usershortinstitution']=trim($inst2[1]);
        if(isset($inst[2])){
            $a['userdesc']=trim($inst[2]);
            $inst=explode('[',$inst[1]);
            if(isset($inst[1])){
                $inst2=explode(',',trim($inst[1]));
                $a['userflag']=strtolower($inst2[0]);
                //if(isset($inst2[1])) $a['usersitename']=strtoupper(trim($inst2[1]));
            }
        }else{
            $a['userdesc']=trim($inst[1]);
        }
    }
    return $a;
}
//si es el mismo retorna false, hace update a usertable userenabled='f' y algunos
//campos si existe en runtable status a deleted nueva tarea old tasktable answertable problemtable
function DBDeleteUser($user){

    if ($user==$_SESSION["usertable"]["usernumber"]) return false;
	$c = DBConnect();
	DBExec($c, "begin work");
	DBExec($c, "lock table usertable");
	$sql = "select * from usertable where usernumber=$user for update";
	$a = DBGetRow ($sql, 0, $c);
	if ($a != null) {

        $sql = "update usertable set userenabled='f', userlastlogin=NULL, usersessionextra='', usersession='', updatetime=".time(). " where usernumber=$user";
		//		$sql = "delete from usertable where usernumber=$user and usersitenumber=$site and " .
		//     "contestnumber=$contest";
		DBExec ($c, $sql);
		$r = DBExec($c,"select runnumber as number, contestnumber as contest from runtable where usernumber=$user for update");
		$n = DBnlines($r);

		for ($i=0;$i<$n;$i++) {
		  $a = DBRow($r,$i);
          //DBRunDelete actualizar la tabla runtable a status =deleted y guarda una nueva tarea en tasktable
          //obtenido datos de tabla problemtable y answertable del mismo usuario
		  if(DBRunDelete($a["number"],$a["contest"],$_SESSION["usertable"]["usernumber"],$c) === false) {
		    DBExec($c, "rollback work");
		    LOGLevel("User $user (contest=".$a["contest"].") could not be removed (run delete error).", 1);
		    return false;
		  }
		}
		DBExec($c, "commit work");
		LOGLevel("User $user marked as inactive.", 1);

        return true;
	} else {
		DBExec($c, "rollback work");
		LOGLevel("User $user could not be removed.", 1);
		return false;
	}
}

//(contest,site, c=null,msg=true)
//retorna el resultado de un contest y sitio dado mas la info. de siterunning, currenttime,siteendeddate
//de la tabla sitimetable;
function DBSiteInfo($contest, $site, $c=null, $msg=true){
    $sql = "select *from sitetable where sitenumber=$site and contestnumber=$contest";
    if($c==null) $c=DBConnect();//realizar conexcion su no existe conexcion
    $r=DBExec($c, $sql);//ejecuta el sql y retorna el resultado en caso de no lanza un mesaje de error y la prioridad del mesaje
    //cuenta el numero de filas del resultado sql
    if(DBnlines($r)<1){
        if($msg){
            //funcion para generar el nivel de registro y prioridad para el sistema nivel 0
            LOGError("Unable to find the site in the database (site=$site, contest=$contest). SQL=(".$sql.")");
            MSGError("Unable to find the site in the database. contact an admin now!");//lanza un mesaje en javascript
        }else{
            return null;
        }
    }
    $a=DBRow($r, 0);//retorna en array asocciotivo dando el resutado de la consulta y posicion
    $sql="select sitestartdate as s, siteenddate as e from sitetimetable ".
        "where sitenumber=$site and contestnumber=$contest order by sitestartdate";
    $r=DBExec($c, $sql);//ejecuta el sql y retorna el resultado en caso de no lanza un mesaje de error y la prioridad del mesaje
    $n=DBnlines($r);//retorna el numero de filas que tiene la consulta
    $a["currenttime"]=0;//tiempo actual
    $a["siterunning"]=false;//sitio corriendo
    $ti=time();
    for($i=0;$i<$n;$i++){
        $b=DBRow($r,$i);
        if($i==0) $a["sitestartdate"]=$b["s"];//se guarda junto con la info del sitio
        if($b["e"]==0){
            $a["siterunning"]=true;//currenttime == a tiempo actual
            $a["currenttime"]+=$ti-$b["s"];//tiempo que falta para la competencia
        }else{
            $a["currenttime"]+=$b["e"]-$b["s"];//tiempo que falta para finalizar o 5 horas de contest
        }
        $a["siteendeddate"]=$b["e"];
    }
    if($a["siteendeddate"] == 0)
        $a["siteendeddate"] = $ti + $a["siteduration"] - $a["currenttime"];
    $a["siteautoended"] = false;
    if($a["siteautoend"] == "t" && $a["currenttime"] >= $a["siteduration"]){
        $a["siterunning"] = false;
        $a["siteautoended"] = true;
    }

    return $a;
}
//funcion para actulizar la tabla usertable usersessionextra,usersession a vacio y userlastlogout a time()
function DBSiteLogoffAll($contest, $site) {
	$c = DBConnect();
	DBExec($c, "begin work");
	$r = DBExec($c,"update usertable set usersessionextra='', usersession='', updatetime=".time()." where usertype!='admin' and " .
			"contestnumber=$contest and usersitenumber=$site");
	$r = DBExec($c,"update usertable set userlastlogout=".time()." where usertype!='admin' and " .
			"contestnumber=$contest and usersitenumber=$site and (userlastlogin>userlastlogout or " .
			"(userlastlogout is null and userlastlogin is not null))");
	DBExec($c, "commit work");

	LOGLevel("Logoff all (contest=$contest,site=$site).",2);
}
//funcion para sacar toda la informacion de una competencia dada
function DBAllSiteInfo($contest, $c=null){
    $sql ="select *from sitetable where contestnumber=$contest";
    if($c==null)
        $c=DBConnect();
    $r=DBExec($c,$sql);
    $n=DBnlines($r);
    if($n==0){
        LOGError("Unable to find sites in the database.l    sql=(".$sql.")");
        MSGError("Unable to find sites in the database!");
    }
    $a=array();
    for($i=0;$i<$n;$i++){
        $a[$i]=DBRow($r,$i);
    }
    return $a;
}
//retorna todas las competencias
function DBAllContestInfo(){
    $usernumber=$_SESSION["usertable"]["usernumber"];
    if($usernumber==0){
        $sql="select * from contesttable where contestnumber!=0 order by contestnumber desc";
    }else{
        $sql="select *from contesttable where usernumber=$usernumber and contestnumber!=0 order by contestnumber desc";
    }

    $c=DBConnect();
    $r=DBExec($c,$sql);
    $n=DBnlines($r);
    /*if($n==0){
        LOGError("Unable to find contests in the database. SQL=(".$sql.")");
        MSGError("Unable to find contests in the database!");
    }*/
    $a=array();
    for ($i=0; $i < $n; $i++) {
        $a[$i]=DBRow($r,$i);
    }
    return $a;
}
//funcion para actualizar el sitio con sitepermitlogins=$logins y obtenido el info del sitio
function DBSiteLogins ($contest, $site, $logins) {
    //retorna el resultado de la consulta del sitio tambien con algunas
    //mejoras de sitetimetable s e si es posible o null si no hay constest y site o mesaje si es true
	if(($s = DBSiteInfo($contest, $site)) == null)
		LOGError("DBSiteLogins: cant read site (contest=$contest,site=$site)");//no puedo leer el sitio

	$param = $s;
	$param['contestnumber']=$contest;
	$param['sitenumber']=$site;
	$param['sitepermitlogins']=$logins;
	$param['updatetime']= -1;
    //actualizar todos los campos de sitetable
	DBUpdateSite ($param);
	LOGLevel("Site logins=$logins (contest=$contest,site=$site)",2);
}
/*//funcion para eliminar todos los registro de la tabla clartable dado contest, site.
//tambien actualiza la table sitetable sitenextclar=0
//numbercontest, -1, usernumber,usersite
//numbercontest, 1, usernumber,usersitenumber
function DBSiteDeleteAllClars ($contest, $site, $user, $usersite, $c=null) {
	$cw=false;
	if($c==null) {
		$cw=true;
        $c = DBConnect();
        DBExec($c, "begin work");
	}
	DBExec($c, "lock table sitetable");
	DBExec($c, "lock table clartable");
	DBExec($c, "select * from sitetable where contestnumber=$contest and sitenumber=$site for update");
	$r = DBExec($c, "select * from clartable as c where c.contestnumber=$contest " .
				" and (c.clarsitenumber=$site or $site < 0) for update");
	DBExec($c, "delete from clartable where contestnumber=$contest and (clarsitenumber=$site or $site < 0)");
	DBExec($c, "update sitetable set sitenextclar=0, updatetime=".time()." " .
		   "where contestnumber=$contest and (sitenumber=$site or $site < 0)");
	if($cw) {
        DBExec($c, "commit work");
        //Todas las aclaraciones eliminadas
		LOGLevel("All Clarifications deleted (site=$site, contest=$contest, user=$user(site=$usersite)).", 3);
	}
	return true;
}*/
/*
////funcion para vaciar la tabla tasktable teniendo en cuenta contest site user
//usersite tambien actualizar sitetable sitenexttask=0
function DBSiteDeleteAllTasks($contest, $site, $user, $usersite, $c=null){
    $cw=false;
    if($c==null){
        $cw=true;
        $c = DBConnect();
        DBExec($c,"begin work");
    }

    DBExec($c, "lock table sitetable");
	DBExec($c, "lock table tasktable");
    DBExec($c,"select *from sitetable where contestnumber=$contest and sitenumber=$site for update");
    $r=DBExec($c,"select *from tasktable as t where t.contestnumber=$contest ".
        " and (t.sitenumber=$site or $site < 0) for update");
    DBExec($c,"delete from tasktable where contestnumber=$contest and (sitenumber=$site or $site < 0)");
    DBExec($c,"update sitetable set sitenexttask=0, updatetime=".time()." ".
        "where contestnumber=$contest and (sitenumber=$site or $site < 0)");
    if($cw){
        DBExec($c,"commit work");//aclaraciones
        LOGLevel("All Tasks deleted (site=$site, contest=$contest, user=$user(site=$usersite)).",3);
    }
    return true;
}
*/
/*
//para eliminar todos los registros de la tabla bkptable enviado el contest y el site
function DBSiteDeleteAllBkps ($contest, $site, $user, $usersite,$c=null) {
	$cw=false;
	if($c==null) {
		$cw=true;
        $c = DBConnect();
        DBExec($c, "begin work");
	}
	DBExec($c, "lock table bkptable");
	$r = DBExec($c, "select bkpdata from bkptable where contestnumber=$contest and (sitenumber=$site or $site < 0) and bkpstatus='active'");
	$n = DBnlines($r);
	for ($i=0;$i<$n;$i++) {
		$a = DBRow($r,$i);
        //pg_lo_unlink () elimina un objeto grande con la oid . Rendimientos true en caso de éxito o false fracaso.
        //Para utilizar la interfaz de objetos grandes, es necesario encerrarla dentro de un bloque de transacciones.
		DB_lo_unlink($c,$a["bkpdata"]);
	}
	DBExec($c, "delete from bkptable where contestnumber=$contest and (sitenumber=$site or $site < 0)");
	if($cw) {
		DBExec($c, "commit work");
		LOGLevel("All Bkps deleted (site=$site, contest=$contest, user=$user(site=$usersite)).", 3);
	}
	return true;
}*/
//funcion para vaciar la tabla runtable teniendo en cuenta contest site user usersite tambien actualizar sitetable sitenextrun=0
function DBSiteDeleteAllRuns($contest, $site, $user, $usersite, $c=null){
    $cw=false;
    if($c==null){
        $cw=true;
        $c=DBConnect();
        DBExec($c,"begin work");
    }
    DBExec($c,"lock table sitetable");
    DBExec($c,"lock table runtable");
    DBExec($c,"select *from sitetable where contestnumber=$contest and (sitenumber=$site or $site < 0) for update");
    $sql="select *from runtable as r where r.contestnumber=$contest and ".
        "r.runsitenumber=$site";
    $r=DBExec($c,$sql." for update");
    DBExec($c,"delete from runtable where contestnumber=$contest and (runsitenumber=$site or $site < 0)");
    DBExec($c,"update sitetable set sitenextrun=0, updatetime=".time()." ".
        "where contestnumber=$contest and (sitenumber=$site or $site < 0)");
    if($cw){
        DBExec($c,"commit work");
        LOGLevel("All Runs deleted (site=$site, contest=$contest, user=$user(site=$usersite)).",3);
    }
    return true;
}
//actualizar todos los campos de sitetable
function DBUpdateSite($param, $c=null){
    $ac=array('contestnumber','sitenumber','sitename','sitepermitlogins','sitescorelevel');
    $ac1=array('updatetime','siteautoend','siteglobalscore','siteip','siteactive','siteduration','sitelastmileanswer','sitelastmilescore',
            'siteautojudge','sitenextuser','sitenextrun','sitechiefname','sitejudging');

    if(isset($param['number']) && !isset($param['sitenumber'])) $param['sitenumber']=$param['number'];
    $type['contestnumber']=1;
    $type['sitenumber']=1;
    $type['updatetime']=1;
    $type['siteduration']=1;
    $type['sitelastmilescore']=1;
    $type['sitelastmileanswer']=1;
    $type['sitenextuser']=1;

    $type['sitenextrun']=1;


    $type['sitescorelevel']=1;
    foreach ($ac as $key) {
        if(!isset($param[$key])){
            MSGError("DBUpdateSite param error: $key is not set");
            return false;
        }
        $$key=myhtmlspecialchars($param[$key]);
        if(isset($type[$key]) && !is_numeric($param[$key])){
            MSGError("DBUpdateSite param error: $key is not numeric");
            return false;
        }
    }
    $siteduration=-1;
    $sitelastmileanswer=-1;
    $sitelastmilescore=-1;
    $sitenextuser=-1;

    $sitenextrun=-1;


    $sitejudging='';

    $sitechiefname='';
    $siteip='';
    $updatetime=-1;
    $siteautojudge='f';
    $siteautoend='f';
    $siteglobalscore='';
    $siteactive='f';
    foreach ($ac1 as $key) {
        if(isset($param[$key])){
            $$key=myhtmlspecialchars($param[$key]);
            if(isset($type[$key]) && !is_numeric($param[$key])){
                MSGError("DBUpdateSite param error: $key is not numeric");
                return false;
            }
        }
    }
    if($siteautoend != "t" && $siteautoend != "") $siteautoend="f";
    if($siteactive !="t" && $siteactive !="") $siteactive="f";
    if($siteautojudge != "t" && $siteautojudge !="") $siteautojudge="f";
    if($sitescorelevel=="" || !is_numeric($sitescorelevel)){
        $sitescorelevel=-10;
    }else{
        if($sitescorelevel<-3) $sitescorelevel=-4;
        if($sitescorelevel>3) $sitescorelevel=4;
    }
    $docommit=false;
    if($c==null){
        $c=DBConnect();
        DBExec($c,"begin work","DBUpdateSite(begin)");
        $docommit=true;
    }
    $a=DBGetRow("select *from sitetable where contestnumber=$contestnumber and sitenumber=$sitenumber",0,$c);
    if($a==null){
        $ret=2;
        $param['number']=$sitenumber;
        DBNewSite($contestnumber,$c,$param);//funcion para create un nuevo sitio tambien esta incluido sitetimetable
        $a=DBGetRow("select *from sitetable where contestnumber=$contestnumber and sitenumber=$sitenumber",0,$c);
        if($a==null){
            DBExec($c,"rollback work","DBUpdateSite(rollback-errorsite)");
            MSGError("DBUpdateSite update error: impossible to create a site in the DB");
            LOGLevel("DBUpdateSite update error: impossible to create a site in the DB",0);
            return false;
        }
    }
    $t=time();
    if($updatetime<=0)
        $updatetime=$t;
    $ret=1;
    if($updatetime>$a['updatetime']){
        $ret=2;
        ////funcion para vaciar la tabla runtable teniendo en cuenta contest site user usersite tambien actualizar sitetable sitenextrun=0
        //if($sitenextrun==0)
        //    DBSiteDeleteAllRuns($contestnumber,$sitenumber,$_SESSION["usertable"]["usernumber"],$_SESSION["usertable"]["usersitenumber"],$c);
        ///funcion para vaciar la tabla clartable teniendo en cuenta contest site user usersite tambien actualizar sitetable sitenextclar=0
        //if($sitenextclar==0)
        //    DBSiteDeleteAllClars($contestnumber,$sitenumber,$_SESSION["usertable"]["usernumber"],$_SESSION["usertable"]["usersitenumber"],$c);
        ///funcion para vaciar la tabla tasktable teniendo en cuenta contest site user usersite tambien actualizar sitetable sitenexttask=0
        //if($sitenexttask==0)
        //    DBSiteDeleteAllTasks($contestnumber,$sitenumber,$_SESSION["usertable"]["usernumber"],$_SESSION["usertable"]["usersitenumber"],$c);
        $sql ="update sitetable set sitename='$sitename', ";
        if($sitepermitlogins!="") $sql.="sitepermitlogins='$sitepermitlogins', ";
        if($siteduration>0)
            $sql.="siteduration=$siteduration, ";
        if($siteip != '')
            $sql.="siteip='$siteip',";
        if($siteautoend != "")
            $sql.="siteautoend='$siteautoend', ";
        if($siteactive != "")
            $sql.="siteactive='$siteactive', ";
        if($siteglobalscore != "")
            $sql.="siteglobalscore='$siteglobalscore', ";
        if($sitenextuser>=0)
            $sql.="sitenextuser=$sitenextuser, ";

        if($sitenextrun>=0)
            $sql.="sitenextrun=$sitenextrun, ";


        if($sitechiefname !='')
            $sql.="sitechiefname='$sitechiefname', ";
        if($siteautojudge !='')
            $sql.="siteautojudge='$siteautojudge', ";
        if($sitejudging !='')
            $sql.="sitejudging='$sitejudging', ";

        if($sitelastmileanswer>0)
            $sql.="sitelastmileanswer=$sitelastmileanswer, ";
        if($sitelastmilescore>0)
            $sql.=" sitelastmilescore=$sitelastmilescore, ";
        if($sitescorelevel>-5)
            $sql.=" sitescorelevel=$sitescorelevel, ";
        $sql.=" updatetime=".$updatetime." where contestnumber=$contestnumber and sitenumber=$sitenumber ";
        // . "and updatetime<$updatetime";
        DBExec($c,$sql,"DBUpdateSite(update site)");
        if($docommit){
            DBExec($c,"commit work","DBUpdateSite(commit-update)");
            LOGLevel("User ".$_SESSION["usertable"]["username"]."/".$_SESSION["usertable"]["usersitenumber"].
                " changed the site $sitenumber (contest=$contestnumber) settings.",2);
        }
    }else{
        if($docommit)
            DBExec($c,"commit work","DBUpdateSite(commit-noupdate)");
    }
    return $ret;
}
//funccion para actulizar contesttable de true a false y sitetable, sitetimetable con valores nuevos
function DBUpdateContest($param, $c=null){
    if(isset($param['contestnumber']) && !isset($param['number'])) $param['number']=$param['contestnumber'];
    if(isset($param['contestname']) && !isset($param['name'])) $param['name']=$param['contestname'];
    if(isset($param['conteststartdate']) && !isset($param['startdate'])) $param['startdate']=$param['conteststartdate'];
    if(isset($param['contestduration']) && !isset($param['duration'])) $param['duration']=$param['contestduration'];
    if(isset($param['contestlastmileanswer']) && !isset($param['lastmileanswer'])) $param['lastmileanswer']=$param['contestlastmileanswer'];
    if(isset($param['contestlastmilescore']) && !isset($param['lastmilescore'])) $param['lastmilescore']=$param['contestlastmilescore'];
    if(isset($param['contestpenalty']) && !isset($param['penalty'])) $param['penalty']=$param['contestpenalty'];
    if(isset($param['contestmaxfilesize']) && !isset($param['maxfilesize'])) $param['maxfilesize']=$param['contestmaxfilesize'];
    if(isset($param['contestactive']) && !isset($param['active'])) $param['active']=$param['contestactive'];
    if(isset($param['contestscorelevel']) && !isset($param['scorelevel'])) $param['scorelevel']=$param['contestscorelevel'];
    if(isset($param['contestautoend']) && !isset($param['autoend'])) $param['autoend']=$param['contestautoend'];
    if(isset($param['contestglobalscore']) && !isset($param['globalscore'])) $param['globalscore']=$param['contestglobalscore'];
//    if(isset($param['contestip']) && !isset($param['ip'])) $param['ip']=$param['contestip'];
    if(isset($param['contestautojudge']) && !isset($param['autojudge'])) $param['autojudge']=$param['contestautojudge'];
    if(isset($param['contestnextuser']) && !isset($param['nextuser'])) $param['nextuser']=$param['contestnextuser'];
    if(isset($param['contestnextrun']) && !isset($param['nextrun'])) $param['nextrun']=$param['contestnextrun'];
    if(isset($param['contestjudging']) && !isset($param['judging'])) $param['judging']=$param['contestjudging'];
    $password='';
    if(isset($param["password"])){
        $password=$param["password"];
    }

    $ac=array('number','contestpermitlogins');
    $ac1=array('updatetime','scorelevel','name',
        'active','lastmileanswer','lastmilescore','penalty','startdate','duration','maxfilesize','private','activeresult',
        'autoend','globalscore','autojudge','nextuser','nextrun','judging');

    //$ac=array('contestnumber','sitenumber','sitename','sitepermitlogins','sitescorelevel');
    //$ac1=array('siteautoend','siteglobalscore','siteip','siteactive',
    //            'siteautojudge','sitenextuser','sitenextrun','sitechiefname','sitejudging');

    $type['number']=1;
    $type['scorelevel']=1;
    $type['startdate']=1;
    $type['updatetime']=1;
    $type['duration']=1;
    $type['penalty']=1;
    $type['maxfilesize']=1;

    $type['lastmilescore']=1;
    $type['lastmileanswer']=1;


    $type['nextuser']=1;
    $type['nextrun']=1;


    foreach ($ac as $key) {
        if(!isset($param[$key])){
            MSGError("DBUpdateContest param error: $key is not set");
            return false;
        }
        $$key=myhtmlspecialchars($param[$key]);
        if(isset($type[$key]) && !is_numeric($param[$key])){
            MSGError("DBUpdateContest param error: $key is not numeric");
            return false;
        }
    }
    $name='';

    $duration=-1;
    $lastmilescore=-1;
    $lastmileanswer=-1;
    $penalty=-1;
    $maxfilesize=-1;
    $active='f';
    $startdate=-1;
    $updatetime=-1;
    $private='f';
    $activeresult='f';

    $nextuser=-1;
    $nextrun=-1;
    $judging='';

    $autojudge='f';
    $autoend='f';
    $globalscore='';

    foreach ($ac1 as $key) {
        if(isset($param[$key])){
            $$key=myhtmlspecialchars($param[$key]);
            if(isset($type[$key]) && !is_numeric($param[$key])){
                MSGError("DBUpdateContest param error: $key is not numeric");
                return false;
            }
        }
    }

    if($autoend != "t" && $autoend != "") $autoend="f";
    if($active !="t" && $active !="") $active="f";
    if($autojudge != "t" && $autojudge !="") $autojudge="f";
    if($scorelevel=="" || !is_numeric($scorelevel)){
        $scorelevel=-10;
    }else{
        if($scorelevel<-3) $scorelevel=-4;
        if($scorelevel>3) $scorelevel=4;
    }


    $t=time();
    if($updatetime<=0)
        $updatetime=$t;
    if($activeresult != "t" || $activeresult =="") $activeresult="f";
    if($private != "t" || $private =="") $private="f";
    //para ver resultado

    $cw=false;
    if($c==null){
        $cw=true;
        $c=DBConnect();
        DBExec($c,"begin work","DBUpdateContest(begin)");
    }
    $a=DBGetRow("select * from contesttable where contestnumber=$number for update",0,$c,"DBUpdateContest(get for update)");
    if($a==null){
        MSGError("Error updating contest $number -- not found");
        LOGError("DBUpdateContest contest $number not found");
        return false;
    }
    $ret=1;
    if($active =='t' ){
        $ret=2;
        //DBExec($c,"update contesttable set contestactive='f'","DBUpdateContest(desactivate)");
        DBExec($c,"update contesttable set contestactive='t' where contestnumber=$number",
            "DBUpdateContest(active)");
        LOGLevel("User ".$_SESSION["usertable"]["username"]."/ activated contest $number.",2);
    }
    $chd=false;

    if($updatetime > $a['updatetime']){


        $ret=2;
        $sql="update contesttable set updatetime=".$updatetime;
        if($name != '') $sql.=", contestname='$name'";
        if($maxfilesize > 0) $sql.=", contestmaxfilesize=$maxfilesize";
        if($penalty > 0) $sql.=", contestpenalty=$penalty";
        if($lastmileanswer > 0) $sql.=", contestlastmileanswer=$lastmileanswer";
        if($lastmilescore > 0) $sql.=", contestlastmilescore=$lastmilescore";
        if($startdate > 0) $sql.=", conteststartdate=$startdate";
        if($duration > 0) $sql.=", contestduration=$duration";
        $sql.=", contestactiveresult='$activeresult'";
        $sql.=", contestprivate='$private', ";

        if($contestpermitlogins!="") $sql.="contestpermitlogins='$contestpermitlogins', ";

        if($autoend != "")
            $sql.="contestautoend='$autoend', ";
        if($active != "")
            $sql.="contestactive='$active', ";
        if($globalscore != "")
            $sql.="contestglobalscore='$globalscore', ";
        if($nextuser>=0)
            $sql.="contestnextuser=$contestnextuser, ";
        if($nextrun>=0)
            $sql.="contestnextrun=$nextrun, ";
        if($autojudge !='')
            $sql.="contestautojudge='$autojudge', ";
        if($judging !='')
            $sql.="contestjudging='$judging', ";
        if($scorelevel>-5)
            $sql.="contestscorelevel=$scorelevel, ";

        $sql.="contestpassword='$password' where contestnumber=$number";
        DBExec($c,$sql, "DBUpdateContest(update contest)");

        if($startdate>0){
            $p=array();
            $p['contest']=$number;

            $p['start']=$startdate;
            ////funcion para verificar los datos de contest,site,
            //en sitetimetable, si no hay registro en la tabla inserta new
            DBRenewContestTime($p, $c);
        }

        $chd=true;
    }
    if($cw){
        DBExec($c,"commit work","DBUpdateContest(commit)");
    }
    if($chd)
        LOGLevel("User ".$_SESSION["usertable"]["username"]."/ changed the contest $number settings.",2);
    return $ret;
}
//insertamos a la tabla langtable 5 lenguajes principales
function insertlanguages($c=null){
    $ok=false;
    $param=null;
    $param['number']=1;
    $param['name']='C';
    $param['extension']='c';
    //es para actulizar o insertar un nuevo registro el name y extension en la tabla langtable
    DBNewLanguage($param,$c);
    $param['number']=2;
    $param['name']='C++11';
    $param['extension']='cpp';//cc
    //es para actulizar o insertar un nuevo registro el name y extension en la tabla langtable
    DBNewLanguage($param,$c);
    $param['number']=3;
    $param['name']='Java';
    $param['extension']='java';
    //es para actulizar o insertar un nuevo registro el name y extension en la tabla langtable
    DBNewLanguage($param,$c);
    $param['number']=4;
    $param['name']='Python2';
    $param['extension']='py2';
    //es para actulizar o insertar un nuevo registro el name y extension en la tabla langtable
    DBNewLanguage($param,$c);
    $param['number']=5;
    $param['name']='Python3';
    $param['extension']='py3';
    //es para actulizar o insertar un nuevo registro el name y extension en la tabla langtable
    DBNewLanguage($param,$c);
}
//funcion para registrar todas las respuestas posibles en una competencia
function insertanswers($c){

    DBExec($c, "insert into answertable (answernumber, runanswer, yes, fake) values ".
        "(0, 'Not answerd yet','f','t')","DBFakeContest(insert fake answer)");
    DBExec($c, "insert into answertable (answernumber, runanswer, yes, fake) values ".
        "(1, 'YES - Accepted','t','f')","DBFakeContest(insert YES answer)");
    DBExec($c, "insert into answertable (answernumber, runanswer, yes, fake) values ".
        "(2, 'NO - Compilation error','f','f')","DBFakeContest(insert CE answer)");
    DBExec($c, "insert into answertable (answernumber, runanswer, yes, fake) values ".
        "(3, 'NO - Runtime error','f','f')","DBFakeContest(insert RE answer)");
    DBExec($c, "insert into answertable (answernumber, runanswer, yes, fake) values ".
        "(4, 'NO - Time limit exceeded','f','f')","DBFakeContest(insert TLE answer)");
    DBExec($c, "insert into answertable (answernumber, runanswer, yes, fake) values ".
        "(5, 'NO - Presentation error','f','f')","DBFakeContest(insert PE answer)");
    DBExec($c, "insert into answertable (answernumber, runanswer, yes, fake) values ".
        "(6, 'NO - Wrong answer','f','f')","DBFakeContest(insert WA answer)");
    DBExec($c, "insert into answertable (answernumber, runanswer, yes, fake) values ".
        "(7, 'NO - Contact staff','f','f')","DBFakeContest(insert CS answer)");
    DBExec($c, "insert into answertable (answernumber, runanswer, yes, fake) values ".
        "(8, 'NO - Name mismatch','f','f')","DBFakeContest(insert MI answer)");
}
//funcion para create un nuevo sitio tambien esta incluido sitetimetable y user admin
function DBNewSite($contest, $c=null, $param=array()){
    $cw=false;
    if($c==null){
        $cw=true;
        $c=DBConnect();
        DBExec($c,"begin work");
    }
    $ct=DBContestInfo($contest,$c);//retorna la informacion de la competencia en caso no retorna null
    if($ct==null) return false;
    if(isset($param['sitenumber']) && !isset($param['number'])) $param['number']=$param['sitenumber'];
    if(isset($param['siteduration']) && !isset($param['duration'])) $param['duration']=$param['siteduration'];

    $ac=array('number','siteip','sitename','sitescorelevel','updatetime','startdate','duration');
    $type=array();
    $type['startdate']=1;
    $type['duration']=1;
    $type['number']=1;
    $type['sitescorelevel']=1;
    $type['updatetime']=1;
    foreach ($ac as $key) {
        if(isset($param[$key]) && (!isset($type[$key]) || is_numeric($param[$key])))
            $$key=myhtmlspecialchars($param[$key]);
        else
            $$key="";
    }
    if($number==""){
        $a=DBGetRow("select max(sitenumber) as site from sitetable where contestnumber=$contest", 0, $c);
        if($a==null) $n=1;
        else $n = $a["site"]+1;
        $number=$n;
    }else{
        //si en el sitetable existe igual entoces return false
        $a=DBGetRow("select *from sitetable where contestnumber=$contest and sitenumber=$number", 0, $c);
        if($a != null){
            if($cw) DBExec($c, "commit work");
            return false;
        }
    }
    if($duration=='') $duration=$ct["contestduration"];
    if($startdate=='') $startdate=$ct["conteststartdate"];
    if($siteip=="") $siteip="127.0.0.1/salomon";
    if($sitename=="") $sitename="Site";
    if($sitescorelevel=="") $sitescorelevel=3;
    $t=time();
    if($updatetime=="") $updatetime=$t;
    ////DBExecNonStop( ejecuta el sql en caso de no exito lanza un error
    if(!DBExecNonStop($c, "insert into sitetable (contestnumber, sitenumber, siteip, sitename, siteactive, sitepermitlogins, ".
            "sitelastmileanswer, sitelastmilescore, siteduration, siteautoend, sitejudging, ".
            "siteglobalscore, sitescorelevel, ".
            "sitenextuser, sitenextrun, updatetime) values ".
            "($contest, $number, '$siteip','$sitename', 't', 't', ".
            $ct["contestlastmileanswer"].",".$ct["contestlastmilescore"].
            ", $duration, 't', '$number', '$number', $sitescorelevel, 0, 0, $updatetime)")){
        if($cw) DBExec($c,"commit work");
        return false;
    }

    $cf=globalconf();
    $admpass=myhash($cf["basepass"]);
    DBExec($c, "insert into usertable ".
        "(contestnumber, usersitenumber, usernumber, username, userfullname, ".
        "userdesc, usertype, userenabled, usermultilogin, userpassword, userip, userlastlogin, ".
        "usersession, usersessionextra, userlastlogout, userpermitip, updatetime) values ".
        "($contest, $number, 1000, 'admin','Administrador', NULL, 'admin', ".
        "'t', 't','$admpass', NULL, NULL, '', '', NULL, NULL, $updatetime)");
    $param=array();
    $param['contest']=$contest;
    $param['site']=$number;
    $param['start']=$startdate;
    DBRenewSiteTime($param,$c);//funcion para verificar los datos de contest,site, en sitetimetable, si no hay registro en la tabla inserta new
    if($cw) DBExec($c, "commit work");
    LOGLevel("User ".$_SESSION["usertable"]["username"]."/".$_SESSION["usertable"]["usersitenumber"].
            " create site $number on contest $contest.",2);
    return $number;
}
//funcion para actulizar usuario y modificacion de password
//ac1
function DBUserUpdate($user, $username, $userfull, $userdesc, $passo, $passn){
    $a = DBUserInfo($user, null, false);//esta funcion retorna el registro de usuario y tambien si cambio o no hashpass = true
    $p = myhash($a["userpassword"].session_id());
    if($a["userpassword"] != "" && $p != $passo){
        LOGLevel("User ".$_SESSION["usertable"]["username"].
            "tried to change settings, but password was incorrect. ",2);
            //intentó cambiar la configuración, pero la contraseña era incorrecta.
        MSGError("Incorrect password");
    }else{
        if(!$a['changepassword']){
            //El cambio de contraseña está DESHABILITADO
            MSGError('Password change is DISABLED'); return;
        }
        if($a["userpassword"] == "") $temp = myhash("");
        else $temp=$a["userpassword"];
        $lentmp = strlen($temp);//para saber el tamano de la cadena
        $temp = bighexsub($passn, $temp);///si son iguales retorna 0 si no retorna sub en resto de dos str.

        if($lentmp>strlen($temp)){//esperar...
            $newpass='0'.$temp;
            while(strlen($newpass)<$lentmp) $newpass='0'.$newpass;
        }else{
            $newpass=substr($temp,strlen($temp)-$lentmp);
        }
        $c=DBConnect();
        DBExec($c,"begin work");
        DBExec($c,"lock table usertable");
        $r=DBExec($c,"select *from usertable where username='$username' and usernumber!=$user");
        $n=DBnlines($r);
        if($n == 0){
            $sql="update usertable set username='$username', userdesc='$userdesc', userfullname='$userfull', updatetime=".time();
            if($newpass !=myhash("")) $sql.=", userpassword='$newpass'";
            $sql .= " where usernumber=$user";
            $r=DBExec($c,$sql);
            DBExec($c,"commit work");
            LOGLevel("User ".$_SESSION["usertable"]["username"]." changed his settings (newname=$username) ",2);

            $_SESSION["usertable"]["userfullname"]=$userfull;//para cambiar el session de userfull
            MSGError("Data updated.");
            //ForceLoad("index.php");//index.php
        }else{
            DBExec($c,"rollback work");
            //no pudo cambiar su configuración
            LOGLevel("User ".$_SESSION["usertable"]["username"]." couldn't change his settings ",2);
            MSGError("Update problem (maybe username already in use). No data was changed.");
        }
    }

}
//funcion para actulizar o insertar un nuevo usuario segun los datos que pasa
//actualizado1
function DBNewUser($param, $c=null){

    //if(isset($param['contestnumber']) && !isset($param['contest'])) $param['contest']=$param['contestnumber'];
	//if(isset($param['sitenumber']) && !isset($param['site'])) $param['site']=$param['sitenumber'];
	//if(isset($param['usersitenumber']) && !isset($param['site'])) $param['site']=$param['usersitenumber'];
	if(isset($param['usernumber']) && !isset($param['user'])) $param['user']=$param['usernumber'];
	if(isset($param['number']) && !isset($param['user'])) $param['user']=$param['number'];

	if(isset($param['userpassword']) && !isset($param['pass'])) $param['pass']=$param['userpassword'];
	if(isset($param['userenabled']) && !isset($param['enabled'])) $param['enabled']=$param['userenabled'];
	if(isset($param['usermultilogin']) && !isset($param['multilogin'])) $param['multilogin']=$param['usermultilogin'];
	if(isset($param['userpermitip']) && !isset($param['permitip'])) $param['permitip']=$param['userpermitip'];
	if(isset($param['userfullname']) && !isset($param['userfull'])) $param['userfull']=$param['userfullname'];
	if(isset($param['usertype']) && !isset($param['type'])) $param['type']=$param['usertype'];
	if(isset($param['userpermitip']) && !isset($param['permitip'])) $param['permitip']=$param['userpermitip'];
	if(isset($param['userpermitip']) && !isset($param['permitip'])) $param['permitip']=$param['userpermitip'];

	$ac=array('user');
	//$ac=array('contest','site','user');
	$ac1=array('updatetime','username','userfull','useremail','userdesc','type','enabled','multilogin','pass','permitip','changepass',
			   'userip','userlastlogin','userlastlogout','usersession','usersessionextra');

	//$typei['contest']=1;
	$typei['updatetime']=1;
	//$typei['site']=1;
	$typei['user']=1;
	foreach($ac as $key) {
		if(!isset($param[$key]) || $param[$key]=="") {
			MSGError("DBNewUser param error: $key not found");
			return false;
		}
		if(isset($typei[$key]) && !is_numeric($param[$key])) {
			MSGError("DBNewUser param error: $key is not numeric");
			return false;
		}
		$$key = myhtmlspecialchars($param[$key]);
	}
	$username= "team" . $user;
	$updatetime=-1;
	$pass = null;

	$userfull='';
	$useremail='';
	$userdesc='';
	$type='team';
	$enabled='f';
	$changepass='f';
	$multilogin='f';
	$permitip='';
	$usersession=null;
	$usersessionextra=null;
	$userip=null;
	$userlastlogin=null;
	$userlastlogout=null;
	foreach($ac1 as $key) {
		if(isset($param[$key])) {
			$$key = myhtmlspecialchars($param[$key]);
			if(isset($typei[$key]) && !is_numeric($param[$key])) {
				MSGError("DBNewUser param error: $key is not numeric");
				return false;
			}
		}
	}
	$t = time();
	if($updatetime <= 0)
		$updatetime=$t;

	if ($type != "admin" && $type != "coach")
		$type = "team";
	if ($type == "admin") $changepass = "t";
	if ($enabled != "f") $enabled = "t";
	if ($multilogin != "t") $multilogin = "f";
	if ($changepass != "t") $changepass = "f";

	$cw = false;
	if($c == null) {
		$cw = true;
		$c = DBConnect();
		DBExec($c, "begin work", "DBNewUser(begin)");
	}
	DBExec($c, "lock table usertable", "DBNewUser(lock)");
//
    /*$r = DBExec($c, "select * from sitetable where sitenumber=$site and contestnumber=$contest", "DBNewUser(get site)");
	$n = DBnlines ($r);
	if($n == 0) {
	      if($cw)
	         DBExec ($c, "rollback work","DBNewUser(no-site)");
	      MSGError("DBNewUser param error: site $site does not exist");
	      return false;
	}*/
	if($pass != myhash("") && $type != "admin" && $changepass != "t" && substr($pass,0,1) != "!") $pass='!'.$pass;
	$r = DBExec($c, "select * from usertable where (username='$username' or useremail='$useremail') and usernumber!=$user", "DBNewUser(get user)");

	$n = DBnlines ($r);
	$ret=1;

	if ($n == 0) {

		$sql = "select * from usertable where usernumber=$user";
		$a = DBGetRow ($sql, 0, $c);
        //para insercion o actulizacion
		if ($a == null) {
			  $ret=2;

    		  /*$sql = "select * from sitetable where sitenumber=$site and contestnumber=$contest";
    		  $aa = DBGetRow ($sql, 0, $c);
    		   if($aa==null) {
    		     if($cw)
    		   	       DBExec ($c, "rollback work");
    		     MSGError("Site $site does not exist");
    		     return false;
             }*/
    		   $sql = "insert into usertable (usernumber, username, userfullname, useremail, " .
    				"userdesc, usertype, userenabled, usermultilogin, userpassword, userpermitip) values " .
    				"($user, '$username', '$userfull', '$useremail', '$userdesc', '$type', '$enabled', " .
    				"'$multilogin', '$pass', '$permitip')";
    			DBExec ($c, $sql, "DBNewUser(insert)");
    			if($cw) {
    				DBExec ($c, "commit work");
    			}
    			LOGLevel ("Usuario $user registrado.",2);
		} else {
			if($updatetime > $a['updatetime']) {
				$ret=2;
				$sql = "update usertable set username='$username', userdesc='$userdesc', updatetime=$updatetime, " .
					"userfullname='$userfull', usertype='$type', userpermitip='$permitip', ";

                if($useremail!='') $sql .= "useremail='$useremail', ";
                if($pass != null && $pass != myhash("")) $sql .= "userpassword='$pass', ";
				if($usersession != null) $sql .= "usersession='$usersession', ";
				if($usersessionextra != null) $sql .= "usersessionextra='$usersessionextra', ";
				if($userip != null) $sql .= "userip='$userip', ";
				if($userlastlogin != null) $sql .= "userlastlogin='$userlastlogin', ";
				if($userlastlogout != null) $sql .= "userlastlogout='$userlastlogout', ";
				$sql .= "userenabled='$enabled', usermultilogin='$multilogin'";
				$sql .=	" where usernumber=$user";
				$r = DBExec ($c, $sql, "DBNewUser(update)");
				if($cw) {
					DBExec ($c, "commit work");
				}
				LOGLevel("Usuario $user actualizado.",2);
			}
		}
	} else {
	  if($cw)
	     DBExec ($c, "rollback work");
	  LOGLevel ("Problema de actualizacion para el usuario  $user (tal vez el nombre de usuario ya esté en uso).",1);
//Problema de actualización para el usuario $ usuario, sitio $ sitio (tal vez el nombre de usuario ya esté en uso).
      MSGError ("Problema de actualizacion para el usuario  $user, (tal vez el nombre de usuario ya esté en uso).");
	  return false;
	}
	if($cw) DBExec($c, "commit work");
	return $ret;
}
//funcion para restablecer password
function DBResPassword($name, $pass, $c=null){
    $cw = false;
	if($c == null) {
		$cw = true;
		$c = DBConnect();
		DBExec($c, "begin work", "DBNewUser(begin)");
	}
	DBExec($c, "lock table usertable", "DBNewUser(lock)");

    $u=DBUserInfoName($name);
    $user=$u["usernumber"];
	$r = DBExec($c, "select * from usertable where (username='$name' or useremail='$name') and usernumber!=$user", "DBNewUser(get user)");

	$n = DBnlines ($r);
	$ret=false;

	if ($n == 0) {

		$sql = "select * from usertable where username='$name' or useremail='$name'";
		$a = DBGetRow ($sql, 0, $c);
        //para insercion o actulizacion
		if ($a == null) {

    			if($cw) {
    				DBExec ($c, "commit work");
    			}
    			LOGLevel ("Usuario $user no encotrado para restablecer password.",2);
                return false;
        } else {

				$sql = "update usertable set userpassword='$pass' where username='$name' or useremail='$name'";
				$r = DBExec ($c, $sql, "DBNewUser(update)");
				if($cw) {
					DBExec ($c, "commit work");
				}
				LOGLevel("Usuario $user restablece password.",2);
                return true;
		}
	} else {
	  if($cw)
	     DBExec ($c, "rollback work");
	  LOGLevel ("Problema de restablecer password para el usuario  $user (tal vez el nombre de usuario ya esté en uso).",1);
//Problema de actualización para el usuario $ usuario, sitio $ sitio (tal vez el nombre de usuario ya esté en uso).
      MSGError ("Problema de restablecer password para el usuario  $user, (tal vez el nombre de usuario ya esté en uso).");
	  return false;
	}

	if($cw) DBExec($c, "commit work");
	return $ret;
}
//funcion para habilitar usuarios
function enabledUser($user,$c=null){
    $cw = false;

	if($c == null) {
		$cw = true;
		$c = DBConnect();
		DBExec($c, "begin work", "DBNewUser(begin)");
	}
    $t=time();
    $sql="update usertable set userenabled='t', updatetime=".$t." where usernumber=".$user;
    DBExec($c,$sql);
    if($cw) DBExec($c, "commit work");
    return true;
}
//retorna el tiempo de la competencia en d h s cuanto falta o no esta corriendo
function siteclock(){
    //retorna el resultado de la consulta del sitio tambien con algunas
    //mejoras de sitetimetable s e si es posible o null si no hay constest y site o mesaje si es true
    if(($s=DBSiteInfo($_SESSION["usertable"]["contestnumber"],$_SESSION["usertable"]["usersitenumber"])) == null)
        ForceLoad("../index.php");//genera script para redireccion con el parametro dada
    ////////CASO DE COMENZAR MAS TARDE NO CENTRALIZADO
    if(substr($_SESSION["usertable"]["username"],0,3) == 'XXX'){
        $s["currenttime"]=$s["currenttime"] - 60*10; //10 minutos mas a tiempo actual
    }
    if($s["siteactive"]!="t")
        return array("sitio no activa",-1000000000);//site is not active
    if(!$s["siterunning"])
        return array("competencia no corriendo",-1000000000);//contest not running
    if($s["currenttime"]<0){
        $t = - $s["currenttime"];
        if($t>3600*24){//un dia
            $t=((int) ($t/(360*24)))/10;
            return array("&gt; ".$t." dia(s) para iniciar",$s["currenttime"]);
        }
        if($t>3600){//horas
            $t=((int) ($t/360))/10;
            return array("&gt; ".$t." hora(s) para iniciar",$s["currenttime"]);
        }
        if($t>60){
            $t=(int) ($t/60);
            return array("&gt; ".$t." minuto(s) para iniciar",$s["currenttime"]);
        }else{
            return array($t." segundo(s) para iniciar",$s["currenttime"]);
        }

    }
    //para tiempo >= 0
    if($s["currenttime"]>=0){
        $t=$s["siteduration"] - $s["currenttime"];
        $str='';
        if($t>=3600*24){
            $str.= ((int)($t/(3600*24))).'d ';
            $t=$t%(3600*24);
        }
        if($t >= 3600){
            $str.=((int)($t/3600)).'h ';
            $t=$t%3600;
        }
        if($t>60){
            $t=(int)($t/60);
            return array($str.$t." minuto(s) Quedan",$s["currenttime"]);
        }else if($str==''){
            if($t>0){
                return array($t. " second(s) Quedan",$s["currenttime"]);
            }else{
                $t=(int)(-$t/60);
                return array($t."min. de tiempo extra",$s["currenttime"]);
            }
        }else{
            return array($str." Quedan",$s["currenttime"]);
        }
    }else{
        return array("no empezado",-1000000000);
    }

}
//funcion de sacar la hora de las todas las competencias
function siteclock2($contest){
    //retorna el resultado de la consulta del sitio tambien con algunas
    //mejoras de sitetimetable s e si es posible o null si no hay constest y site o mesaje si es true
    if(($s=DBContestClockInfo($contest)) == null)
        ForceLoad("../index.php");//genera script para redireccion con el parametro dada

    //if($s["siteactive"]!="t")
    //    return array("sitio no activa",-1000000000);//site is not active
    if(!$s["contestrunning"])
        return array("competencia no corriendo",-1000000000);//contest not running
    if($s["currenttime"]<0){
        $t = - $s["currenttime"];
        if($t>3600*24){//un dia
            $t=((int) ($t/(360*24)))/10;
            return array("&gt; ".$t." dia(s) para iniciar",$s["currenttime"]);
        }
        if($t>3600){//horas
            $t=((int) ($t/360))/10;
            return array("&gt; ".$t." hora(s) para iniciar",$s["currenttime"]);
        }
        if($t>60){
            $t=(int) ($t/60);
            return array("&gt; ".$t." minuto(s) para iniciar",$s["currenttime"]);
        }else{
            return array($t." segundo(s) para iniciar",$s["currenttime"]);
        }

    }
    //para tiempo >= 0
    if($s["currenttime"]>=0){
        $t=$s["contestduration"] - $s["currenttime"];
        $str='';
        if($t>=3600*24){
            $str.= ((int)($t/(3600*24))).'d ';
            $t=$t%(3600*24);
        }
        if($t >= 3600){
            $str.=((int)($t/3600)).'h ';
            $t=$t%3600;
        }
        if($t>60){
            $t=(int)($t/60);
            return array($str.$t." minuto(s) Quedan",$s["currenttime"]);
        }else if($str==''){
            if($t>0){
                return array($t. " second(s) Quedan",$s["currenttime"]);
            }else{
                $t=(int)(-$t/60);
                return array($t."min. de tiempo extra",$s["currenttime"]);
            }
        }else{
            return array($str." Quedan",$s["currenttime"]);
        }
    }else{
        return array("no empezado",-1000000000);
    }

}


//retorna la informacion de la competencia en caso no retorna null
function DBContestInfo($contest,$c=null){
    $sql="select *from contesttable where contestnumber=$contest";
    ////retorna un registro de una cosulta en array asociativo en caso de no existo lanza un error
    $a=DBGetRow($sql,0,$c);
    if($a==null){
        //funcion para generar el nivel de registro y prioridad para el sistema nivel 0
        LOGError("Unable to find the contest $contest in the database. SQL=(".$sql.")");
        MSGError("Unable to find the contest $contest in the database. Contact an admin now!");//lanza un mesaje en javascript
        return null;
    }
    return $a;
}
function DBContestClockInfo($contest, $c=null, $msg=true){
    $sql = "select *from contesttable where contestnumber=$contest";
    if($c==null) $c=DBConnect();//realizar conexcion su no existe conexcion
    $r=DBExec($c, $sql);//ejecuta el sql y retorna el resultado en caso de no lanza un mesaje de error y la prioridad del mesaje
    //cuenta el numero de filas del resultado sql
    if(DBnlines($r)<1){
        if($msg){
            //funcion para generar el nivel de registro y prioridad para el sistema nivel 0
            LOGError("Unable to find the contest in the database (contest=$contest). SQL=(".$sql.")");
            MSGError("Unable to find the contest in the database. contact an admin now!");//lanza un mesaje en javascript
        }else{
            return null;
        }
    }
    $a=DBRow($r, 0);//retorna en array asocciotivo dando el resutado de la consulta y posicion
    $sql="select conteststartdate as s, contestenddate as e from contesttimetable ".
        "where contestnumber=$contest order by conteststartdate";
    $r=DBExec($c, $sql);//ejecuta el sql y retorna el resultado en caso de no lanza un mesaje de error y la prioridad del mesaje
    $n=DBnlines($r);//retorna el numero de filas que tiene la consulta
    $a["currenttime"]=0;//tiempo actual
    $a["contestrunning"]=false;//sitio corriendo
    //$a["siterunning"]=false;//sitio corriendo
    $ti=time();
    for($i=0;$i<$n;$i++){
        $b=DBRow($r,$i);
        if($i==0) $a["conteststartdate"]=$b["s"];//se guarda junto con la info del sitio
        if($b["e"]==0){
            $a["contestrunning"]=true;//currenttime == a tiempo actual
            $a["currenttime"]+=$ti-$b["s"];//tiempo que falta para la competencia
        }else{
            $a["currenttime"]+=$b["e"]-$b["s"];//tiempo que falta para finalizar o 5 horas de contest
        }
        $a["contestendeddate"]=$b["e"];
    }
    if($a["contestendeddate"] == 0)
        $a["contestendeddate"] = $ti + $a["contestduration"] - $a["currenttime"];
    $a["contestautoended"] = false;
    if($a["contestautoend"] == "t" && $a["currenttime"] >= $a["contestduration"]){
        $a["contestrunning"] = false;
        $a["contestautoended"] = true;
    }

    return $a;
}
//funcion para sacar todas las competencias
function DBContestInfoAll() {

    $c = DBConnect();
    $sql = "select distinct c.contestnumber as number, c.usernumber as user, c.contestprivate as private, " .
        "c.contestname as name, c.conteststartdate as startdate, c.contestactive as active, c.contestprivate as private " .
        "from contesttable as c where c.contestnumber!=0 order by c.contestnumber desc";
    $r = DBExec($c, $sql, "DBContestInfoAll(get contests)");
    $n = DBnlines($r);
    $a = array();
    for ($i=0;$i<$n;$i++) {
        $a[$i] = DBRow($r,$i);
    }
    return $a;
}
//funcion para actulizar la tabla sitetimetable siteenddate y inserta un nuevo sitetimetable start a ahora
function DBSiteStartNow ($contest, $site) {
    //retorna el resultado de la consulta del sitio tambien con algunas
    //mejoras de sitetimetable s e si es posible o null si no hay constest y site o mesaje si es true
	$s = DBSiteInfo($contest, $site);
	if($s["siterunning"]) return false;
	$t = time();
	$c = DBConnect();
	DBExec($c, "begin work");
	DBExec($c, "lock table sitetable");
	DBExec($c, "lock table sitetimetable");
	DBExec($c, "update sitetimetable set siteenddate=".$s['siteendeddate']." where ".
			"siteenddate=0 and sitenumber=$site and contestnumber=$contest");
	DBExec($c, "insert into sitetimetable (contestnumber, sitenumber, sitestartdate, siteenddate) ".
			"values ($contest, $site, $t, 0)");
	DBExec($c, "commit work");
	LOGLevel("Site $site (contest=$contest) started at ".dateconv($t),2);
	return true;
}
//funcion para que devuelve la informacion de la competencia activa
function DBGetActiveContest() {
       $sql = "select * from contesttable where contestactive='t'";
       $a = DBGetRow ($sql, 0);
       if ($a == null) {
               LOGError("Unable to find active contests in the database. SQL=(" . $sql . ")");
               MSGError("Unable to find active contests in the database.");
       }
       return $a;
}
//funcion para actulizar la tabla sitetimetable siteenddate=$t ahora
function DBSiteEndNow ($contest, $site, $w=0) {
    //retorna el resultado de la consulta del sitio tambien con algunas
    //mejoras de sitetimetable s e si es posible o null si no hay constest y site o mesaje si es true
	$s = DBSiteInfo($contest, $site);
	if(!$s["siterunning"]) return false;
	if($w == 0) $t = time();
	else $t = $w;
	$c = DBConnect();
        DBExec($c, "begin work");
	DBExec($c, "lock table runtable");
	$a = DBGetRow("select max(rundate) as t from runtable where contestnumber=$contest and ".
			"runsitenumber=$site and not runstatus ~ 'deleted'", 0);
	if($a["t"] >= $t) {
        //No se puede detener un concurso antes de una carrera existente
		LOGLevel("Unable to stop a contest before an existing run",2);
        //Imposible detener un concurso antes de una carrera existente
		MSGError("Impossible to stop a contest before an existing run");
		DBExec($c, "commit work");
		return false;
	}
    //actualiza la tabla a tiempo ahora
    DBExec($c, "update sitetimetable set siteenddate=$t, updatetime=".time()." " .
                "where contestnumber=$contest and sitenumber=$site and siteenddate=0");
	DBExec($c, "commit work");
	LOGLevel("Site $site (contest=$contest) stopped at ".dateconv(time()),2);
	return true;
}
//funcion para verificar los datos de contest,site, en sitetimetable, si no hay registro en la tabla inserta new
//antes DBRenewSiteTime
function DBRenewContestTime($param, $c=null){
    if(!isset($param[0])){
        $tmp = $param;
        $param=array();
        $param[0]= $tmp;
    }
    $ac=array('contest','start');
    $ac1=array('enddate','updatetime');
    $type['contest']=1;

    $type['start']=1;
    $type['enddate']=1;

    $t=time();
    $maxtime=0;
    for ($i=0; isset($param[$i]); $i++) {
        //LOGLevel(implode(" ",array_keys($param[$i])),2);
        //LOGLevel(implode(" ",$param[$i]),2);
        if(isset($param[$i]['contestnumber']) && !isset($param[$i]['contest'])) $param[$i]['contest']=$param[$i]['contestnumber'];
        //if(isset($param[$i]['usernumber']) && !isset($param[$i]['user'])) $param[$i]['user']=$param[$i]['usernumber'];
        foreach ($ac as $key) {
            if(!isset($param[$i][$key])){
                MSGError("DBRenewContestTime param error: $key is not set");
                return false;
            }
            if(isset($type[$key]) && !is_numeric($param[$i][$key])){
                MSGError("DBRenewContestTime param error: $key is not numeric");
                return false;
            }
        }
        foreach ($ac1 as $key) {
            if(isset($param[$i][$key])){
                if(isset($type[$key]) && !is_numeric($param[$i][$key])){
                    MSGError("DBRenewContestTime param error: $key is not numeric");//alert
                    return false;
                }
            }
        }
        if(!isset($param[$i]['updatetime'])) $param[$i]['updatetime']=$t;
        if($param[$i]['updatetime']>$maxtime) $maxtime = $param[$i]['updatetime'];
        if(!isset($param[$i]['enddate'])) $param[$i]['enddate']=0;

        if($param[$i]['contest']!=$param[0]['contest']){
            MSGError("DBRenewContestTime param error: contest have to have over all instances");
            return false;
        }
    }
    $cw=false;
    if($c==null){
        $cw=true;
        $c=DBConnect();
        DBExec($c,"begin work","DBRenewContestTime(begin)");
    }
    DBExec($c,"lock table contesttimetable","DBRenewContestTime(lock)");

    $a = DBGetRow("select max(updatetime) as maxtime from contesttimetable where contestnumber=".$param[0]['contest'], 0, $c);

    $ret=1;
    if($a == null || $a['maxtime']<$maxtime){
        DBExec($c, "delete from contesttimetable where contestnumber=".$param[0]['contest'],"DBRenewContestTime(delete)");

        for($i=0;isset($param[$i]);$i++){
            DBExec($c,"insert into contesttimetable (contestnumber, conteststartdate, contestenddate, updatetime) ".
            "values (".$param[0]['contest'].", ".$param[$i]['start'].", ".
            $param[$i]['enddate'].", ".$param[$i]['updatetime'].")","DBRenewContestTime(insert)");
        }
        $ret=2;
    }
    if($cw) DBExec($c,"commit work", "DBRenewContestTime(commit)");
    return $ret;
}
//funcion para sacar el max number para user
function DBUserNumberMax($c=null){
    $cw=false;
    if($c==null){
        $cw=true;
        $c=DBConnect();
        DBExec($c,"begin work","DBUserNumberMax(begin)");
    }
    //no retorna un array asociativo el primer resultado
    $a=DBGetRow("select max(usernumber) as n from usertable",0,$c,
        "DBUserNumberMax(max(n))");
    if($cw){
        DBExec($c,"commit work", "DBUserNumberMax(commit)");
    }
    $n=$a["n"]+1;
    return $n;
}
//creamos un nuevo contest y insertamos problem fake, language, respuestas del juez
//funcion para create un nuevo sitio tambien esta incluido sitetimetable
function DBNewContest($usernumber, $param=array(), $c=null){
    $cw=false;
    if($c==null){
        $cw=true;
        $c=DBConnect();
        DBExec($c,"begin work","DBNewContest(begin)");
    }
    //no retorna un array asociativo el primer resultado
    $a=DBGetRow("select max(contestnumber) as contest from contesttable",0,$c,
        "DBNewContest(max(contest))");
    if($a==null) $n=1;
    else $n=$a["contest"]+1;

    $ac=array('name','startdate','duration','lastmileanswer','lastmilescore','penalty','updatetime','contestip','contestscorelevel');//'active'

    $type['startdate']=1;
    $type['duration']=1;
    $type['lastmileanswer']=1;
    $type['lastmilescore']=1;
    $type['penalty']=1;
    $type['updatetime']=1;

    $type['contestscorelevel']=1;//sitescorelevel

    foreach ($ac as $key) {
        if(isset($param[$key]) && (!isset($type[$key]) || is_numeric($param[$key])))
            $$key=myhtmlspecialchars($param[$key]);
        else
            $$key="";
    }

    if($name=="") $name="Contest";
    if($startdate=="") $startdate="EXTRACT(EPOCH FROM now())+600";//hoy mas 10 minutos
    if($duration=="") $duration=300*60;//5 horas de duracion
    if($lastmileanswer=="") $lastmileanswer=285*60;//4:45 horas
    if($lastmilescore=="") $lastmilescore=240*60;//4:20 horas
    if($penalty=="") $penalty=20*60;//20 minutos

    if($contestip=="") $contestip="127.0.0.1/salomon";
    if($contestscorelevel=="") $contestscorelevel=3;
    //
    $t=time();
    if($updatetime=="") $updatetime=$t;

    //if($active=="")
    $active="t";
    //para ver resultados
    $activeresult="f";
    $private="f";
    if($_SESSION["usertable"]["usertype"]=="team"){
        $judge=0;
    }else{
        $judge=$usernumber;
    }
    DBExec($c,"insert into contesttable (contestnumber, usernumber, contestname, conteststartdate, contestduration, ".
        "contestlastmileanswer, contestlastmilescore, contestpenalty, ".
        "contestmaxfilesize, contestactive, contestprivate, contestactiveresult, ".
        "contestip, contestpermitlogins, contestautoend, contestjudging, ".
        "contestglobalscore, contestscorelevel, contestnextuser, contestnextrun, ".
        "updatetime) values ($n, $usernumber, '$name', ".
        "$startdate, $duration, $lastmileanswer, ".
        "$lastmilescore, $penalty, 100000, '$active', '$private', '$activeresult', ".
        "'$contestip', 't', 't', $judge, ".
        "$n, $contestscorelevel, 0, 0, ".
        "$updatetime)","DBNewContest(insert contest)");

    $ct=DBContestInfo($n,$c);
    $param=array();
    $param['contest']=$n;//$contest;
    //$param['usernumber']=$usernumber;
    //$param['site']=$number;
    $param['start']=$ct["conteststartdate"];
    DBRenewContestTime($param,$c);//funcion para verificar los datos de contest,site, en sitetimetable, si no hay registro en la tabla inserta new


    //fin de sitio




    if($cw){
        DBExec($c,"commit work", "DBNewContest(commit)");
    }
    LOGLevel("User ".$_SESSION["usertable"]["username"]."/ created a new contest ($n).",2);
    return $n;
}
?>
