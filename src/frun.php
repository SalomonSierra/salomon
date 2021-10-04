<?php

//function para eliminar la tabla runtable de la base de datos
function DBDropRunTable(){
    $c=DBConnect();
    $r=DBExec($c,"drop table if exists \"runtable\"","DBDropRunTable(Drop table)");
}
//funcion para crear la tabla runtable
function DBCreateRunTable(){
    $c=DBConnect();
    $conf=globalconf();
    if($conf["dbuser"]=="") $conf["dbuser"]="salomonuser";
    $r=DBExec($c, "
    CREATE TABLE \"runtable\" (
        \"contestnumber\" int4 NOT NULL,                            -- (id de competencia)

        \"runnumber\" int4 NOT NULL,                                -- (numero de runtable)
        \"usernumber\" int4 NOT NULL,                               -- (id del usuario o equipo)
        \"rundate\" int4 NOT NULL,                                  -- (dia/hora de presentacion en el lugar de origen)
        \"rundatediff\" int4 NOT NULL,                              -- (diferencia entre el inicio de la competenica y el dia / hora de presentacion en segundos)
        \"rundatediffans\" int4 NOT NULL,                           -- (diferencia entre el inicio de la competicion y el dia hora de la correccion en seg.)
        \"runproblem\" int4 NOT NULL,                               -- (id del problema)
        \"runfilename\" varchar(200) NOT NULL,                      -- (nombre del archivo enviado)
        \"rundata\" oid NOT NULL,                                   -- (codigo fuente del archivo enviado)
        \"runanswer\" int4 DEFAULT 0 NOT NULL,                      -- (respuesta dada en el juicio)
        \"runstatus\" varchar(20) NOT NULL,                         -- (estado de envio: abierto, juzgado,eliminado,juzdado+)
        \"runjudge\" int4 DEFAULT NULL,                             -- (juzgar quien esta juzgando)



        \"runlangnumber\" int4 NOT NULL,                            -- (lenguaje de codigo fuente)

        \"autoip\" varchar(20) DEFAULT '',                          -- los campos automaticos -- son para correccion automatica
        \"autobegindate\" int4 DEFAULT NULL,
        \"autoenddate\" int4 DEFAULT NULL,
        \"autoanswer\" text DEFAULT '',
        \"autostdout\" oid DEFAULT NULL,
        \"autostderr\" oid DEFAULT NULL,
        \"autostdoutuser\" oid DEFAULT NULL,                        -- (oid para el team)
        \"updatetime\" int4 DEFAULT EXTRACT(EPOCH FROM now()) NOT NULL, -- (indica la ultimo actualicion del registro)
        CONSTRAINT \"run_pkey\" PRIMARY KEY (\"contestnumber\",\"runnumber\"),
        CONSTRAINT \"contest_fk\" FOREIGN KEY (\"contestnumber\")
            REFERENCES \"contesttable\" (\"contestnumber\")
            ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
        CONSTRAINT \"user_fk\" FOREIGN KEY (\"usernumber\")
            REFERENCES \"usertable\" (\"usernumber\")
            ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
        CONSTRAINT \"problem_fk\" FOREIGN KEY (\"runproblem\")
            REFERENCES \"problemtable\" (\"problemnumber\")
            ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
        CONSTRAINT \"answer_fk\" FOREIGN KEY (\"runanswer\")
            REFERENCES \"answertable\" (\"answernumber\")
            ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
        CONSTRAINT \"lang_fk\" FOREIGN KEY (\"runlangnumber\")
            REFERENCES \"langtable\" (\"langnumber\")
            ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )","DBCreateRunTable(create table)");
        $r=DBExec($c,"REVOKE ALL ON \"runtable\" FROM PUBLIC","DBCreateRunTable(revoke public)");
        $r=DBExec($c,"GRANT ALL ON \"runtable\" TO \"".$conf["dbuser"]."\"","DBCreateRunTable(grant salomonuser)");//asignando privilegio a dbuser
        $r=DBExec($c,"CREATE UNIQUE INDEX \"run_index\" ON \"runtable\" USING btree ".
        "(\"contestnumber\" int4_ops, \"runnumber\" int4_ops)",
        "DBCreateRunTable(create run_index)");
        $r=DBExec($c,"CREATE INDEX \"run_index2\" ON \"runtable\" USING btree ".
        "(\"contestnumber\" int4_ops, \"usernumber\" int4_ops)",
        "DBCreateRunTable(create run_index2)");
        //creando index
        //\"runanswer1\" int4 DEFAULT 0 NOT NULL,                     -- (respuesta dada en el juicio)
        //\"runjudge1\" int4 DEFAULT NULL,                            -- (juez quien esta juezgando)
        //\"runjudgesite1\" int4 DEFAULT NULL,                        -- (juez quien esta juezgando)
        //\"runanswer2\" int4 DEFAULT 0 NOT NULL,                     -- (respuesta dada en el juicio)
        //\"runjudge2\" int4 DEFAULT NULL,                            -- (juez quien esta juzgando)
        //\"runjudgesite2\" int4 DEFAULT NULL,                        -- (juez quien esta juzgando)


}

///////////////////////////////funciones de ejecuciones///////////////////////////////////////////////////////
// responde a una contest. Reciba el número del contest, el sitio del usuario, el usuario, el sitio de ejecución,
// número de ejecución, número de respuesta (notificar usuario y puntuación de actualización).
// intenta cambiar el estado a 'juzgado'.
function DBChiefUpdateRun($contest, $usernumber, $runnumber, $answer) {
	return DBUpdateRunC($contest, $usernumber, $runnumber, $answer, 1);
}
//(contest,site,usernumber,site,number,7,connect)
//funcion para actulizar runtable runanswer1= y verifica si tiene globo o no para insertar un nuevo registro en tasktable
//y chief es igual a 1
//mejorado...
function DBUpdateRunO($contest, $usernumber, $runnumber, $answer, $c) {
    //(contest,site,usernumber,site,number,7,1,connect)
    ////funcion para actulizar runtable runanswer1= y verifica si tiene globo o no para insertar un nuevo registro en tasktable
    //1 para no añadir mas comparacion a la consulta
	return DBUpdateRunC($contest, $usernumber, $runnumber, $answer, 1, $c);
}
//
function DBUpdateRun($contest, $usersite, $usernumber, $runsite, $runnumber, $answer) {
	return DBUpdateRunC($contest, $usersite, $usernumber, $runsite, $runnumber, $answer, 0);
}
//(contest,site,usernumber,site,number,7,1,connect)
//funcion para actulizar runtable runanswer1= y verifica si tiene globo o no para insertar un nuevo registro en tasktable
//si todo es bien true caso contrario false
function DBUpdateRunC($contest, $usernumber, $runnumber, $answer, $chief, $c=null) {
	$bw = 0;
	if($c==null) {
		$bw = 1;
		$c = DBConnect();
		DBExec($c, "begin work", "DBUpdateRunC(transaction)");
	}
    //contact a staff 7=answer
	$a = DBGetRow("select * from answertable where answernumber=$answer",0,$c,
				  "DBUpdateRunC(get answer)");
	if ($a == null) {
		DBExec($c, "rollback work", "DBUpdateRunC(rollback)");
		MSGError("Problem with the answer table. Contact an admin now!");
        //No se puede juzgar una carrera porque no se encontró la respuesta
		LogLevel("Unable to judge a run because the answer was not found (run=$runnumber, ".
				 "contest=$contest, answer=$answer).",0);
		return false;
	}
	if ($a["fake"] == 't') {
		DBExec($c, "rollback work", "DBUpdateRunC(rollback)");
		MSGError("You must choose a valid answer.");//Debes elegir una respuesta válida.
        //Incapaz de juzgar una carrera debido a la respuesta falsa
		LogLevel("Unable to judge a run because of the fake answer (run=$runnumber, ".
				 "contest=$contest, answer=$answer).",0);
		return false;
	}
	$yes = $a["yes"];
	$b = DBContestClockInfo($contest, $c);
	if ($b == null) {
		exit;
	}

	$sql = "select * from runtable as r where r.contestnumber=$contest and " .
		"r.runnumber=$runnumber";
    //chief=1
	if ($chief != 1) {
		$sql .= " and (r.runstatus='judging' or r.runstatus='judged+')";
		//	"(r.runjudge1=$usernumber and r.runjudgesite1=$usersite)";

			//" (r.runjudge2=$usernumber and r.runjudgesite2=$usersite))";

        $tx = "Judge";
	} else $tx = "Chief";
	$r = DBExec ($c, $sql . " for update", "DBUpdateRunC(get run for update)");
	$n = DBnlines($r);
	if ($n != 1) {
		if($bw == 1) {
			DBExec($c, "rollback work", "DBUpdateRunC(rollback)");
			LogLevel("Unable to judge a run (maybe it was already judged or catched by a chief) " .
					 "(run=$runnumber, contest=$contest).",2);
			MSGError("Unable to judge the run (maybe it was already judged or catched by a chief)");
            //Incapaz de juzgar la carrera (tal vez ya fue juzgada o atrapada por un jefe)
		}
		return false;
	}
	$temp = DBRow($r,0);
	$t = $b["currenttime"];//tiempo actual

	$team=$temp["usernumber"];
    ////funcion para seleccionar la tabla runtable y answertable y saca yes si es t return true no false para globo
	if ($temp["runanswer"] != "")
		$tinhabalao = DBBalloon($contest, $temp["usernumber"], $temp["runproblem"], $c);
	else	$tinhabalao = false;

	/*if($temp["runjudge1"]==$usernumber && $temp["runjudgesite1"]==$usersite) {
		DBExec($c, "update runtable set runanswer1=$answer, updatetime=".time()." " .
			   "where contestnumber=$contest and runnumber=$runnumber and runsitenumber=$runsite",
               "DBUpdateRunC(update run judge1)");
		//$outra = $temp["runanswer2"];
		$outra = "no";
	}*/
	/*if($temp["runjudge2"]==$usernumber && $temp["runjudgesite2"]==$usersite) {
		DBExec($c, "update runtable set runanswer2=$answer, updatetime=".time()." " .
			   "where contestnumber=$contest and runnumber=$runnumber and runsitenumber=$runsite",
               "DBUpdateRunC(update run judge2)");
		$outra = $temp["runanswer1"];
	}*/
	$newstatus = 'judging';//juzgando
	if($chief == 1 ) {
		$newstatus = 'judged';//juzgado
		$tans = max($t,$temp['rundatediff']);//encotra el valor mas alto
		DBExec($c, "update runtable set runstatus='judged', " .
			   "runjudge=$usernumber, " .
			   "runanswer=$answer, rundatediffans=$tans, updatetime=".time()." " .
			   "where contestnumber=$contest and runnumber=$runnumber",
			   "DBUpdateRunC(update run)");
        //si tenemos blobo o no
		$tembalao = DBBalloon($contest, $temp["usernumber"], $temp["runproblem"],$c);

//	if ($runsite==$usersite) {
		if (!$tinhabalao && $tembalao) {
			if (($b = DBContestClockInfo($contest, $c)) == null)
				return true;
			$ta = $b["currenttime"];
			$tf = $b["contestlastmileanswer"];
			if ($ta < $tf || $ta > $b['contestduration']) {
                //falta..
				$u = DBUserInfo ($team, $c);
				if($u['usertype']=='team') {
                    //datos de un problema de todos lo sitios
					$p = DBGetProblemData ($temp["runproblem"],$c);
                    //debe tener un globo para el problema
                    //////inserta o actulizar una nueva tarea a la tabla tasktable y el autotask es true++
					/*DBNewTask_old ($contest, $runsite, $team,
						       escape_string("\"" . $u["username"] ."\" must have a balloon for problem " .
								     $p[0]["problemname"] . ": " . $p[0]["fullname"]),
						       "", "", "t", $p[0]["color"], $p[0]["colorname"], $c);
                    */
                }
			} else {
                //score se cogela..
                //OCULTO: usuario = $ equipo, sitio = $ sitio de ejecución, concurso = $ concurso tendría un globo para problema =
				LOGError("DBUpdateRunC: OCULTO: user=$team,contest=$contest would have a balloon for problem=" .  $temp["runproblem"]);
			}
		} else if ($tinhabalao && !$tembalao) {
			$u = DBUserInfo ($team, $c);
			if($u['usertype']=='team') {
				$p = DBGetProblemData ($temp["runproblem"],$c);
                //debe tener _NO_ globo por problema
                //Verifíquelo y elimínelo, si es necesario.
				/*DBNewTask_old ($contest, $runsite, $team, escape_string("\"" .
											$u["username"] ."\" must have _NO_ balloon for problem " . $p[0]["problemname"] .
											": " . $p[0]["fullname"]). ". Please verify and remove it, if needed.", "", "",
					       "t", $p[0]["color"], $p[0]["colorname"], $c);*/
			}
		}
//	}
	}

	if($bw == 1) {
		DBExec($c, "commit work", "DBUpdateRunC(commit)");
		LOGLevel("Ejecucion actualizada (run=$runnumber,user=$team,contest=$contest,newstatus=$newstatus,".
				 "judge=$usernumber,answer=$answer(".$a["runanswer"].")).", 3);
	}
	return true;
}
// devuelve una ejecución que estaba siendo respondida. Recibirá el número de ejecución,
// el número de sitio de ejecución y el número de concurso.
// intenta cambiar el estado a 'openrun'. Si no puede devolver falso
//jefe ejecutar terminar
//modificado...
function DBChiefRunGiveUp($number,$contest) {
	return DBRunGiveUp($number,$contest,-1);//-1,-1 añade mas comparaciones
}
//funcion intenta cambiar el estado a 'openrun'. Si no puede devolver falso
//modificado...
function DBRunGiveUp($number,$contest,$usernumber) {
	$c = DBConnect();
	DBExec($c, "begin work", "DBRunGiveUp(transaction)");
	$sql = "select * from runtable as r where r.contestnumber=$contest and " .
		"r.runnumber=$number";
	if ($usernumber != 1) {
		$sql .= " and (r.runstatus='judging' or r.runstatus='judged+')";

		$tx = "Judge";
	} else $tx = "Chief";
	$r = DBExec ($c, $sql . " for update", "DBRunGiveUp(get run for update)");
	$n = DBnlines($r);

    if ($n != 1) {

		DBExec($c, "rollback work", "DBRunGiveUp(rollback)");
        //No se puede devolver una carrera (tal vez el tiempo de espera o un jefe lo devolvió primero)
		LogLevel("Unable to return a run (maybe the timeout or a chief returned it first). ".
				 "(run=$number, contest=$contest)",2);
		return false;
	}

	$temp = DBRow($r, 0);
    //si tiene globo o no
	$tinhabalao = DBBalloon($contest, $temp["usernumber"], $temp["runproblem"],$c);

	$outra = -1;
	/*if($temp["runjudge1"]==$usernumber && $temp["runjudgesite1"]==$usersite) {
		DBExec($c, "update runtable set runjudge1=NULL, runjudgesite1=NULL, runanswer1=0 " .
			   " where contestnumber=$contest and runnumber=$number and runsitenumber=$site",
			   "DBRunGiveUp(update run judge1)");
		//$outra = $temp['runanswer2'];
		$outra = "no";
	}*/
	/*if($temp["runjudge2"]==$usernumber && $temp["runjudgesite2"]==$usersite) {
		DBExec($c, "update runtable set runjudge2=NULL, runjudgesite2=NULL, runanswer2=0 " .
			   " where contestnumber=$contest and runnumber=$number and runsitenumber=$site",
               "DBRunGiveUp(update run judge1)");
		$outra = $temp['runanswer1'];
	}*/

	$newstatus="judging";
	if($temp["runstatus"]=="judged" || $temp["runstatus"]=="judged+") {
   	    DBExec($c, "update runtable set runstatus='judged+', " .
			   ($tx=="Chief" ? "": "") .
			   " updatetime=" .
			   time()." where contestnumber=$contest and runnumber=$number",
			   "DBRunGiveUp(update run)");
		$newstatus='judged+';
	} else {
		if($outra == 0 || $tx=="Chief") {
			DBExec($c, "update runtable set runstatus='openrun', runanswer=0, runjudge=NULL, ".
				   "updatetime=" .
				   time()." where contestnumber=$contest and runnumber=$number",
				   "DBRunGiveUp(update run)");
            $newstatus='openrun';
		}
	}
    //para globo
	$tembalao = DBBalloon($contest, $temp["usernumber"], $temp["runproblem"],$c);
	if ($tinhabalao && !$tembalao) {
		$u = DBUserInfo ($temp["usernumber"], $c);
		if($u['usertype']=='team') {
			$p = DBGetProblemData ($temp["runproblem"],$c);
			/*DBNewTask_old ($contest, $site, $temp["usernumber"], escape_string("\"" .
											   $u["username"] ."\" must have _NO_ balloon for problem " . $p[0]["problemname"] .
											   ": " . $p[0]["fullname"]), "", "", "t", $p[0]["color"], $p[0]["colorname"], $c);*/
		}
	}

	DBExec($c, "commit work", "DBRunGiveUp(commit)");
	LOGLevel("Ejecucion regreso (run=$number, contest=$contest, user=$usernumber, ".
			 "newstatus=$newstatus", 3);
	return true;
}
//actualizar la tabla runtable a status deleted y guarda una nueva tarea en tasktable
//obtenido datos de tabla problemtable y answertable del mismo usuario
function DBRunDelete($number,$contest,$user,$cc=null){
    if($cc==null){
        $c=DBConnect();
        DBExec($c,"begin work","DBRunDelete(transaction)");
    }else $c=$cc;
    $sql="select *from runtable as r where r.contestnumber=$contest and ".
    "r.runnumber=$number";
    $r=DBExec($c,$sql." for update","DBRunDelete(get run for update)");
    $n=DBnlines($r);
    if($n!=1){
        if($cc == null)
            DBExec("rollback work","DBRunDelete(rollback)");
        LogLevel("Unable to delete a run. ".
            "(run=$number, contest=$contest)",1);
        return false;
    }
    $temp=DBRow($r,0);
    //funcion score.php
    //funcion para seleccionar la tabla runtable y answertable y saca yes si es t return true no false
    $tinhabalao=DBBalloon($contest, $temp["usernumber"], $temp["runproblem"],$c);

    DBExec($c,"update runtable set runstatus='deleted', runjudge=$user, updatetime=".
    time()." where contestnumber=$contest and runnumber=$number",
    "(DBRunDelete(update run)");

    $tembalao=DBBalloon($contest, $temp["usernumber"], $temp["runproblem"],$c);


    if($tinhabalao && !$tembalao){

        $u = DBUserInfo($temp["usernumber"],$c);
        if($u['usertype']=='team'){
            //recibe un numero de contest y problema
            //devuelve todos los datos relacionados con el problema en cada liena de la matriz y cada linea representa el hecho
            //que hay mas de un archivo de entrada/salida
            //No devuelve datos sobre problemas falsos, como no deberian haberlo hecho.
            $p=DBGetProblemData($temp["runproblem"],$c);
            //escape_string funcion una cadena para query must have_NO_balloon for problem(no debe tener globo por problema)
            ////inserta o actulizar una nueva tarea a la tabla tasktable y el autotask es true++
            /*DBNewTask_old($contest, $site, $temp["usernumber"], escape_string("\"".
                            $u["username"]."\" must have _NO_ balloon for problem ".$p[0]["problemname"].
                            ": ".$p[0]["fullname"]),"", "", "t", $p[0]["color"], $p[0]["colorname"],$c);*/

        }
    }

    if($cc==null)
        DBExec($c,"commit work","DBRunDelete(commit)");
    //LOGLevel("Run deleted (run=$number, site=$site, contest=$contest, user=$user(site=$usersite)).",2);//3
    //LOGLevel("Run deleted",2);//3

    return true;
}

// corre para juzgar. Reciba el número de ejecución, el número de sitio y el número de concurso.
// intenta cambiar el estado a 'juzgar' y, si tiene éxito, devuelve una matriz de datos de ejecución. Si no puedes
//retorna false
//actulizar la tabla runtable status a judging y otros
//Devuelve en la matriz: contestnumber, sitenumber, number, timestamp (em segundos), problemname,
//			problemnumber, language, sourcename, sourceoid, (langscript, infiles, solfiles)
//actulizado..
function DBChiefGetRunToAnswer($number,$contest) {
    //actulizar la tabla runtable status a judging y otros devuleve el no actual de runtable de una ejecucion
	return DBGetRunToAnswerC($number,$contest,1);
}
//
function DBGetRunToAnswer($number,$site,$contest) {
	return DBGetRunToAnswerC($number,$site,$contest,0);
}
//actulizar la tabla runtable status a judging y otros devuleve el no actual de runtable de una ejecucion
//para trabajar obtener
//mejorado
function DBGetRunToAnswerC($number,$contest,$chief) {
	$c = DBConnect();
	DBExec($c, "begin work", "DBGetRunToAnswerC(transaction)");

	/*$sql = "select r.contestnumber as contestnumber, r.runsitenumber as sitenumber, r.runanswer as answer, " .
		"r.runjudge as judge, r.runjudgesite as judgesite, " .

		"r.runnumber as number, r.rundatediff as timestamp, r.runstatus as status, " .
		"r.rundata as sourceoid, r.runfilename as sourcename, l.langnumber as langnumber, " .
		"p.problemname as problemname, p.problemnumber as problemnumber, l.langname as language, l.langextension as extension, " .
		"r.autoip as autoip, r.autobegindate as autobegin, r.autoenddate as autoend, r.autoanswer as autoanswer, ".
		"r.autostdout as autostdout, r.autostderr as autostderr ".

		"from runtable as r, problemtable as p, langtable as l " .
		"where r.contestnumber=$contest and p.contestnumber=r.contestnumber and " .
		"r.runproblem=p.problemnumber and r.runsitenumber=$site and " .
		"r.runlangnumber=l.langnumber and r.contestnumber=l.contestnumber and " .
		"r.runnumber=$number";*/
    $sql = "select r.contestnumber as contestnumber, r.runanswer as answer, " .
    	"r.runjudge as judge, " .

    	"r.runnumber as number, r.rundatediff as timestamp, r.runstatus as status, " .
    	"r.rundata as sourceoid, r.runfilename as sourcename, l.langnumber as langnumber, " .
    	"p.problemname as problemname, p.problemnumber as problemnumber, l.langname as language, l.langextension as extension, " .
    	"r.autoip as autoip, r.autobegindate as autobegin, r.autoenddate as autoend, r.autoanswer as autoanswer, ".
    	"r.autostdout as autostdout, r.autostderr as autostderr ".

    	"from runtable as r, problemtable as p, langtable as l " .
    	"where r.contestnumber=$contest and " .
    	"r.runproblem=p.problemnumber and " .
    	"r.runlangnumber=l.langnumber and " .
    	"r.runnumber=$number";
	if ($chief != 1) {
		$sql .= " and (r.runstatus='openrun' or " .
		  "(r.runstatus='judged+' and r.runjudge is NULL) or " .
		  "((r.runstatus='judging' or r.runstatus='judged+')))";

		$tx = "Judge";
	} else $tx = "Chief";
	$r = DBExec ($c, $sql . " for update", "DBGetRunToAnswerC(get run/prob/lang for update)");
	$n = DBnlines($r);
	if ($n != 1) {
		DBExec($c, "rollback work", "DBGetRunToAnswerC(rollback)");
        //Incapaz de correr (tal vez otro juez lo hizo primero).
		LogLevel("Unable to get a run (maybe other judge got it first). (run=$number, ".
				 "contest=$contest)",2);
		return false;
	}
	$a = DBRow($r,0);

	if ($chief != 1) {
		$upd="";
	   	if($a["status"]=="openrun") $upd="runstatus='judging',";

		/*if(($a["judge1"]!=$_SESSION["usertable"]["usernumber"] ||
			$a["judgesite1"]!=$_SESSION["usertable"]["usersitenumber"])) {
		    if($a["judge1"]=='' && $a['judgesite1']=='') {
		    	DBExec($c, "update runtable set runjudge1=" . $_SESSION["usertable"]["usernumber"] .
					   ",$upd updatetime=".time().", " .
					   "runjudgesite1=" . $_SESSION["usertable"]["usersitenumber"] . " " .
					   "where contestnumber=$contest and runnumber=$number and runsitenumber=$site",
					   "DBGetRunToAnswerC(update run judge1)");
		    }
		}*/
	}

	DBExec($c, "commit work", "DBGetRunToAnswerC(commit)");
	LOGLevel("El usuario tiene ejecucion (run=$number, contest=$contest, user=".
			 $_SESSION["usertable"]["usernumber"].")).", 3);
	return $a;
}
//contest activa y ip cd conf local
//capturamos la informacion de runtable problemtable langtable de una competencia donde autoip de vacio
//y luego actulizamos la tabla runtable autoip=ip autobegindate=$t, autoenddate=null, autoanswer=null,
// autostdout=null, autostderr=null
//para trabajar para juez no importa
//mejorado en abajo
/*
function DBGetRunToAutojudging($contest, $ip) {
	$c = DBConnect();
	DBExec($c, "begin work", "DBGetRunToAnswerC(transaction)");
    //
	$sql = "select r.contestnumber as contest, r.runsitenumber as site, r.runanswer as answer, " .
		"r.runnumber as number, r.rundatediff as timestamp, r.runstatus as status, " .
		"r.rundata as sourceoid, r.runfilename as sourcename, l.langnumber as langnumber, " .
		"p.problemname as problemname, p.problemnumber as problemnumber, l.langextension as extension, l.langname as language, " .
		"p.problembasefilename as basename, ".
		"p.probleminputfilename as inputname, p.probleminputfile as inputoid, " .
		"r.autoip as autoip, r.autobegindate as autobegin, r.autoenddate as autoend, r.autoanswer as autoanswer, ".
		"r.autostdout as autostdout, r.autostderr as autostderr ".
		"from runtable as r, problemtable as p, langtable as l " .
		"where r.contestnumber=$contest and p.contestnumber=r.contestnumber and " .
		"r.runproblem=p.problemnumber and r.runlangnumber=l.langnumber and ".
		"r.contestnumber=l.contestnumber and " .
		"r.autoip='' order by r.runnumber for update limit 1";
	$r = DBExec ($c, $sql, "DBGetRunToAutoJudging(get run/prob/lang for update)");
	$n = DBnlines($r);
	if ($n < 1) {
		DBExec($c, "rollback work", "DBGetRunToAutoJudging(rollback)");
		return false;
	}
	$a = DBRow($r,0);
	$t = time();
    //actualizamos la tabla runtable
	DBExec($c, "update runtable set autoip='" . $ip . "', " .
		   "autobegindate=$t, autoenddate=null, autoanswer=null, autostdout=null, autostderr=null, " .
		   "updatetime=$t " .
		   "where contestnumber=${a["contest"]} and runnumber=${a["number"]} and runsitenumber=${a["site"]}",
		   "DBGetRunToAutojudging(update run)");

	DBExec($c, "commit work", "DBGetRunToAutojudging(commit)");
    //Autojugar consiguió una carrera
	LOGLevel("Autojuez consiguio una ejecucion (run=${a["number"]}, site=${a["site"]}, contest=${a["contest"]})", 3);
	return $a;
}*/
//mejorado
function DBGetRunToAutojudging($ip) {
	$c = DBConnect();
	DBExec($c, "begin work", "DBGetRunToAnswerC(transaction)");
    //
	$sql = "select r.contestnumber as contest, r.runanswer as answer, " .
		"r.runnumber as number, r.rundatediff as timestamp, r.runstatus as status, " .
		"r.rundata as sourceoid, r.runfilename as sourcename, l.langnumber as langnumber, " .
		"p.problemname as problemname, p.problemnumber as problemnumber, l.langextension as extension, l.langname as language, " .
		"p.problembasefilename as basename, ".
		"p.probleminputfilename as inputname, p.probleminputfile as inputoid, " .
		"r.autoip as autoip, r.autobegindate as autobegin, r.autoenddate as autoend, r.autoanswer as autoanswer, ".
		"r.autostdout as autostdout, r.autostderr as autostderr ".
		"from runtable as r, problemtable as p, langtable as l " .
		"where " .
		"r.runproblem=p.problemnumber and r.runlangnumber=l.langnumber and ".

		"r.autoip='' order by r.runnumber for update limit 1";
	$r = DBExec ($c, $sql, "DBGetRunToAutoJudging(get run/prob/lang for update)");
	$n = DBnlines($r);
	if ($n < 1) {
		DBExec($c, "rollback work", "DBGetRunToAutoJudging(rollback)");
		return false;
	}
	$a = DBRow($r,0);
	$t = time();
    //actualizamos la tabla runtable
	DBExec($c, "update runtable set autoip='" . $ip . "', " .
		   "autobegindate=$t, autoenddate=null, autoanswer=null, autostdout=null, autostderr=null, " .
		   "updatetime=$t " .
		   "where contestnumber=${a["contest"]} and runnumber=${a["number"]}",
		   "DBGetRunToAutojudging(update run)");

	DBExec($c, "commit work", "DBGetRunToAutojudging(commit)");
    //Autojugar consiguió una carrera
	LOGLevel("Autojuez consiguio una ejecucion (run=${a["number"]}, ontest=${a["contest"]})", 3);
	return $a;
}
//actulizar la respuesta en runtable en autoended todo y los stderr stdout devuelve true
//mejorado...
function DBUpdateRunAutojudging($contest, $number, $ip, $answer, $stdout, $stderr, $stdoutuser, $retval=0) {

    if($retval=="") $retval=0;
	$c = DBConnect();
	DBExec($c, "begin work", "DBUpdateRunAutojudging(transaction)");
	$sql = "select * from runtable as r " .
		"where r.contestnumber=$contest and r.runnumber=$number and " .
		"r.autoip='$ip'";
	$r = DBExec ($c, $sql . " for update", "DBUpdateRunAutoJudging(get run for update)");
	$n = DBnlines($r);

    if ($n != 1) {
		DBExec($c, "rollback work", "DBUpdateRunAutoJudging(rollback)");
        //Incapaz de hacer una prueba para autojugar
		LogLevel("Incapaz de hacer una prueba para autojugar (run=$number, contest=$contest)",1);
		return false;
	}
	$a = DBRow($r,0);
	$b = DBContestClockInfo($contest, $c);
	$t = time();

	if (($oid1 = DB_lo_import($c, $stdout)) === false) {
		DBExec($c, "rollback work", "DBUpdateRunAutojudging(rollback-stdout)");
        //No se puede crear un objeto grande para el archivo
        LOGError("Unable to create a large object for file $stdout.");
		return false;
	}

	if (($oid2 = DB_lo_import($c, $stderr)) === false) {
		DBExec($c, "rollback work", "DBUpdateRunAutojudging(rollback-stderr)");
        //No se puede crear un objeto grande para el archivo
		LOGError("Unable to create a large object for file $stderr.");
		return false;
	}


    if (($oid3 = DB_lo_import($c, $stdoutuser)) === false) {
		DBExec($c, "rollback work", "DBUpdateRunAutojudging(rollback-stdoutuser)");
        //No se puede crear un objeto grande para el archivo
		LOGError("Unable to create a large object for file $stdoutuser.");
		return false;
	}

	if($answer=="") $answer="null";
	else $answer="'$answer'";

	DBExec($c, "update runtable set autoenddate=$t, autoanswer=$answer, autostdout=$oid1, autostderr=$oid2, autostdoutuser=$oid3, " .
		   "updatetime=$t " .
		   "where contestnumber=$contest and runnumber=$number",
		   "DBUpdateRunAutojudging(update run)");

	$b = DBContestClockInfo($contest, $c);

	if($b["contestautojudge"]!="t") {
	  DBExec($c, "commit work", "DBUpdateRunAutojudging(commit)");
      //utojuzgar respondió una carrera
	  LOGLevel("Autojuez respondio a ejecucion (run=$number, contest=$contest, answer='$answer', retval=$retval)", 3);
	  return true;
	}


	if(DBUpdateRunO($contest, $a["usernumber"], $number, $retval, $c)==false) {
		DBExec($c, "rollback work", "DBUpdateRunAutoJudging(rollback)");
        //No se puede actualizar automáticamente una respuesta de ejecución
		LOGError("Unable to automatically update a run answer (run=$number, ".
				 "contest=$contest, answer='$answer', retval=$retval)");
		return false;
	}



	DBExec($c, "commit work", "DBUpdateRunAutojudging(commit)");
    //Autojugar respondió automáticamente una carrera
	LOGLevel("Autojuez respondio automaticamente a ejecucion (run=$number, contest=$contest, retval=$retval, answer='$answer')", 3);
	return true;
}
//(contest,site,number,ip='',ans='',true)
//funcion para actulizar la tabla runtable y verificar el juzgado si tiene globo o no
//actualizado...
function DBGiveUpRunAutojudging($contest, $number, $ip="", $ans="", $fromadmin=false) {
	$c = DBConnect();
	DBExec($c, "begin work", "DBGiveUpRunAutojudging(transaction)");
	$sql = "select * from runtable as r " .
		"where r.contestnumber=$contest and r.runnumber=$number";

    $r = DBExec ($c, $sql . " for update", "DBGiveUpRunAutoJudging(get run for update)");
	$n = DBnlines($r);
	if ($n != 1) {
		DBExec($c, "rollback work", "DBGiveUpRunAutoJudging(rollback)");
        //Incapaz de renunciar a una carrera desde la evaluación automática
		LogLevel("Unable to giveup a run from autojudging (run=$number, contest=$contest)",1);
		return false;
	}
	$a = DBRow($r,0);
	$t = time();

	$b = DBContestClockInfo($contest, $c);
	if(!$fromadmin && $b["contestautojudge"]=="t") {
        //(contest,site,usernumber,site,number,7,connect)
        //(contest,site,usernumber,site,number,7,1,connect)
        ////funcion para actulizar runtable runanswer1= y verifica si tiene globo o no para insertar un nuevo registro en tasktable
        //1 para no añadir mas comparacion a la consulta
        //si todo es bien true caso contrario false

	    if(DBUpdateRunO($contest, $a["usernumber"], $number, 7, $c)==false) {  // 7 significa contactar al personal(staff)
	         DBExec($c, "commit work", "DBGiveUpRunAutojudging(commit prob auto)");
             //Incapaz de renunciar a una carrera desde la evaluación automática
	         LOGError("Unable to automatically update a run answer (run=$number, ".
		           "contest=$contest, answer='$ans', retval=7)");
	         return false;
	    }
	}

	if($ip=="") {
		DBExec($c, "update runtable set autoenddate=null, autoanswer=null, autostdout=null, autostderr=null, autostdoutuser=null, " .
			   "updatetime=$t, autobegindate=null, autoip='' " .
			   "where contestnumber=$contest and runnumber=$number",
			   "DBGiveUpRunAutojudging(update run)");
	} else {
		DBExec($c, "update runtable set autoenddate=$t, autoanswer='$ans', autostdout=null, ".
			   "autostderr=null, autostdoutuser=null, updatetime=$t, autoip='$ip' " .
			   "where contestnumber=$contest and runnumber=$number",
			   "DBGiveUpRunAutojudging(update run-withip)");
	}
	DBExec($c, "commit work", "DBGiveUpRunAutojudging(commit)");
    //Ejecutar abandonado de Autojugar
	LOGLevel("Ejecucion abandonado de autojuez(run=$number, contest=$contest)", 3);

	return true;
}
////(contest,sitejudging(usersiten site es),''0 user(etc))
//capturamos toda la informacion acerca de todos los envios realizados de un contest
//funcion que me devuelve datos de las tablas runtable,problemtable,langtable,answertable,usertable
//y dependiendo del orden -1 es para no añadir mas a la consulta de la base de datos
/*function DBAllRunsInSites($contest,$site,$order='run') {
    //funcion que me devuelve datos de las tablas runtable,problemtable,langtable,answertable,usertable
    //y dependiendo del orden -1 es para no añadir mas a la consulta de la base de datos
	return DBOpenRunsSNS($contest,$site,-1,$order);
}*/
function DBAllRunsInContest($contest,$order='run') {
    //funcion que me devuelve datos de las tablas runtable,problemtable,langtable,answertable,usertable
    //y dependiendo del orden -1 es para no añadir mas a la consulta de la base de datos
	return DBOpenRunsSNS($contest,-1,$order);
}

//funcion que me devuelve datos de las tablas runtable,problemtable,langtable,answertable,usertable
//y dependiendo del orden -1 es para no añadir mas a la consulta de la base de datos
//actualizado
function DBOpenRunsSNS($contest,$st,$order='run') {
	$c = DBConnect();
    //saca todas las columnas de una competencia de las tablas runtable,problemtable, langtable, answertable, usertable
	/*$sql = "select distinct r.runnumber as number, r.rundatediff as timestamp, r.usernumber as user, " .
		"p.problemname as problem, r.runstatus as status, l.langname as language, l.langextension as extension, " .
		"a.yes as yes, p.problemcolor as color, p.problemcolorname as colorname, " .
		"r.runsitenumber as site, r.runjudge as judge, r.runjudgesite as judgesite, " .
		"a.runanswer as answer, r.runfilename as filename, " .
		"r.autobegindate as autobegin, r.autoenddate as autoend, r.autoanswer as autoanswer ".
		"from runtable as r, problemtable as p, langtable as l, answertable as a, usertable as u " .
		"where r.contestnumber=$contest and p.contestnumber=r.contestnumber and u.contestnumber=r.contestnumber and " .
		"r.runproblem=p.problemnumber and l.contestnumber=r.contestnumber and r.usernumber=u.usernumber and r.runsitenumber=u.usersitenumber and " .
		"l.langnumber=r.runlangnumber and a.answernumber=r.runanswer and " .
		"a.contestnumber=r.contestnumber";*/

    $sql = "select distinct r.runnumber as number, r.rundatediff as timestamp, r.usernumber as user, " .
    	"p.problemname as problem, r.runstatus as status, l.langname as language, l.langextension as extension, " .
    	"a.yes as yes, p.problemcolor as color, p.problemcolorname as colorname, " .
    	"r.runjudge as judge, " .
    	"a.runanswer as answer, r.runfilename as filename, " .
    	"r.autobegindate as autobegin, r.autoenddate as autoend, r.autoanswer as autoanswer ".
    	"from runtable as r, problemtable as p, langtable as l, answertable as a, usertable as u " .
    	"where r.contestnumber=$contest and " .
    	"r.runproblem=p.problemnumber and r.usernumber=u.usernumber and " .
    	"l.langnumber=r.runlangnumber and a.answernumber=r.runanswer";



	if ($st == 1) {

		if ($order == 'report')
			$sql .= " and (u.usertype != 'judge')";
		$sql .= " and (not r.runstatus = 'judged') " .
			" and not r.runstatus ~ 'deleted' order by ";
	} else if($st == 2) {
	  $sql .= " and (not r.runstatus = 'judged') order by ";
	} else $sql .= " order by ";


	if ($order == "status")
		$sql .= "r.runstatus,";
  	else if ($order == "judge")
		$sql .= "r.runjudge,";
	else if ($order == "problem")
		$sql .= "p.problemname,";
	else if ($order == "language")
		$sql .= "l.langname,";
	else if ($order == "answer")
		$sql .= "a.runanswer,";
	else if ($order == "user")
		$sql .= "r.usernumber,";

	if ($st == 1 || $order == "report")
		$sql .= "r.runnumber";
	else
		$sql .= "r.rundatediff desc";//rundatediff de forma desendente

	$r = DBExec($c, $sql, "DBOpenRunsSNS(get run/prob/lang/ans)");

	$n = DBnlines($r);
	$a = array();
	for ($i=0;$i<$n;$i++)
		$a[$i] = DBRow($r,$i);
	return $a;
}
//funcion para capturar todo ejecucion
//mejorado
function DBAllRuns($order='run',$contest='') {
	$c = DBConnect();
    //saca todas las columnas de una competencia de las tablas runtable,problemtable, langtable, answertable, usertable
	$sql = "select distinct r.runnumber as number, r.rundatediff as timestamp, r.usernumber as user, " .
		"p.problemname as problem, p.problemnumber, r.runstatus as status, l.langname as language, l.langextension as extension, " .
		"a.yes as yes, p.problemcolor as color, p.problemcolorname as colorname, " .

		"a.runanswer as answer, r.runfilename as filename, " .
		"r.autobegindate as autobegin, r.autoenddate as autoend, r.autoanswer as autoanswer ".
		"from runtable as r, problemtable as p, langtable as l, answertable as a, usertable as u " .
		"where " .
		"r.runproblem=p.problemnumber and r.usernumber=u.usernumber and " .
		"l.langnumber=r.runlangnumber and a.answernumber=r.runanswer";
    if($contest!=''){
        $sql.=" and r.contestnumber=$contest";
    }
	$sql .=" and not r.runstatus ~ 'deleted' order by ";
	if ($order == "problem")
		$sql .= "p.problemname";
	else if ($order == "language")
		$sql .= "l.langname";
	else if ($order == "answer")
		$sql .= "a.runanswer";
	else if ($order == "user")
		$sql .= "r.usernumber";
    else if ($contest=='')
        $sql .="r.runnumber desc";
    else
        $sql .= "r.updatetime desc";//rundatediff de forma desendente



	$r = DBExec($c, $sql, "DBAllRuns(get run/prob/lang/ans)");

	$n = DBnlines($r);
	$a = array();
	for ($i=0;$i<$n;$i++)
		$a[$i] = DBRow($r,$i);
	return $a;
}


//funcion para insertar o actulizar un registro en la tabla runtable con los parametros enviados
function DBNewRun($param,$c=null,$allowinsert=true) {
	if(isset($param['contestnumber']) && !isset($param['contest'])) $param['contest']=$param['contestnumber'];

	if(isset($param['usernumber']) && !isset($param['user'])) $param['user']=$param['usernumber'];
	if(isset($param['number']) && !isset($param['runnumber'])) $param['runnumber']=$param['number'];
	if(isset($param['runlangnumber']) && !isset($param['lang'])) $param['lang']=$param['runlangnumber'];
	if(isset($param['runproblem']) && !isset($param['problem'])) $param['problem']=$param['runproblem'];
	if(isset($param['runfilename']) && !isset($param['filename'])) $param['filename']=$param['runfilename'];
	if(isset($param['rundata']) && !isset($param['filepath'])) $param['filepath']=$param['rundata'];
	$param['filename']=sanitizeFilename($param['filename']);
	$param['filepath']=sanitizeFilename($param['filepath']);

	$ac=array('contest','user','problem','lang','filename','filepath');
	$ac1=array('runnumber','rundate','rundatediff','rundatediffans','runanswer','runstatus','runjudge',
			   'autoip','autobegindate','autoenddate','autoanswer','autostdout','autostderr','autostdoutuser','updatetime');
	$type['contest']=0;
	$type['autobegindate']=1;
	$type['autoenddate']=1;
	$type['problem']=0;
	$type['updatetime']=1;

	$type['user']=1;
	$type['runnumber']=1;
	$type['rundatediffans']=1;
	$type['rundatediff']=1;
	$type['rundate']=1;
	$type['runanswer']=1;
	$type['runjudge']=1;



	foreach($ac as $key) {
		if(!isset($param[$key]) || $param[$key]=="") {
			MSGError("DBNewRun param error: $key not found");
			return false;
		}
		if(isset($type[$key]) && !is_numeric($param[$key])) {
			MSGError("DBNewRun param error: $key is not numeric");
			return false;
		}
		$$key = myhtmlspecialchars($param[$key]);
	}
	$t = time();
	$autoip='';
	$autobegindate='NULL';
	$autoenddate='NULL';
	$autoanswer='';
	$autostdout='';
	$autostderr='';
	$autostdoutuser='';
	$runjudge='NULL';



	$runnumber=-1;
	$updatetime = -1;
	$rundatediff = -1;
	$rundate = $t;
	$runanswer=0;
	$rundatediffans = 999999999;
	$runstatus='openrun';
	foreach($ac1 as $key) {
		if(isset($param[$key])) {
			$$key = myhtmlspecialchars($param[$key]);
			if(isset($type[$key]) && !is_numeric($param[$key])) {
				MSGError("DBNewRun param error: $key is not numeric");
				return false;
			}
		}
	}
	if($updatetime < 0)
		$updatetime=$t;

	$cw = false;
	if($c == null) {
		$cw = true;
		$c = DBConnect();
		DBExec($c, "begin work", "DBNewRun(transaction)");
	}
	$insert=true;
	$oid1 = '';
	$oid2 = '';
	$oid3 = '';
	$oldold1='';
	$oldold2='';
	$oldold3='';

	$sql = "select contestnextrun as nextrun from " .
		"contesttable where contestnumber=$contest for update";
	$r = DBExec($c, $sql, "DBNewRun(get contest for update)");
	if (DBnlines($r) != 1) {
		DBExec($c, "rollback work", "DBNewRun(rollback-contest)");
        //No se pudo encontrar un sitio / concurso único en la base de datos.
		LOGError("Unable to find a unique contest in the database. SQL=(" . $sql . ")");
		MSGError("Unable to find a unique contest in the database.");
		return false;
	}
	$a = DBRow($r,0);
	if($runnumber > 0) {
		$sql = "select * from runtable as t where t.contestnumber=$contest and " .
			"t.runnumber=$runnumber";
		$r = DBExec ($c, $sql . " for update", "DBNewRun(get run for update)");
		$n = DBnlines($r);
		if ($n > 0) {
			$insert=false;
			$lr = DBRow($r,0);
			$t = $lr['updatetime'];
			if(isset($lr['autostdout']))
				$oid1 = $lr['autostdout'];
			if(isset($lr['autostderr']))
				$oid2 = $lr['autostderr'];
            if(isset($lr['autostdoutuser']))
    			$oid3 = $lr['autostdoutuser'];
		} else {
		  if(!$allowinsert) {
		    if($cw)
		      DBExec($c, "rollback work", "DBNewRun(rollback-noinsert)");
		    return -1;
		  }
		}
		$runinc = $runnumber - 1;
	} else {
        //default true
	  if(!$allowinsert) {
	    if($cw)
	      DBExec($c, "rollback work", "DBNewRun(rollback-noinsert)");
	    return -1;
	  }
	  $runnumber = $a["nextrun"] + 1;//0+1
	  DBExec($c, "update contesttable set contestnextrun=$runnumber" .
		 " where contestnumber=$contest and contestnextrun<$runnumber", "DBNewRun(update contest)");
	  $runnumber = myunique($runnumber);////Devuelve la fecha Unix actual con microsegundos con una formula retorna un numero entero
      ////un valor unico en el tiempo que esta ejecutando
      $runinc = $runnumber;
	}

	if($rundatediff < 0) {
		$b = DBContestClockInfo($contest, $c);
		$dif = $b["currenttime"];
		$rundatediff = $dif;
		if ($dif < 0) { if(!isset($param['allowneg'])) {
			DBExec($c, "rollback work", "DBNewRun(rollback-started)");
            //Intenté enviar una carrera, pero el concurso no se inició.
			LOGError("Tried to submit a run but the contest is not started. SQL=(" . $sql . ")");
			MSGError("The contest is not started yet!");
			return 0;//despues retornamos
		} }
		if (!$b["contestrunning"]) {
			DBExec($c, "rollback work", "DBNewRun(rollback-over)");
			LOGError("Tried to submit a run but the contest is over. SQL=(" . $sql . ")");
			MSGError("The contest is over!");//intentó enviar una carrera, pero el concurso ha terminado
			return 0;
		}
	} else {
	  //cassiopc: so we let the run enter the system if it comes with a defined timestamp, but it is to decide later if it will be counted...
      //así que dejamos que la ejecución ingrese al sistema si viene con una marca de tiempo definida, pero es para decidir más adelante si se contará ...
      //$b = DBSiteInfo($contest, $site, $c);
	  $dif = $rundatediff;
	  /*
	    if ($dif >= $b['siteduration']) {
	    DBExec($c, "rollback work", "DBNewRun(rollback-over)");
	    LOGError("Tried to submit a run but the contest is over. SQL=(" . $sql . ")");
	    MSGError("The contest is over!");
	    return 0;
	    }
	  */
	}

	if($updatetime > $t || $insert) {

//	LOGError($autostdout);
		if(substr($autostdout,0,7)=="base64:") {
			$autostdout = base64_decode(substr($autostdout,7));
			$oldoid1 = $oid1;
			if (($oid1 = DB_lo_import_text($c, $autostdout)) == null) {
				DBExec($c, "rollback work", "DBNewRun(rollback-import stdout)");
				LOGError("Unable to create a large object for file stdout (run=$runnumber,contest=$contest).");
				MSGError("problem importing stdout to database. Contact an admin now!");
				return false;
			}
		} else {
			if($autostdout != '') {
				DBExec($c, "rollback work", "DBNewRun(rollback-import stderr)");
				LOGError("Unable to create a large object for file stdout that is not BASE64 (run=$runnumber,contest=$contest).");
				MSGError("problem importing stdout (not BASE64) to database. Contact an admin now!");
				return false;
			}
			$oid1 = 'NULL';
		}
		if(substr($autostderr,0,7)=="base64:") {
//		LOGError($autostderr);
			$autostderr = base64_decode(substr($autostderr,7));
			$oldoid2 = $oid2;
			if (($oid2 = DB_lo_import_text($c, $autostderr)) == null) {
				DBExec($c, "rollback work", "DBNewRun(rollback-import stderr)");
				LOGError("Unable to create a large object for file stderr (run=$runnumber,contest=$contest).");
				MSGError("problem importing stderr to database. Contact an admin now!");
				return false;
			}
		} else {
			if($autostderr != '') {
				DBExec($c, "rollback work", "DBNewRun(rollback-import stderr)");
				LOGError("Unable to create a large object for file stderr that is not BASE64 (run=$runnumber,contest=$contest).");
				MSGError("problem importing stderr (not BASE64) to database. Contact an admin now!");
				return false;
			}
			$oid2 = 'NULL';
		}
        if(substr($autostdoutuser,0,7)=="base64:") {
//		LOGError($autostdoutuser);
            $autostdoutuser = base64_decode(substr($autostdoutuser,7));
            $oldoid3 = $oid3;
            if (($oid3 = DB_lo_import_text($c, $autostdoutuser)) == null) {
                DBExec($c, "rollback work", "DBNewRun(rollback-import stdoutuser)");
                LOGError("Unable to create a large object for file stdoutuser (run=$runnumber,contest=$contest).");
                MSGError("problem importing stderr to database. Contact an admin now!");
                return false;
            }
        } else {
            if($autostdoutuser != '') {
                DBExec($c, "rollback work", "DBNewRun(rollback-import stderr)");
                LOGError("Unable to create a large object for file stdoutuser that is not BASE64 (run=$runnumber,contest=$contest).");
                MSGError("problem importing stderr (not BASE64) to database. Contact an admin now!");
                return false;
            }
            $oid3 = 'NULL';
        }
	}
	$ret=1;
	if($insert) {

		if(substr($filepath,0,7)!="base64:") {
            //importa el archivo a base de datos y devuelve el oid
			if (($oid = DB_lo_import($c, $filepath)) === false) {
				DBExec($c, "rollback work", "DBNewRun(rollback-import)");
                //No se puede crear un objeto grande para el archivo $ filepath.
				LOGError("DBNewRun: Unable to create a large object for file $filepath.");
				MSGError("problem importing file $filepath to database. Contact an admin now!");
				return false;
			}
		} else {
			$filepath = base64_decode(substr($filepath,7));
			if (($oid = DB_lo_import_text($c, $filepath)) == null) {
				DBExec($c, "rollback work", "DBNewRun(rollback-import)");
				LOGError("DBNewRun: Unable to create a large object for file.");
				MSGError("problem importing file to database. Contact an admin now!");
				return false;
			}
		}
        //Devuelve la fecha Unix actual con microsegundos con una formula retorna un numero entero
        //por ahora son iguales
        if($runinc >= $runnumber) {
		  while(true) {
		    DBExec($c,"SAVEPOINT sp" . $runnumber,"DBNewRun(insert run sp)");
		    if(DBExecNonStop($c, "INSERT INTO runtable (contestnumber, runnumber, usernumber, rundate, " .
				     "rundatediff, rundatediffans, runproblem, runfilename, rundata, runanswer, runstatus, runlangnumber, " .
				     "runjudge, ".
				     "autoip, autobegindate, autoenddate, autoanswer, autostdout, autostderr, autostdoutuser, updatetime) " .
				     "VALUES ($contest, $runnumber, $user, $rundate, $rundatediff, $rundatediffans, $problem, '$filename', $oid, $runanswer, " .
				     "'$runstatus', $lang, $runjudge, " .
				     "'$autoip', $autobegindate, $autoenddate, '$autoanswer', $oid1, $oid2, $oid3, $updatetime)",
				     "DBNewRun(insert run)")) break;
		    DBExec($c,"ROLLBACK TO SAVEPOINT sp" . $runnumber,"DBNewRun(insert run sp rollback)");
		    $runnumber++;
		    if($runnumber > $runinc + 3) break;
		  }
		  if($runnumber > $runinc + 3) {
		    DBExec($c, "INSERT INTO runtable (contestnumber, runnumber, usernumber, rundate, " .
			   "rundatediff, rundatediffans, runproblem, runfilename, rundata, runanswer, runstatus, runlangnumber, " .
			   "runjudge, ".
			   "autoip, autobegindate, autoenddate, autoanswer, autostdout, autostderr, autostdoutuser, updatetime) " .
			   "VALUES ($contest, $runnumber, $user, $rundate, $rundatediff, $rundatediffans, $problem, '$filename', $oid, $runanswer, " .
			   "'$runstatus', $lang, $runjudge, " .
			   "'$autoip', $autobegindate, $autoenddate, '$autoanswer', $oid1, $oid2, $oid3, $updatetime)",
			   "DBNewRun(insert run)");
		  }
		} else {
		  if(!DBExecNonStop($c, "INSERT INTO runtable (contestnumber, runnumber, usernumber, rundate, " .
				       "rundatediff, rundatediffans, runproblem, runfilename, rundata, runanswer, runstatus, runlangnumber, " .
				       "runjudge, ".
				       "autoip, autobegindate, autoenddate, autoanswer, autostdout, autostderr, autostdoutuser, updatetime) " .
				       "VALUES ($contest, $runnumber, $user, $rundate, $rundatediff, $rundatediffans, $problem, '$filename', $oid, $runanswer, " .
				       "'$runstatus', $lang, $runjudge, " .
				       "'$autoip', $autobegindate, $autoenddate, '$autoanswer', $oid1, $oid2, $oid3, $updatetime)",
				       "DBNewRun(insert run)")) {
		    if($cw)
		      DBExec($c, "commit work", "DBNewRun(commit-error)");
		    return false;
		  }
		}
		if($cw) {
			DBExec($c, "commit work", "DBNewRun(commit)");
			LOGLevel("Usuario $user envio una ejecucion (#$runnumber) " .
					 "(problem=$problem,filename=$filename,lang=$lang,contest=$contest,date=$t,datedif=$dif,oid=$oid).",2);
		}
		$ret=2;
	} else {
		if($updatetime > $t) {
			$ret=2;
			DBExec($c, "update runtable set rundate=$rundate, rundatediff=$rundatediff, " .
				   "rundatediffans=$rundatediffans, runanswer=$runanswer, runstatus='$runstatus', ".
				   "runjudge=$runjudge, updatetime=$updatetime, ".
				   "autoip='$autoip', autobegindate=$autobegindate, autoenddate=$autoenddate, autoanswer='$autoanswer', " .
				   "autostdout=$oid1, autostderr=$oid2, autostdoutuser=$oid3 " .
				   "where runnumber=$runnumber and contestnumber=$contest ", "DBNewRun(update run)");

			if(isset($oldoid1) && is_numeric($oldoid1)) DB_lo_unlink($c,$oldoid1);
			if(isset($oldoid2) && is_numeric($oldoid2)) DB_lo_unlink($c,$oldoid2);
			if(isset($oldoid3) && is_numeric($oldoid3)) DB_lo_unlink($c,$oldoid3);
		}
		if($cw) DBExec($c, "commit work", "DBNewRun(commit-update)");
	}
	return $ret;
/* // isso gera problemas de portabilidade e de seguranca (quando outros usuarios tambem tem shell
   // no servidor e podem construir paginas web. Eles podem usar essas paginas para acessar esses arquivos,
   // pois os mesmos ficaram com dono apache/www-data/etc)
   // esto crea problemas de portabilidad y seguridad (cuando otros usuarios también tienen un shell
    // en el servidor y puede crear páginas web. Pueden utilizar estas páginas para acceder a estos archivos,
    // porque eran propiedad de apache / www-data / etc)
   umask(0077);
   @mkdir("/tmp/boca");
   if (!move_uploaded_file ($filepath,
   "/tmp/boca/contest${contest}.site${site}.run${n}.user${user}.problem${problem}.time${t}.${filename}"))
   LOGLevel("Run not saved as file (run=$runnumber,site=$site,contest=$contest", 1);
*/
}
//para competencia cero
function DBNewRun0($param,$c=null,$allowinsert=true) {


    if(isset($param['contestnumber']) && !isset($param['contest'])) $param['contest']=$param['contestnumber'];

	if(isset($param['usernumber']) && !isset($param['user'])) $param['user']=$param['usernumber'];
	if(isset($param['number']) && !isset($param['runnumber'])) $param['runnumber']=$param['number'];
	if(isset($param['runlangnumber']) && !isset($param['lang'])) $param['lang']=$param['runlangnumber'];
	if(isset($param['runproblem']) && !isset($param['problem'])) $param['problem']=$param['runproblem'];
	if(isset($param['runfilename']) && !isset($param['filename'])) $param['filename']=$param['runfilename'];
	if(isset($param['rundata']) && !isset($param['filepath'])) $param['filepath']=$param['rundata'];
	$param['filename']=sanitizeFilename($param['filename']);
	$param['filepath']=sanitizeFilename($param['filepath']);

	$ac=array('contest','user','problem','lang','filename','filepath');
	$ac1=array('runnumber','rundate','rundatediff','rundatediffans','runanswer','runstatus','runjudge',
			   'autoip','autobegindate','autoenddate','autoanswer','autostdout','autostderr','autostdoutuser','updatetime');
	$type['contest']=0;
	$type['autobegindate']=1;
	$type['autoenddate']=1;
	$type['problem']=0;
	$type['updatetime']=1;

	$type['user']=1;
	$type['runnumber']=1;
	$type['rundatediffans']=1;
	$type['rundatediff']=1;
	$type['rundate']=1;
	$type['runanswer']=1;
	$type['runjudge']=1;
    $key='contest';


    $str='';
	foreach($ac as $key) {

        if(!isset($param[$key])) {
			MSGError("DBNewRun param error: $key not found");
			return false;
		}
		if(isset($type[$key]) && !is_numeric($param[$key])) {
			MSGError("DBNewRun param error: $key is not numeric");
			return false;
		}
		$$key = myhtmlspecialchars($param[$key]);
	}


	$t = time();
	$autoip='';
	$autobegindate='NULL';
	$autoenddate='NULL';
	$autoanswer='';
	$autostdout='';
	$autostderr='';
	$autostdoutuser='';
	$runjudge='NULL';



	$runnumber=-1;
	$updatetime = -1;
	$rundatediff = time();//-1;
	$rundate = $t;
	$runanswer=0;
	$rundatediffans = 999999999;
	$runstatus='openrun';
	foreach($ac1 as $key) {
		if(isset($param[$key])) {
			$$key = myhtmlspecialchars($param[$key]);
			if(isset($type[$key]) && !is_numeric($param[$key])) {
				MSGError("DBNewRun param error: $key is not numeric");
				return false;
			}
		}
	}
	if($updatetime < 0)
		$updatetime=$t;

	$cw = false;
	if($c == null) {
		$cw = true;
		$c = DBConnect();
		DBExec($c, "begin work", "DBNewRun(transaction)");
	}
	$insert=true;
	$oid1 = '';
	$oid2 = '';
	$oid3 = '';
	$oldold1='';
	$oldold2='';
	$oldold3='';

	$sql = "select contestnextrun as nextrun from " .
		"contesttable where contestnumber=$contest for update";
	$r = DBExec($c, $sql, "DBNewRun(get contest for update)");
	if (DBnlines($r) != 1) {
		DBExec($c, "rollback work", "DBNewRun(rollback-contest)");
        //No se pudo encontrar un sitio / concurso único en la base de datos.
		LOGError("Unable to find a unique contest in the database. SQL=(" . $sql . ")");
		MSGError("Unable to find a unique contest in the database.");
		return false;
	}
	$a = DBRow($r,0);

        //default true
	  if(!$allowinsert) {
	    if($cw)
	      DBExec($c, "rollback work", "DBNewRun(rollback-noinsert)");
	    return -1;
	  }
	  $runnumber = $a["nextrun"] + 1;//0+1
	  DBExec($c, "update contesttable set contestnextrun=$runnumber" .
		 " where contestnumber=$contest and contestnextrun<$runnumber", "DBNewRun(update contest)");
	  $runnumber = myunique($runnumber);////Devuelve la fecha Unix actual con microsegundos con una formula retorna un numero entero
      ////un valor unico en el tiempo que esta ejecutando
      $runinc = $runnumber;



	if($updatetime > $t || $insert) {

        $oid1 = 'NULL';

        $oid2 = 'NULL';

        $oid3 = 'NULL';

	}
	$ret=1;
	if($insert) {

		if(substr($filepath,0,7)!="base64:") {
            //importa el archivo a base de datos y devuelve el oid
			if (($oid = DB_lo_import($c, $filepath)) === false) {
				DBExec($c, "rollback work", "DBNewRun(rollback-import)");
                //No se puede crear un objeto grande para el archivo $ filepath.
				LOGError("DBNewRun: Unable to create a large object for file $filepath.");
				MSGError("problem importing file $filepath to database. Contact an admin now!");
				return false;
			}
		}
        //Devuelve la fecha Unix actual con microsegundos con una formula retorna un numero entero
        //por ahora son iguales
        if($runinc >= $runnumber) {
		  while(true) {
		    DBExec($c,"SAVEPOINT sp" . $runnumber,"DBNewRun(insert run sp)");
		    if(DBExecNonStop($c, "INSERT INTO runtable (contestnumber, runnumber, usernumber, rundate, " .
				     "rundatediff, rundatediffans, runproblem, runfilename, rundata, runanswer, runstatus, runlangnumber, " .
				     "runjudge, ".
				     "autoip, autobegindate, autoenddate, autoanswer, autostdout, autostderr, autostdoutuser, updatetime) " .
				     "VALUES ($contest, $runnumber, $user, $rundate, $rundatediff, $rundatediffans, $problem, '$filename', $oid, $runanswer, " .
				     "'$runstatus', $lang, $runjudge, " .
				     "'$autoip', $autobegindate, $autoenddate, '$autoanswer', $oid1, $oid2, $oid3, $updatetime)",
				     "DBNewRun(insert run)")) break;
		    DBExec($c,"ROLLBACK TO SAVEPOINT sp" . $runnumber,"DBNewRun(insert run sp rollback)");
		    $runnumber++;
		    if($runnumber > $runinc + 3) break;
		  }
		  if($runnumber > $runinc + 3) {
		    DBExec($c, "INSERT INTO runtable (contestnumber, runnumber, usernumber, rundate, " .
			   "rundatediff, rundatediffans, runproblem, runfilename, rundata, runanswer, runstatus, runlangnumber, " .
			   "runjudge, ".
			   "autoip, autobegindate, autoenddate, autoanswer, autostdout, autostderr, autostdoutuser, updatetime) " .
			   "VALUES ($contest, $runnumber, $user, $rundate, $rundatediff, $rundatediffans, $problem, '$filename', $oid, $runanswer, " .
			   "'$runstatus', $lang, $runjudge, " .
			   "'$autoip', $autobegindate, $autoenddate, '$autoanswer', $oid1, $oid2, $oid3, $updatetime)",
			   "DBNewRun(insert run)");
		  }
		} else {

		    if($cw)
		      DBExec($c, "commit work", "DBNewRun(commit-error)");
		    return false;

		}
		if($cw) {
			DBExec($c, "commit work", "DBNewRun(commit)");
			LOGLevel("Usuario $user envio una ejecucion (#$runnumber) " .
					 "(problem=$problem,filename=$filename,lang=$lang,contest=$contest,date=$t,oid=$oid).",2);
		}
		$ret=2;
	} else {

		if($cw) DBExec($c, "commit work", "DBNewRun(commit-update)");
	}


	return $ret;

}




// recibe el número del concurso, el número del sitio y el número del usuario
// devuelve una matriz, donde cada línea tiene los atributos
// número (número de ejecución)
// marca de tiempo (tiempo de creación de ejecución)
// problema (nombre del problema)
// estado (situación de ejecución)
// respuesta (texto con la respuesta)
// lenguaje y extension
function DBUserRuns($contest,$user) {
	$b = DBContestClockInfo($contest);
	if ($b == null) {
		exit;
	}
	$t = $b["currenttime"];

	$c = DBConnect();
	/*$r = DBExec($c, "select distinct r.runnumber as number, r.rundatediff as timestamp, " .
	     		"r.runfilename as filename, r.rundata as oid, " .
				"p.problemcolorname as colorname, p.problemcolor as color, a.yes as yes, " .
				"p.problemname as problem, r.runstatus as status, l.langname as language, l.langextension as extension, " .
				"a.runanswer as answer, a.fake as ansfake, r.rundatediffans as anstime, r.autoanswer as autoanswer, " .
				"r.autostdoutuser as stdoutuser " .
				"from runtable as r, problemtable as p, answertable as a, langtable as l " .
				"where r.contestnumber=$contest and p.contestnumber=r.contestnumber and " .
				"l.contestnumber=r.contestnumber and l.langnumber=r.runlangnumber and " .
				"r.contestnumber=a.contestnumber and r.runproblem=p.problemnumber and " .
				"r.runsitenumber=$site and r.usernumber=$user and not r.runstatus ~ 'deleted' and " .
				"(r.rundatediffans<=$t or (r.runstatus != 'judged' and r.rundatediff<=$t)) and " .
				"a.answernumber=r.runanswer order by r.runnumber",
				"DBUserRuns(get run/prob/ans/lang)");*/
    $r = DBExec($c, "select distinct r.runnumber as number, r.rundatediff as timestamp, " .
            	"r.runfilename as filename, r.rundata as oid, " .
            	"p.problemcolorname as colorname, p.problemcolor as color, a.yes as yes, " .
            	"p.problemname as problem, r.runstatus as status, l.langname as language, l.langextension as extension, " .
            	"a.runanswer as answer, a.fake as ansfake, r.rundatediffans as anstime, r.autoanswer as autoanswer, " .
            	"r.autostdoutuser as stdoutuser " .
            	"from runtable as r, problemtable as p, answertable as a, langtable as l " .
            	"where r.contestnumber=$contest and " .
            	"l.langnumber=r.runlangnumber and " .
            	"r.runproblem=p.problemnumber and " .
            	"r.usernumber=$user and not r.runstatus ~ 'deleted' and " .
            	"(r.rundatediffans<=$t or (r.runstatus != 'judged' and r.rundatediff<=$t)) and " .
            	"a.answernumber=r.runanswer order by r.runnumber",
            	"DBUserRuns(get run/prob/ans/lang)");
    $n = DBnlines($r);

	$a = array();
	for ($i=0;$i<$n;$i++) {
		$a[$i] = DBRow($r,$i);
		if ($a[$i]["timestamp"] >= $b["contestlastmileanswer"])
			$a[$i]["answer"] = "";
	}
	return $a;
}
//(contestnumber,usersite,user)
//funcion para capturar el color y otros de un problema resuelto de n usuario en runtable
function DBUserRunsYES($contest,$user) {

	$b = DBContestClockInfo($contest);
	if ($b == null) {
		exit;
	}
	$t = $b["currenttime"];
	$c = DBConnect();

    //capturamos el color de un problema resulto de un usuario
	/*$r = DBExec($c, "select distinct p.problemcolorname as colorname, p.problemcolor as color, " .
				"r.rundatediff as timestamp, p.problemnumber as number " .
				"from runtable as r, problemtable as p, answertable as a " .
				"where r.contestnumber=$contest and p.contestnumber=r.contestnumber and " .
				"r.contestnumber=a.contestnumber and r.runproblem=p.problemnumber and " .
				"r.runsitenumber=$site and r.usernumber=$user and not r.runstatus ~ 'deleted' and " .
				"(r.rundatediffans<=$t or (r.runstatus != 'judged' and r.rundatediff<=$t)) and " .
				"a.answernumber=r.runanswer and a.yes='t' order by r.rundatediff",
				"DBUserRunsYES(get run/prob/ans/lang)");*/
    //consulta modificado
    $r = DBExec($c, "select distinct p.problemcolorname as colorname, p.problemcolor as color, " .
				"r.rundatediff as timestamp, p.problemnumber as number " .
				"from runtable as r, problemtable as p, answertable as a " .
				"where r.contestnumber=$contest and " .
				"r.runproblem=p.problemnumber and " .
				"r.usernumber=$user and not r.runstatus ~ 'deleted' and " .
				"(r.rundatediffans<=$t or (r.runstatus != 'judged' and r.rundatediff<=$t)) and " .
				"a.answernumber=r.runanswer and a.yes='t' order by r.rundatediff",
				"DBUserRunsYES(get run/prob/ans)");
	$n = DBnlines($r);

	$a = array(); $j=0;
	$p = array();
	for ($i=0;$i<$n;$i++) {
		$aa = DBRow($r,$i);
		if ($aa["timestamp"] < $b["contestlastmileanswer"]) {
			if(!isset($p[$aa["number"]])) {
				$p[$aa["number"]] = 1;
				$a[$j] = $aa;
				$j++;
			}
		}
	}
	return $a;
}
//funcion para devolver el msg de error y retval correspondiente
function exitmsg($retval) {
/* FROM SAFEEXEC
# 0 ok
# 1 compile error
# 2 runtime error
# 3 timelimit exceeded
# 4 internal error
# 5 parameter error
# 6 internal error
# 7 memory limit exceeded
# 8 security threat
# 9 runtime error
*/
/*
	DBExec($c, "insert into answertable (contestnumber, answernumber, runanswer, yes, fake) values ".
			"($n, 0, 'Not answered yet', 'f', 't')", "DBNewContest(insert fake answer)");
	DBExec($c, "insert into answertable (contestnumber, answernumber, runanswer, yes, fake) values ".
			"($n, 1, 'YES', 't', 'f')", "DBNewContest(insert YES answer)");
	DBExec($c, "insert into answertable (contestnumber, answernumber, runanswer, yes, fake) values ".
			"($n, 2, 'NO - Compilation error', 'f', 'f')", "DBNewContest(insert CE answer)");
	DBExec($c, "insert into answertable (contestnumber, answernumber, runanswer, yes, fake) values ".
			"($n, 3, 'NO - Runtime error', 'f', 'f')", "DBNewContest(insert RE answer)");
	DBExec($c, "insert into answertable (contestnumber, answernumber, runanswer, yes, fake) values ".
			"($n, 4, 'NO - Time limit exceeded', 'f', 'f')", "DBNewContest(insert TLE answer)");
	DBExec($c, "insert into answertable (contestnumber, answernumber, runanswer, yes, fake) values ".
			"($n, 5, 'NO - Presentation error', 'f', 'f')", "DBNewContest(insert PE answer)");
	DBExec($c, "insert into answertable (contestnumber, answernumber, runanswer, yes, fake) values ".
			"($n, 6, 'NO - Wrong answer', 'f', 'f')", "DBNewContest(insert WA answer)");
	DBExec($c, "insert into answertable (contestnumber, answernumber, runanswer, yes, fake) values ".
			"($n, 7, 'NO - Contact staff', 'f', 'f')", "DBNewContest(insert CS answer)");
*/
	if($retval==-1) {
        //Error interno al ejecutar el comando de ejecución
		$answer="Internal error while executing run command";
		$retval = 7; // contact staff
	}
	else if($retval==1) {
		$answer="Compilation error";
		$retval = 2; // compilation error
	}
	else if($retval==2) {
		$answer="Runtime error";
		$retval = 3; // runtime error
	}
	else if($retval==3) {
		$answer="Time limit exceeded";
		$retval = 4; // timelimit exceeded
	}
	else if($retval==4) {
        //error interno de safeexec
		$answer="safeexec internal error (4)";
		$retval = 7; // contact staff
	}
	else if($retval==5) {
        //error safeexec: problema de parámetro
		$answer="safeexec error: parameter problem";
		$retval = 7; // contact staff
	}
	else if($retval==6) {
        //error interno de safeexec (6)
		$answer="safeexec internal error (6)";
		$retval = 7; // contact staff
	}
	else if($retval==7) {
		$answer="Runtime error (memory-limit)";
		$retval = 3; // runtime error
	}
	else if($retval==8) {
        //El código genera una amenaza a la seguridad
		$answer="Code generates security threat";
		$retval = 3; // runtime error
	}
	else if($retval==9) {
        //Error de tiempo de ejecución (o posible discrepancia en el nombre de la clase de Java)
		$answer="Runtime error (or possible java class name mismatch)";
	} else {
        //Estado de autoevaluación desconocido
		$answer="Unknown autojudge status";
		$retval = 7;
	}
	return array($retval,$answer);
}
//añadido para obtener problemas resueltos
//y enviados de un problema
function DBRunProblem($problem) {
	$c = DBConnect();
	DBExec($c, "begin work", "DBRunProblem(begin)");
	$sql = "select * from runtable as r " .
		"where r.runproblem=$problem";
    $rall = DBExec ($c, $sql, "DBRunProblem(get run problem all)");
    $sql.=" and r.runanswer=1";
    $rac = DBExec ($c, $sql, "DBRunProblem(get run problem ac)");
	$nall = DBnlines($rall);
	$nac = DBnlines($rac);
    $param["all"]=$nall;
    $param["ac"]=$nac;

	DBExec($c, "commit work", "DBGiveUpRunAutojudging(commit)");


	return $param;
}
// eof
?>
