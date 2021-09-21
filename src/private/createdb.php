<?php

$ds=DIRECTORY_SEPARATOR;
if($ds=="") $ds="/";
//si es legible
if(is_readable('/etc/salomon.conf')){
    //parse_ini_file() carga el fichero ini especificado en filename,
    //y devuelve las configuraciones que hay en él a un array asociativa.
    //La estructura del ficher0 ini es la misma que la de php.ini.
    $pif=parse_ini_file('/etc/salomon.conf');
    $salomondir = trim($pif['salomondir'].$ds.'src');
}else{
    $salomondir=getcwd();//obtiene el directorio actual
}
//tenemos la ruta
if(is_readable($salomondir.$ds.'..'.$ds.'db.php')){
    require_once($salomondir.$ds.'..'.$ds.'db.php');
    @include_once($salomondir.$ds.'..'.$ds.'version.php');
}else{
    if(is_readable($salomondir.$ds.'db.php')){
        require_once($salomondir.$ds.'db.php');
        @include_once($salomondir.$ds.'version.php');
    }else{
        echo "unable to find db.php";
        exit;
    }
}
/*
//php_sapi_name devuelve el tipo de interfaz que har entre php y el servidor
if(getIP()!='UNKNOWN' || php_sapi_name()!=="cli") exit;
//estable los valores durante la ejecucion del script
ini_set('memory_limit','600M');
ini_set('output_buffering','off');
ini_set('implicit_flush','on');
@ob_end_flush();
if(system('test "`id -u`" -eq "0"',$retval)===false || $retval!=0){
    echo "Must be run as root";
    exit;
}
echo "\n This will erase all the data in your salomondb database.";
echo "\n***** YOU WILL LOSE WHATEVER YOU HAVE THERE!!! *****";
echo "\nType Yes and press return to continue or anything else will abort it:";

$resp=strtoupper(trim(fgets(STDIN)));//captura el puentero de yes
if($resp !='YES') exit;
*/



echo "\n dropping database\n";
DBDropDatabase();
echo "creating database\n";
DBCreateDatabase();

echo "creating tables\n";
DBCreateUserTable();
DBCreateContestTable();
//DBCreateSiteTable();
//DBCreateSiteTimeTable();
DBCreateContestTimeTable();


DBCreateLogTable();
//..
DBCreateProblemTable();
DBCreateProblemContestTable();
DBCreateAnswerTable();
//DBCreateTaskTable(); //desarrollo
DBCreateLangTable();
DBCreateRunTable();


//DBCreateClarTable(); en desarrollo
//DBCreateBkpTable(); aun en desarrollo
echo "creating initial fake contest\n";
DBFakeContest();

?>
