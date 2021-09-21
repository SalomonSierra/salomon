<?php

//funcion para la tabla answertable en la base de datos
function DBDropAnswerTable(){
    $c=DBConnect();
    $r=DBExec($c,"drop table if exists \"answertable\"","DBDropAnswerTable(drop table)");
}
//funcion para crear la tabla answertable
function DBCreateAnswerTable(){
    $c=DBConnect();
    $conf=globalconf();
    if($conf["dbuser"]=="") $conf["dbuser"]="salomonuser";
    $r=DBExec($c,"
    CREATE TABLE \"answertable\"(

        \"answernumber\" int4 NOT NULL,             --(id de la respuesta)
        \"runanswer\" varchar(50) NOT NULL,         --(respuesta dada en el juicio)
        \"yes\" bool DEFAULT 'f' NOT NULL,          --(bandera para indicar si cuenta el punto)
        \"fake\" bool DEFAULT 'f' NOT NULL,         --(bandera para indicar si la respuesta es valida)
        \"updatetime\" int4 DEFAULT EXTRACT(EPOCH FROM now()) NOT NULL, --(indica la ultima actualizacion)
        --(posibles tipos de respuestas de los jueces. la bandera es h para indicar si la respuesta es YES o NO)
        CONSTRAINT \"answer_pkey\" PRIMARY KEY (\"answernumber\")
        )","DBCreateAnswerTable(create table)");
    $r=DBExec($c,"REVOKE ALL ON \"answertable\" FROM PUBLIC","DBCreateAnswerTable(revoke public)");
    $r=DBExec($c,"GRANT ALL ON \"answertable\" TO \"".$conf["dbuser"]."\"","DBCreateAnswerTable(grant salomonuser)");//asignando privilegio a dbuser
    $r=DBExec($c,"CREATE UNIQUE INDEX \"answer_index\" ON \"answertable\" USING btree ".
    "(\"answernumber\" int4_ops)","DBCreateAnswerTable(create index)");
    //creando index
}


?>
