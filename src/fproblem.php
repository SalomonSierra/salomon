<?php
//funcion para eliminar la tabla problemtable de database
function DBDropProblemTable(){
    $c=DBConnect();
    $r=DBExec($c,"drop table if exists \"problemtable\"","DBDropProblemTable(drop table)");
}
//funcion para crear la tabla problemtable
function DBCreateProblemTable(){
    $c=DBConnect();
    $conf=globalconf();
    if($conf["dbuser"]=="") $conf["dbuser"]="salomonuser";
    $r=DBExec($c,"
    CREATE TABLE \"problemtable\" (
        \"problemnumber\" int4 NOT NULL,            --(id de problema)
        \"usernumber\" int4 NOT NULL,            --(id de problem setter)
        \"problemname\" varchar(20) NOT NULL,       --(nombre del problema)
        \"problemfullname\" varchar(100) DEFAULT '',    --(nombre completo del problema)
        \"problembasefilename\" varchar(100),           --(nombre base de archvos del problema)
        \"probleminputfilename\" varchar(100) DEFAULT '',   --(nombre de archivos de entrada)
        \"probleminputfile\" oid,                           --(apuntador para archivos de entrada)
        \"probleminputfilehash\" varchar(50),               --(apuntador para archivo de entrada)
        \"fake\" bool DEFAULT 'f' NOT NULL,                 --(indica si el problema es valido clasificacion en general)

        \"problemcolorname\" varchar(100) DEFAULT '',       -- nombre de color del problemname
        \"problemcolor\" varchar(6) DEFAULT '',             --color del problema en formato html (RGB hexadecimal)
        \"updatetime\" int4 DEFAULT EXTRACT(EPOCH FROM now()) NOT NULL,     --(indica la ultima actualizacion del registro)
        CONSTRAINT \"problem_pkey\" PRIMARY KEY (\"problemnumber\"),
        CONSTRAINT \"user_fk\" FOREIGN KEY (\"usernumber\") REFERENCES \"usertable\" (\"usernumber\")
        ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
    )","DBCreateProblemTable(create table)");
    $r=DBExec($c,"REVOKE ALL ON \"problemtable\" FROM PUBLIC","DBCreateProblemTable(revoke public)");
    $r=DBExec($c,"GRANT ALL ON \"problemtable\" TO \"".$conf["dbuser"]."\"","DBCreateProblemTable(grant salomonuser)");
    $r=DBExec($c,"CREATE INDEX \"problem_index\" ON \"problemtable\" USING btree ".
        "(\"problemnumber\" int4_ops,\"usernumber\" int4_ops)","DBCreateProblemTable(create problem_index)");
    $r=DBExec($c,"CREATE INDEX \"problem_index2\" ON \"problemtable\" USING btree ".
        "(\"usernumber\" int4_ops, \"problemname\" varchar_ops)","DBCreateProblemTable(create problem_index2)");

}
//funcion para la logtable en la base de datos
function DBDropProblemContestTable(){
    $c=DBConnect();
    $r=DBExec($c,"drop table if exists \"problemcontesttable\"","DBDropProblemContestTable(drop table)");
}
//funcion para crear la tabla logtable
function DBCreateProblemContestTable(){
    $c=DBConnect();
    $conf=globalconf();
    if($conf["dbuser"]=="") $conf["dbuser"]="salomonuser";
    $r=DBExec($c,"
    CREATE TABLE \"problemcontesttable\"(
        \"contestnumber\" int4 NOT NULL,                  --()
        \"problemnumber\" int4 NOT NULL,                  --()
        \"data\" varchar(20) DEFAULT '',                  --(descripcion)
        \"updatetime\" int4 DEFAULT EXTRACT(EPOCH FROM now()) NOT NULL, --(indica la ultima actualizacion)
        CONSTRAINT \"problemcontest_pkey\" PRIMARY KEY(\"contestnumber\",\"problemnumber\"),
        CONSTRAINT \"contest_fk\" FOREIGN KEY (\"contestnumber\")
            REFERENCES \"contesttable\" (\"contestnumber\")
            ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
        CONSTRAINT \"problem_fk\" FOREIGN KEY (\"problemnumber\")
            REFERENCES \"problemtable\" (\"problemnumber\")
            ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )","DBProblemContestTable(create table)");
        //\"contestnumber\" int4_ops,
    $r=DBExec($c,"REVOKE ALL ON \"problemcontesttable\" FROM PUBLIC","DBCreateProblemContestTable(revoke public)");
    $r=DBExec($c,"GRANT INSERT, SELECT ON \"problemcontesttable\" TO \"".$conf["dbuser"]."\"","DBCreateProblemContestTable(grant salomonuser)");//asignando privilegio a dbuser
    $r=DBExec($c,"CREATE UNIQUE INDEX \"problemcontest_index\" ON \"problemcontesttable\" USING btree ".
    "(\"contestnumber\" int4_ops, \"problemnumber\" int4_ops)",
    "DBCreateProblemContestTable(create log_index)");
}
//funcion para insertar un problemcontest
function DBNewProblemContest($param, $c=null){
    //if(isset($param['usernumber']) && !isset($param['user'])) $param['user']=$param['usernumber'];
    if(isset($param['contestnumber']) && !isset($param['contest'])) $param['contest']=$param['contestnumber'];
    if(isset($param['problemnumber']) && !isset($param['problem'])) $param['problem']=$param['problemnumber'];

    $ac=array('contest','problem');
    $typei['contest']=0;
	$typei['updatetime']=-1;
	$typei['problem']=0;

    $data=$param["data"];
    $updatetime=-1;
    foreach($ac as $key) {
        if(!isset($param[$key]) || $param[$key]=="") {
            MSGError("DBNewProblemContest param error: $key not found");
            return false;
        }
        if(isset($typei[$key]) && !is_numeric($param[$key])) {
            MSGError("DBNewProblemContest param error: $key is not numeric");
            return false;
        }
        $$key = myhtmlspecialchars($param[$key]);
    }
    //if($data=='') $data=$data;
    //else $data='';
    $t = time();
    if($updatetime <= 0)
        $updatetime=$t;

    $cw = false;
    if($c == null) {
        $cw = true;
        $c = DBConnect();
        DBExec($c, "begin work", "DBNewProblemContest(begin)");
    }
    DBExec($c, "lock table problemcontesttable", "DBNewProblemContest(lock)");
    $r = DBExec($c, "select * from problemcontesttable where contestnumber=$contest and problemnumber=$problem", "DBNewProblemContest(get problemcontest)");
    $n = DBnlines ($r);
    $ret=1;
    if($n==0){
        $ret=2;
        $sql = "insert into problemcontesttable (contestnumber, problemnumber, data, updatetime)" .
             " values($contest, $problem, '$data', $t)";
         DBExec ($c, $sql, "DBNewProblemContest(insert)");
         if($cw) {
             DBExec ($c, "commit work");
         }
         LOGLevel ("problema añadido a la competencia $contest",2);
    }else{
        if($n==1){
            $ret=2;
            if($data=='deleted'){
                DBRunDelete($problem,$contest,$_SESSION["usertable"]["usernumber"],$c);
            }
            $sql="update problemcontesttable set data='$data' where contestnumber=$contest and problemnumber=$problem";
            DBExec ($c, $sql, "DBNewProblemContest(update data)");

        }else{
            if($cw)
      	         DBExec ($c, "rollback work");
      	    LOGLevel ("Problema al añadir problem $problem a la competencia $contest",1);
      //Problema de actualización para el usuario $ usuario, sitio $ sitio (tal vez el nombre de usuario ya esté en uso).
            MSGError ("Problema al añadir problem $problem a la competencia $contest");
      	    return false;
        }
    }
    if($cw) DBExec($c, "commit work");
	return $ret;
}

/////////////////////////////////ejecuta funciones de respuesta///////////////////////////////////////////
// recibe el número del concurso
// devuelve una matriz, donde cada línea tiene los atributos number
//(número de respuesta) y desc (descripción de respuesta o answer)
//mejorado
function DBGetAnswers() {
        $c = DBConnect();
        $r = DBExec($c, "select a.answernumber as number, a.runanswer as desc, a.yes as yes, a.fake as fake ".
	     "from answertable as a where a.runanswer !~ '(DEL)' order by a.answernumber", "DBGetAnswers(get answers)");
        $n = DBnlines($r);
        $a = array();
        for ($i=0;$i<$n;$i++)
                $a[$i] = DBRow($r,$i);
        return $a;
}
//insertamos un registro falso a problemtable
function DBinsertfakeproblem($n,$c){
    DBExec($c,"insert into problemtable (contestnumber, problemnumber, problemname, problemfullname, ".
        "problembasefilename, probleminputfilename, probleminputfile, fake) values ($n, 0, 'General' , 'General', NULL, NULL, ".
        "NULL, 't')","DBNewContest(insert problem)");
}
/////////////////////////funciones de los problemas////////////////////////////////
//recibe un numero de contest y problema
//devuelve todos los datos relacionados con el problema en cada liena de la matriz y cada linea representa el hecho
//que hay mas de un archivo de entrada/salida
//No devuelve datos sobre problemas falsos, como no deberian haberlo hecho.
function DBGetProblemData($problemnumber, $c=null){
    if($c==null)
        $c=DBConnect();
    $r=DBExec($c,"select p.problemname as problemname, p.problemfullname as fullname, p.problembasefilename ".
    "as basefilename, p.problemnumber as number, ".
    "p.problemcolor as color, p.problemcolorname as colorname, ".
    "p.probleminputfilename as inputfilename, p.probleminputfile as inputoid, p.probleminputfilehash as inputhash ".
    " from problemtable as p where p.problemnumber=$problemnumber and p.fake!='t'",
    "DBGetProblemData(get problem)");
    $n=DBnlines($r);
    if ($n == 0){
        LOGError("No se pueden encontrar los datos del problema en la base de datos. ($problemnumber)");
        MSGError("No se pueden encontrar los datos del problema en la base de datos. contactate con el admin ahora!");
        exit;
    }
    $a=array();
    for($i=0;$i<$n;$i++){
        $a[$i]=DBRow($r,$i);
        if($a[$i]['basefilename']=='') continue;
        if(isset($_SESSION['locr'])){
            $ds=DIRECTORY_SEPARATOR;
            if($ds=="") $ds="/";
            $nn=$a[$i]['number'];
            $ptmp=$_SESSION["locr"].$ds."private".$ds."problemtmp".$ds."problem".$nn;
            if(is_readable($ptmp.".name")){
                $a[$i]['descfilename']=trim(file_get_contents($ptmp.".name"));
                if($a[$i]['descfilename']!='')
                    $a[$i]['descoid']=-1;
            }

        }
    }
    return $a;
}
/*function DBGetProblemData($contestnumber, $problemnumber, $c=null){
    if($c==null)
        $c=DBConnect();
    $r=DBExec($c,"select p.problemname as problemname, p.problemfullname as fullname, p.problembasefilename ".
    "as basefilename, p.problemnumber as number, ".
    "p.problemcolor as color, p.problemcolorname as colorname, ".
    "p.probleminputfilename as inputfilename, p.probleminputfile as inputoid, p.probleminputfilehash as inputhash ".
    " from problemtable as p where p.contestnumber=$contestnumber and p.problemnumber=$problemnumber and p.fake!='t'",
    "DBGetProblemData(get problem)");
    $n=DBnlines($r);
    if ($n == 0){
        LOGError("No se pueden encontrar los datos del problema en la base de datos. ($contestnumber, $problemnumber)");
        MSGError("No se pueden encontrar los datos del problema en la base de datos. contactate con el admin ahora!");
        exit;
    }
    $a=array();
    for($i=0;$i<$n;$i++){
        $a[$i]=DBRow($r,$i);
        if($a[$i]['basefilename']=='') continue;
        if(isset($_SESSION['locr'])){
            $ds=DIRECTORY_SEPARATOR;
            if($ds=="") $ds="/";
            $nn=$a[$i]['number'];
            $ptmp=$_SESSION["locr"].$ds."private".$ds."problemtmp".$ds."contest".$contestnumber."-problem".$nn;
            if(is_readable($ptmp.".name")){
                $a[$i]['descfilename']=trim(file_get_contents($ptmp.".name"));
                if($a[$i]['descfilename']!='')
                    $a[$i]['descoid']=-1;
            }

        }
    }
    return $a;
}*/
//para sacar la informacion del problema
function DBProblemContestInfo($problemnumber, $contestnumber, $c=null){
    $sw=false;
    if($c==null){
        $sw=true;
        $c=DBConnect();
        DBExec($c, "begin work", "DBProblemContestInfo");
    }

    $r=DBExec($c,"select * from problemcontesttable where problemnumber=$problemnumber and data='' and contestnumber=$contestnumber");
    $n = DBnlines($r);//puede ver solamente uno
    if($sw)
        DBExec($c, "commit", "DBProblemContestInfo(commit)");
    if ($n == 0) {
        return false;
    }else{
        return true;
    }

}
//elimina los archivos creados del problema de una contest data .name .hash
function DBClearProblemTmp($contestnumber) {

    $ds = DIRECTORY_SEPARATOR;
    if($ds=="") $ds = "/";
    $ptmp = $_SESSION["locr"] . $ds . "private" . $ds . "problemtmp" . $ds . "contest" . $contestnumber . "-*.name";
    foreach(glob($ptmp) as $file) @unlink($file);
    $ptmp = $_SESSION["locr"] . $ds . "private" . $ds . "problemtmp" . $ds . "contest" . $contestnumber . "-*.hash";
    foreach(glob($ptmp) as $file) @unlink($file);
}
// el numero de la competencia y true
//funcion para obtener todos los problemas con sus datos y creando la archivos si no existe con los datos
function DBGetFullProblemData($freeproblems=false) {

      $c = DBConnect();//una conexcion
      DBExec($c, "begin work", "GetFullProblemData");

      $r = DBExec($c, "select p.usernumber as user, p.problemnumber as number, p.problemname as name, p.problemfullname as fullname, " .
    	      "p.problembasefilename as basefilename, p.fake as fake, " .
    	      "p.problemcolor as color, p.problemcolorname as colorname, " .
    	      "p.probleminputfilename as inputfilename, p.probleminputfile as inputoid, p.probleminputfilehash as inputhash " .
    	      " from problemtable as p " .
    	      "order by p.problemnumber", "DBGetFullProblemData(get problem)");
      // and p.problemfullname !~ '(DEL)'

      $n = DBnlines($r);//puede ver solamente uno
      if ($n == 0) {
          LOGLevel("No hay problemas definidos en la base de datos. ",1);
      }

      $cf = globalconf();
      $a = array();
      $ds = DIRECTORY_SEPARATOR;
      if($ds=="") $ds = "/";
      for ($i=0;$i<$n;$i++) {
          //array_merge es para mesclar un array
          $a[$i] = array_merge(array(),DBRow($r,$i));//DBRow me da un array asociativa

          if(strpos($a[$i]['fullname'],'(DEL)') !== false) continue;//para problemas elinados se continua

          $nn=$a[$i]['number'];//el numero del problema
          ////private/problemtmp/contest4-problem2
          $ptmp = $_SESSION["locr"] . $ds . "private" . $ds . "problemtmp" . $ds . "problem" . $nn;
          //$ptmp = $_SESSION["locr"] . $ds . "private" . $ds . "problemtmp" . $ds . "contest" . $contestnumber ."-problem" . $nn;

          $ck = myshorthash('');
          //Indica si un fichero existe y es legible.
          if(is_readable($ptmp . ".hash")) {
              $ck = trim(file_get_contents($ptmp . ".hash"));//el contenido que hay en este archivo.hash
          }
          if($ck != $a[$i]['inputhash']) {
              //Borra un fichero
              @unlink($ptmp . ".name");
              @unlink($ptmp . ".hash");
              $a[$i]['basefilename']='';
              $a[$i]['descfilename']='';
              $a[$i]['fullname']='';
          }

          if($freeproblems && $a[$i]['fake'] != 't') {
              if(is_readable($ptmp . ".name")) {
    	          $a[$i]['descfilename']=trim(file_get_contents($ptmp . ".name"));
    	          if($a[$i]['descfilename'] != '')
    	             $a[$i]['descoid']=-1;
              } else {
    	          @unlink($ptmp . ".name");
    	          @unlink($ptmp . ".hash");
    	          $randnum = session_id() . "_" . rand();
    	          $dir = $ptmp . '-' . $randnum;

    	          @mkdir($dir,0770,true);// /var/www/salomon/src/private/problemtmp/contest1-problem1-329942_239732 en directorio
    	          $failed=0;
                  //pg_lo_export () toma un objeto grande en una base de datos PostgreSQL y guarda
                  //su contenido en un archivo en el sistema de archivos local.
                  //para exportar
    	          if(($ret=DB_lo_export($c, $a[$i]["inputoid"], $dir . $ds . "tmp.zip")) === false) {
                      //FreeProblems: No se puede leer el paquete problemático de la base de datos
    	               LogError("FreeProblems: No se puede leer el paquete de problemas de base de datos (problem=$nn, )");
    	               $failed=1;
    	          }

    	          if(!$failed) {

    	              $zip = new ZipArchive;
    	              if ($zip->open($dir . $ds . "tmp.zip") === true) {
                          //
    	                  $zip->extractTo($dir);
    	                  $zip->close();
                          //parse_ini_file() carga el fichero ini especificado en filename,
                          //y devuelve las configuraciones que hay en él a un array asociativa.
                    	  if(($info=@parse_ini_file($dir . $ds . "description" . $ds . 'problem.info'))===false) {
                    	      $failed=2;//aqui es el el invalido el problema
                    	  }
    	                  if(!$failed) {
    	                       $descfile='';
    	                       if(isset($info['descfile']))
    		                        $descfile=trim(sanitizeFilename($info['descfile']));
    	                       $basename=trim(sanitizeFilename($info['basename']));
    	                       $fullname=trim(sanitizeText($info['fullname']));
    	                       if($basename=='' || $fullname=='')
    		                       $failed=3;//aqui es el el invalido el problema
    	                  }
    	             } else $failed=4;

                     if(!$failed) {
                         //private/problemtmp/contest1-problem1
    	                 @mkdir($ptmp,0770,true);
                	     if($descfile != '') {
                             //Si filename no existe, se crea el fichero. De otro
                             //modo, el fichero existente se sobrescribe, a menos que la bandera FILE_APPEND esté establecida.
                             //CONTENIDO , FfHf7nMddw33E9fEzcWw, 2
                             //encripta el dato con key
                             if(file_put_contents($ptmp . $ds . $descfile, encryptData(file_get_contents($dir . $ds . "description" . $ds . $descfile),$cf['key']),LOCK_EX)===FALSE)
                		          $failed=5;//aqui es el el invalido el problema
                	         if(!$failed) {

                        		 file_put_contents($ptmp . ".name",$ptmp . $ds . $descfile);
                        		 file_put_contents($ptmp . ".hash",$a[$i]['inputhash']);
                        		 if(is_readable($ptmp . ".name")) {

                        		     $a[$i]['descfilename']=trim(file_get_contents($ptmp . ".name"));
                        		     if($a[$i]['descfilename'] != '')
                        		         $a[$i]['descoid']=-1;
                        		  }
                	          }
                	      } else {
                	          @unlink($ptmp . ".name");
                	          @unlink($ptmp . ".hash");
                	      }
                	      if(!$failed) {

                	          DBExec($c,"update problemtable set problemfullname='$fullname', problembasefilename='$basename' where problemnumber=$nn",
                		            "DBGetFullProblemData(free problem)");
                	          $a[$i]['basefilename']=$basename;
                	          $a[$i]['fullname']=$fullname;
                	       }
    	              }
    	         }
                 //si es mayor a 1
            	 if($failed) {

            	     $a[$i]['basefilename']='';
            	     $a[$i]['descfilename']='';
            	     @unlink($ptmp . ".name");
            	     @unlink($ptmp . ".hash");
            	     DBExec($c,"update problemtable set problemfullname='', problembasefilename='' where problemnumber=$nn",
            		       "DBGetFullProblemData(unfree problem)");

            	     if($failed!=4) {
                         //Error al descomprimir el paquete problema
            	         LogError("Error al descomprimir el paquete problema (failcode=$failed, problem=$nn)");
            	         if($failed==1) $a[$i]['fullname']='(ERROR READING FROM DATABASE, OR DIRECTORY PERMISSION PROBLEM)';
            	         else $a[$i]['fullname']='(PROBLEM PACKAGE SEEMS INVALID)'.$failed;//EL PAQUETE DEL PROBLEMA PARECE NO VÁLIDO
            	      } else {
            	           if($ret==1) $a[$i]['fullname']='(PROBABLY ENCRYPTED FILE)';
            	           if($ret==2) $a[$i]['fullname']='(FILE IS NOT A ZIP)';
            	      }
            	 }
    	         cleardir($dir,false,true);
             }
         }
      }

      DBExec($c, "commit", "GetFullProblemData(commit)");
      return $a;
}
//para sacar el maximo valor de la competencia
function DBProblemMax(){
    $c = DBConnect();//una conexcion
    DBExec($c, "begin work", "DBProblemMax");
    $a=DBGetRow("select max(problemnumber) as problem from problemtable",0,$c,
        "DBNewProblem(max(problem))");
    if($a==null) $n=1;
    else $n=$a["problem"]+1;
    DBExec($c, "commit", "DBProblemMax(commit)");
    return $n;
}
//retorna true si todo esta bien actulizar la tabla problemtable problemfullname=..(DEL)
//actualizar la tabla runtable a status deleted y guarda una nueva tarea old en tasktable
//obtenido datos de tabla problemtable y answertable del mismo usuario
function DBDeleteProblem($param, $c=null) {

      $ac=array('number','inputfilename');
      foreach($ac as $key) {
          if(!isset($param[$key])) return false;
          $$key = myhtmlspecialchars($param[$key]);
      }

      $sql = "select * from problemtable where problemnumber=$number and fake='f'";
      if ($inputfilename != "")
          $sql .= " and probleminputfilename='$inputfilename'";

      $cw = false;
      if($c == null) {
          $cw = true;
          $c = DBConnect();
          DBExec($c, "begin work", "DBDeleteProblem(transaction)");
      }

      $r = DBExec($c, $sql . " for update", "DBDeleteProblem(get for update)");
      if(DBnlines($r)>0) {
            $a = DBRow($r,0);

            if(($pos=strpos($a["problemfullname"],"(DEL)")) !== false) {//si ya es del
                $sql="update problemtable set problemfullname='".substr($a["problemfullname"],0,$pos) ."', updatetime=".time().
        	       " where problemnumber=$number ";
            } else {
                $sql="update problemtable set problemfullname='".$a["problemfullname"] ."(DEL)', updatetime=".time().
        	       " where problemnumber=$number ";
                $sql2="update problemcontesttable set data='deleted' where problemnumber=$number";
                $r2 = DBExec($c, $sql2, "DBDeleteProblem(update problemcontesttable)");//DBDeleteLanguage
            }
            if ($inputfilename != "")
                $sql .= " and probleminputfilename='$inputfilename'";
            $r = DBExec($c, $sql, "DBDeleteProblem(update problemtable)");//DBDeleteLanguage
            //$r = DBExec($c,"select runnumber as number from runtable where contestnumber=$contestnumber and runproblem=$number for update");
            $r = DBExec($c,"select runnumber as number, contestnumber as contest from runtable where runproblem=$number for update");
            $n = DBnlines($r);
            for ($i=0;$i<$n;$i++) {
                $a = DBRow($r,$i);
                //actualizar la tabla runtable a status deleted y guarda una nueva tarea old en tasktable
                //obtenido datos de tabla problemtable y answertable del mismo usuario
                DBRunDelete($a["number"],$a["contest"],$_SESSION["usertable"]["usernumber"]);
            }

      }
      if($cw)
        DBExec($c, "commit", "DBDeleteProblem(commit)");
      $ds = DIRECTORY_SEPARATOR;
      if($ds=="") $ds = "/";
      //añalizar
      $ptmp = $_SESSION["locr"] . $ds . "private" . $ds . "problemtmp" . $ds . "problem" . $number;
      @unlink($ptmp . ".name");//para eliminar los archivos
      @unlink($ptmp . ".hash");

      LOGLevel("Problema $number (inputfile=$inputfilename) eliminado (user=".
    	   $_SESSION["usertable"]["username"].")",2);
      return true;
}
//crea un nuevo problema o actuliza un problema el importa un archivo a base de datos y devuelve oid
function DBNewProblem($usernumber, $param, $c=null) {

      if(isset($param["action"]) && $param["action"]=="delete") {
          //retorna true si todo esta bien actulizar la tabla problemtable problemfullname=..(DEL)
          //actualizar la tabla runtable a status deleted y guarda una nueva tarea old en tasktable
          //obtenido datos de tabla problemtable y answertable del mismo usuario
          return DBDeleteProblem($contestnumber, $param);
      }

      if(isset($param['problemnumber']) && !isset($param['number'])) $param['number']=$param['problemnumber'];
      if(isset($param['problemname']) && !isset($param['name'])) $param['name']=$param['problemname'];
      if(isset($param['problemfullname']) && !isset($param['fullname'])) $param['fullname']=$param['problemfullname'];
      if(isset($param['problembasefilename']) && !isset($param['basename'])) $param['basename']=$param['problembasefilename'];
      if(isset($param['problemcolorname']) && !isset($param['colorname'])) $param['colorname']=$param['problemcolorname'];
      if(isset($param['problemcolor']) && !isset($param['color'])) $param['color']=$param['problemcolor'];
      if(isset($param['probleminputfile']) && !isset($param['inputfilepath'])) $param['inputfilepath']=$param['probleminputfile'];
      if(isset($param['probleminputfilename']) && !isset($param['inputfilename'])) $param['inputfilename']=$param['probleminputfilename'];

      if(isset($param['basename'])) $param['basename'] = sanitizeFilename($param['basename']);

      $ac=array('number','name');
      $type['number']=1;
      $type['updatetime']=1;
      $ac1=array('colorname','fake','color','updatetime','fullname',
    	     'basename','inputfilename','inputfilepath');
      $colorname='';
      $color='';
      $fake='f';
      foreach($ac as $key) {

          if(!isset($param[$key])) {
              MSGError("DBNewProblem param error: $key is not set");
              return false;
          }
          if(isset($type[$key]) && !is_numeric($param[$key])) {
              MSGError("DBNewProblem param error: $key is not numeric");
              return false;
          }
          $$key = myhtmlspecialchars($param[$key]);
      }
      $basename='';
      $inputfilename='';
      $inputfilepath='';
      $fullname='';
      $updatetime=-1;
      foreach($ac1 as $key) {

          if(isset($param[$key])) {
              if(isset($type[$key]) && !is_numeric($param[$key])) {
    	           MSGError("DBNewProblem param error: $key is not numeric");
    	           return false;
              }
              $$key = myhtmlspecialchars($param[$key]);
          }
      }

      $t = time();
      if($updatetime <= 0)
        $updatetime=$t;
      $inputhash = '';

      $sql2 = "select * from problemtable where problemnumber=$number for update";
      // "select * from problemtable where contestnumber=$contestnumber and problemnumber=$number " .
      // "and probleminputfilename='$inputfilename'";

      $cw = false;
      if($c == null) {
          $cw = true;
          $c = DBConnect();
          DBExec($c, "begin work", "DBNewProblem(transaction)");
      }
      $r = DBExec ($c, $sql2, "DBNewProblem(get problem for update)");
      $n = DBnlines($r);//0
      $ret=1;//enpieza en 1
      $oldfullname='';
      $deservesupdatetime=false;
      if ($n == 0) {

          DBExec ($c, "insert into problemtable (problemnumber, usernumber, problemname, problemcolor) values " .
    	     "($number, $usernumber, '$name','-1')", "DBNewProblem(insert problem)");
          $deservesupdatetime=true;
          $s = "created";
      }else {

          $lr = DBRow($r,0);
          $t = $lr['updatetime'];
          $oldfullname=$lr['problemfullname'];
          $s = "updated";
          $inputhash = $lr['probleminputfilehash'];
      }

      if($s=="created" || $updatetime > $t) {

          if(substr($inputfilepath,0,7)!="base64:") {
              if ($inputfilepath != "") {
                  ////funcion que retorna el pequeño sha1
    	           $hash = myshorthash(file_get_contents($inputfilepath));
    	           if($hash != $inputhash) {

    	                $oldoid='';
    	                if(isset($lr))
    	                     $oldoid = $lr['probleminputfile'];
                        //pg_lo_import () crea un nuevo objeto grande en la base de datos usando un
                        //archivo en el sistema de archivos como fuente de datos. devuelve el oid. dependiendo de la version 7.2 o < 4.2.0
    	                if (($oid1 = DB_lo_import($c, $inputfilepath)) === false) {

    	                      DBExec($c, "rollback work", "DBNewProblem(rollback-input)");
                              //No se puede crear un objeto grande para el archivo $ inputfilename.
    	                      LOGError("Unable to create a large object for file $inputfilename.");
                              //problema al importar el archivo a la base de datos. ¡Consulte el registro para obtener más detalles!
    	                      MSGError("problem importing file to database. See log for details!");
    	                      exit;
    	                }
                        //pg_lo_unlink () elimina un objeto grande con la oid . Rendimientos true en caso de éxito o false fracaso.
                        //Para utilizar la interfaz de objetos grandes, es necesario encerrarla dentro de un bloque de transacciones.
    	                if($oldoid != '') DB_lo_unlink($c,$oldoid);
                        //funcion devuelve la informacion de oid enviado pg_lo_read en hash1
    	                $inputhash = DBcrc($oid1, $c);
    	           } else
    	              $oid1 = $lr['probleminputfile'];
             }

          } else {

              $inputfilepath = base64_decode(substr($inputfilepath,7));
              $hash = myshorthash($inputfilepath);

              if($hash != $inputhash) {

                  $oldoid='';
                  if(isset($lr))
                	  $oldoid = $lr['probleminputfile'];
                  //importa un texto a un archivo creado en la base de datos y devuelve el oid del archivo
                  if (($oid1 = DB_lo_import_text($c, $inputfilepath)) == null) {
                	  DBExec($c, "rollback work", "DBNewProblem(rollback-i-import)");
                	  LOGError("Unable to import the large object for file $inputfilename.");
                	  MSGError("problem importing file to database. See log for details!");
                	  exit;
                  }
                  //pg_lo_unlink () elimina un objeto grande con la oid . Rendimientos true en caso de éxito o false fracaso.
                  //Para utilizar la interfaz de objetos grandes, es necesario encerrarla dentro de un bloque de transacciones.
                  if($oldoid != '') DB_lo_unlink($c,$oldoid);
                      $inputhash = DBcrc($oid1, $c);//funcion devuelve la informacion de oid enviado pg_lo_read en hash1
               } else
        	         $oid1 = $lr['probleminputfile'];
          }

          if ($name != "")
            DBExec ($c, "update problemtable set problemname='$name' where problemnumber=$number", "DBNewProblem(update name)");
          if ($fullname != "" || strpos($oldfullname,'(DEL)')!==false) {
              $deservesupdatetime=true;
              DBExec ($c, "update problemtable set problemfullname='$fullname' where problemnumber=$number", "DBNewProblem(update fullname)");
          }

          if ($basename != "") {
              $deservesupdatetime=true;
              DBExec ($c, "update problemtable set problembasefilename='$basename' where problemnumber=$number", "DBNewProblem(update basename)");
          }
          if ($colorname != "")
            DBExec ($c, "update problemtable set problemcolorname='$colorname' where problemnumber=$number", "DBNewProblem(update colorname)");
          if ($color != "")
            DBExec ($c, "update problemtable set problemcolor='$color' where problemnumber=$number", "DBNewProblem(update color)");
          if ($inputfilename != "") {
              $deservesupdatetime=true;
              DBExec ($c, "update problemtable set probleminputfilename='$inputfilename' where ".
    	         "problemnumber=$number ", "DBNewProblem(update inputfilename)");
          }
          if ($inputfilepath != "") {
              $deservesupdatetime=true;
              DBExec ($c, "update problemtable set probleminputfile=$oid1, probleminputfilehash='$inputhash' where problemnumber=$number ", "DBNewProblem(update inputfile)");
          }
          if ($fake == "t") {
              $deservesupdatetime=true;
              DBExec ($c, "update problemtable set fake='$fake' where problemnumber=$number", "DBNewProblem(update fake)");
          }

          if($deservesupdatetime) {
              $ds = DIRECTORY_SEPARATOR;
              if($ds=="") $ds = "/";
              @unlink($_SESSION["locr"] . $ds . "private" . $ds . "problemtmp" . $ds . "problem" . $number . '.name');
              DBExec ($c, "update problemtable set updatetime=" . $updatetime .
    	         " where problemnumber=$number", "DBNewProblem(time)");
          }
          if($cw)
              DBExec($c, "commit work", "DBNewProblem(commit)");
          LOGLevel ("Problema $number (inputfile=$inputfilename) $s ($usernumber)", 2);
          $ret=2;
      } else {

          if($cw)
              DBExec($c, "commit work", "DBNewProblem(commit)");
      }

      return $ret;
}
// recibe el número del concurso
// devuelve una matriz, donde cada línea tiene el número de atributos (número de problema), problema (nombre del problema),
// descfilename (nombre de archivo con descripción del problema) y descoid (objeto grande con descripción)
//funcion que fue remplazado abajo.
/*function DBGetProblems($contest,$showanyway=false) {//$showanyway=false mostrar de todos modos

    if (($b = DBContestClockInfo($contest)) == null)
        return array();

    if ($b["currenttime"] < 0 && !$showanyway)
        return array();//si tiempo es menor a cero falta la contest
    if(($c = DBContestInfo($contest)) == null) return array();
    if (time() < $c['conteststartdate'] && !$showanyway) return array();

    $c = DBConnect();
    //falta
    $sql = "select distinct p.problemnumber as number, p.problemname as problem, " .
        "p.problemfullname as fullname, p.problembasefilename as basefilename, " .
        "p.problemcolor as color, p.problemcolorname as colorname " .
        "from problemtable as p where p.fake!='t' and p.contestnumber=$contest and p.problembasefilename != '' and p.problemfullname !~ '(DEL)' order by p.problemnumber";
    $r = DBExec($c, $sql, "DBGetProblems(get problems)");
    $n = DBnlines($r);
    $a = array();
    for ($i=0;$i<$n;$i++) {
        $a[$i] = DBRow($r,$i);

        $ds = DIRECTORY_SEPARATOR;
        if($ds=="") $ds = "/";
        $nn = $a[$i]['number'];
        $ptmp = $_SESSION["locr"] . $ds . "private" . $ds . "problemtmp" . $ds . "contest" . $contest ."-problem" . $nn;
        if(is_readable($ptmp . ".name")) {
            $a[$i]['descfilename']=trim(file_get_contents($ptmp . ".name"));
            if($a[$i]['descfilename'] != '')
	           $a[$i]['descoid']=-1;
        }
    }
    return $a;
}*/
//mejorado para el sistema
function DBGetProblems($contest,$showanyway=false) {//$showanyway=false mostrar de todos modos

    if (($b = DBContestClockInfo($contest)) == null)
        return array();

    if ($b["currenttime"] < 0 && !$showanyway)
        return array();//si tiempo es menor a cero falta la contest
    if(($c = DBContestInfo($contest)) == null) return array();
    if (time() < $c['conteststartdate'] && !$showanyway) return array();

    $c = DBConnect();
    //falta
    $sql="select contestnumber as contest, problemnumber as problem ".
        "from problemcontesttable where contestnumber=$contest and data='' order by problemnumber";
    $r = DBExec($c, $sql, "DBGetProblems(get problemcontest)");
    $n = DBnlines($r);

    /*$sql = "select distinct p.problemnumber as number, p.problemname as problem, " .
        "p.problemfullname as fullname, p.problembasefilename as basefilename, " .
        "p.problemcolor as color, p.problemcolorname as colorname " .
        "from problemtable as p where p.fake!='t' and p.contestnumber=$contest and p.problembasefilename != '' and p.problemfullname !~ '(DEL)' order by p.problemnumber";
    $r = DBExec($c, $sql, "DBGetProblems(get problems)");*/
    $n = DBnlines($r);
    $a = array();
    for ($i=0;$i<$n;$i++) {
        //$fabian
        $pc=DBRow($r,$i);
        $sql = "select distinct p.problemnumber as number, p.problemname as problem, " .
            "p.problemfullname as fullname, p.problembasefilename as basefilename, " .
            "p.problemcolor as color, p.problemcolorname as colorname " .
            "from problemtable as p where p.fake!='t' and p.problemnumber=".$pc["problem"]." and p.problembasefilename != '' and p.problemfullname !~ '(DEL)' order by p.problemnumber";
        $rp = DBExec($c, $sql, "DBGetProblems(get problems)");
        //$fabian
        if($rp!=null){
            $a[$i] = DBRow($rp,0);

            $ds = DIRECTORY_SEPARATOR;
            if($ds=="") $ds = "/";
            $nn = $a[$i]['number'];
            $ptmp = $_SESSION["locr"] . $ds . "private" . $ds . "problemtmp" . $ds . "problem" . $nn;
            if(is_readable($ptmp . ".name")) {
                $a[$i]['descfilename']=trim(file_get_contents($ptmp . ".name"));
                if($a[$i]['descfilename'] != '')
    	           $a[$i]['descoid']=-1;
            }
        }
    }
    return $a;
}
//para extraer problemas para global
function DBGetProblemsGlobal($contest,$showanyway=false) {//$showanyway=false mostrar de todos modos

    if (($b = DBSiteInfo($contest,1)) == null)
        return array();

    if ($b["currenttime"] < 0 && !$showanyway)
        return array();//si tiempo es menor a cero falta la contest
    if(($c = DBContestInfo($contest)) == null) return array();
    if (time() < $c['conteststartdate'] && !$showanyway) return array();

    $c = DBConnect();
    $sql = "select distinct p.problemnumber as number, " .
        "p.problemfullname as fullname, p.problembasefilename as basefilename " .
        "from problemtable as p where p.fake!='t' and p.contestnumber=$contest and p.problembasefilename != '' and p.problemfullname !~ '(DEL)' order by p.problemnumber";
    $r = DBExec($c, $sql, "DBGetProblems(get problems)");
    $n = DBnlines($r);
    $a = array();
    for ($i=0;$i<$n;$i++) {
        $a[$i] = DBRow($r,$i);

        $ds = DIRECTORY_SEPARATOR;
        if($ds=="") $ds = "/";
        $nn = $a[$i]['number'];
        $ptmp = $_SESSION["locr"] . $ds . "private" . $ds . "problemtmp" . $ds . "contest" . $contest ."-problem" . $nn;
        if(is_readable($ptmp . ".name")) {
            $a[$i]['descfilename']=trim(file_get_contents($ptmp . ".name"));
            if($a[$i]['descfilename'] != '')
	           $a[$i]['descoid']=-1;
        }
    }
    return $a;
}



?>
