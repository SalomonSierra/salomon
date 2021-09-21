<?php

//funcion para la tabla lantable en la base de datos
function DBDropLangTable(){
    $c=DBConnect();
    $r=DBExec($c,"drop table if exists \"langtable\"","DBDropLangTable(drop table)");
}
//funcion para crear la tabla langtable
function DBCreateLangTable(){
    $c=DBConnect();
    $conf=globalconf();
    if($conf["dbuser"]=="") $conf["dbuser"]="salomonuser";
    $r=DBExec($c,"
    CREATE TABLE \"langtable\"(

        \"langnumber\" int4 NOT NULL,             --(id del lenguaje)
        \"langname\" varchar(50) NOT NULL,         --(nombre del lenguaje)
        \"langextension\" varchar(20) NOT NULL,          --(extension del lenguaje)
        \"updatetime\" int4 DEFAULT EXTRACT(EPOCH FROM now()) NOT NULL, --(indica la ultima actualizacion)
        CONSTRAINT \"lang_pkey\" PRIMARY KEY (\"langnumber\")
        )","DBCreateLangTable(create table)");
    $r=DBExec($c,"REVOKE ALL ON \"langtable\" FROM PUBLIC","DBCreateLangTable(revoke public)");
    $r=DBExec($c,"GRANT ALL ON \"langtable\" TO \"".$conf["dbuser"]."\"","DBCreateLangTable(grant salomonuser)");//asignando privilegio a dbuser
    $r=DBExec($c,"CREATE INDEX \"lang_index\" ON \"langtable\" USING btree ".
    "(\"langnumber\" int4_ops)","DBCreateLangTable(create lang_index)");
    $r=DBExec($c,"CREATE INDEX \"lang_index2\" ON \"langtable\" USING btree ".
    "(\"langname\" varchar_ops)","DBCreateLangTable(create lang_index2)");
    //creando index
}

// recibe el número del concurso
// devuelve una matriz, donde cada línea tiene el número de atributos (número de idioma)
//y el nombre (nombre del idioma) modificado
function DBGetLanguages() {
	$c = DBConnect();
	$r = DBExec($c, "select distinct l.langnumber as number, l.langname as name, l.langextension as extension from langtable as l " .
				"where l.langname !~ '(DEL)' order by l.langnumber", "DBGetLanguages(get lang)");
	$n = DBnlines($r);
	$a = array();
	for ($i=0;$i<$n;$i++)
		$a[$i] = DBRow($r,$i);
	return $a;
}

//recibe el numero de concurso y el numero de respuesta y lo elimina si su tipo no es falso
//actualizar la tabla langtable namelan añade con (DEL) y tambien el runtable
function DBDeleteLanguage($contestnumber, $param, $c=null){
    $ac=array('number');
    foreach ($ac as $key) {
        if(!isset($param[$key])){
            MSGError("DBDeleteLanguage param error: $key not found");
            return false;
        }
        //elimina los caracteres que no son ASSCI y replaza caracteres como *"$&().. con _ y otro ' " lo remplaza con barra invertida
        $$key=sanitizeFilename($param[$key]);
    }
    $cw=false;
    if($c==null){
        $cw=false;
        $c=DBConnect();
        DBExec($c,"begin work","DBDeleteLanguage(transaction)");
    }
    $sql="select *from langtable where langnumber=$number and contestnumber=$contestnumber";
    $r=DBExec($c,$sql."for update","DBDeleteLanguage(get lang for update)");
    if(DBnlines($r)>0){
        $a=DBRow($r,0);//retorna en array asocciotivo dando el resutado de la consulta y posicion
        $r=DBExec($c,"update langtable set langname='".$a["langname"]."(DEL)', updatetime=".time().
            "where contestnumber=$contestnumber and langnumber=$number ".
            "","DBDeleteLanguage(update)");
        $r=DBExec($c,"select runnumber as number, runsitenumber as site from runtable where contestnumber=$contestnumber and runlangnumber=$number for update");
        $n=DBnlines($r);
        for($i=0;$i<$n;$i++){
            $a=DBRow($r,$i);
            //actualizar la tabla runtable a status deleted y guarda una nueva tarea en tasktable
            //obtenido datos de tabla problemtable y answertable del mismo usuario
            DBRunDelete($a["number"],$a["site"],$contestnumber,$_SESSION["usertable"]["usernumber"],$_SESSION["usertable"]["usersitenumber"]);
        }
    }
    if($cw) DBExec($c,"commit","DBDeleteLanguage(commit)");
    LOGLevel("Language $number deleted (user=".$_SESSION["usertable"]["username"].
        "/".$_SESSION["usertable"]["usersitenumber"].")",2);
    return true;
}

//es para actulizar o insertar un nuevo registro el name y extension en la tabla langtable
function DBNewLanguage($param, $c=null){
    if(isset($param["action"]) && $param["action"]=="delete"){
        //actualizar la tabla langtable namelan añade con (DEL) y tambien el runtable
        return DBDeleteLanguage($contestnumber, $param, $c);
    }
    if(isset($param['langnumber']) && !isset($param['number'])) $param['number']=$param['langnumber'];
    if(isset($param['langname']) && !isset($param['name'])) $param['name']=$param['langname'];
    if(isset($param['langextension']) && !isset($param['extension'])) $param['extension']=$param['langextension'];

    $ac=array('number','name');
    $ac1=array('updatetime','extension');
    $type['number']=1;
    $type['updatetime']=1;
    $extension='';
    foreach ($ac as $key) {
        if(!isset($param[$key]) || $param[$key]==""){
            MSGError("DBNewLanguage param error: $key not found");
            return false;
        }
        if(isset($type[$key]) && !is_numeric($param[$key])){
            MSGError("DBNewLanguage param error: $key is not numeric");
            return false;
        }
        $$key=sanitizeFilename($param[$key]);
    }
    $updatetime=-1;
    foreach ($ac1 as $key) {
        if(isset($param[$key])){
            $$key=sanitizeFilename($param[$key]);
            if(isset($type[$key]) && !is_numeric($param[$key])){
                MSGError("DBNewLanguage param error: $key is not numeric");
                return false;
            }
        }
    }
    $t=time();
    if($updatetime<=0)
        $updatetime=$t;
    $cw=false;
    if($c == null){
        $cw=true;
        $c=DBConnect();
        DBExec($c,"begin work","DBNewLanguage(transaction)");
    }
    $sql2="select *from langtable where langnumber=$number";

    $r=DBExec($c,$sql2." for update","DBNewLanguage(get lang)");
    $n=DBnlines($r);
    $ret=1;
    if($n==0){
        DBExec($c,"insert into langtable (langnumber, langname, langextension) values".
            "($number, '$name', '$extension')","DBNewLanguage(insert lang)");
        $s="created";
    }else{
        $lr=DBRow($r,0);
        $t=$lr['updatetime'];
        if($updatetime > $t){
            if($name!="")
                DBExec($c, "update langtable set langname='$name', updatetime=$updatetime where ".
                    "langnumber=$number","DBNewLanguage(update lang)");
            if($extension!="")
                DBExec($c, "update langtable set langextension='$extension', updatetime=$updatetime where ".
                    "langnumber=$number","DBNewLanguage(update lang)");
        }
        $s="updated";
    }
    if($cw)
        DBExec($c,"commit work","DBNewLanguage(commit)");
    if($s=="created" || $updatetime>$t){
        LOGLevel("Language $number create",2);

        $ret=2;
    }
    return $ret;
}
?>
