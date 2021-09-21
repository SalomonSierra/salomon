<?php
//toda la configuracion que tiene del sistema a db y user salomon y auto judge
function globalconf(){
    $conf["dbencoding"]="UTF8";
    $conf["dbclientenc"]="UTF8";
    $conf["doenc"]=false;

    $conf["dblocal"]="false";
    $conf["dbhost"]="localhost";
    $conf["dbport"]="5432";

    $conf["dbname"]="salomondb";

    $conf["dbuser"]="salomonuser";//tiene privelegios para salomon user
    $conf["dbpass"]="evelyn123";

    $conf["dbsuperuser"]="salomonuser";//privelegio para salomon user
    $conf["dbsuperpass"]="evelyn123";
    $conf["basepass"]="salomon";
    /*
    clave secreta que se utilizara en los encabezados http debe configurarlo ocn cualquier secuencia aleatoria
    lo suficientemente grande
    */
    $conf["key"]="FfHf7nMddw33E9fEzcWw";//PROBLEM
    //el siguiente campo es itilizado po el script de autojuez
    //configurelo con la ip de la computadora que ejecuta el script
    //el verdadero proposito de esto es solo diferenciar entre
    //autojueces conando se utilizan varias computadoreas como autojuecis
    $conf["ip"]="local";
    return $conf;
}




?>
