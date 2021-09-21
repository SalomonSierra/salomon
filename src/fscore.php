<?php
//niega la comparacion scores
function ScoreCMPinv($a,$b) {
    return -ScoreCMP($a,$b);
}
//comparacion del score
function ScoreCMP($a,$b) {

    if ($a["totalcount"]=="") $a["totalcount"]=0;
    if ($b["totalcount"]=="") $b["totalcount"]=0;
    if ($a["totaltime"]=="") $a["totaltime"]=0;
    if ($b["totaltime"]=="") $b["totaltime"]=0;

    if ($a["totalcount"]>$b["totalcount"]) return 1;
    else if ($a["totalcount"]<$b["totalcount"]) return -1;
    else {
        if ($a["totaltime"]<$b["totaltime"]) return 1;
        else if ($a["totaltime"]>$b["totaltime"]) return -1;
        else {
            if(isset($a["first"]) && $a["first"] != 0) {
	            if($a["first"]<$b["first"]) return 1;
	            else if($a["first"]>$b["first"]) return -1;
            }
            if ($a["user"]<$b["user"]) return 1;
            else if ($a["user"]>$b["user"]) return -1;
            else {
                return 0;
	             /*if ($a["site"]<$b["site"]) return 1;
	             else if ($a["site"]>$b["site"]) return -1;
	             else return 0;*/
            }

        }
    }
}
//ordena segun la puntuacion en el score
function ordena($a) {
  /*
  $n = count($a);
  $r = array();
  for ($i=0; $i<$n; $i++) {
    $max=null;
    foreach($a as $e => $c) {
      if ($c != null && ($max==null || ScoreCMP($c,$max) > 0)) {
	//			     $j=0;
	//	     for(;$j<$i;$j++)
	//	       if($r[$j]['user']==$a[$e]['user'] && $r[$j]['site']==$a[$e]['site']) break;
	//	     if($j>=$i) {
	$max = $c;
	$maxe = $e;
	//	     }
      }
    }
    if ($max==null) break;
    $r[$i] = $max;
    $a[$maxe] = null;
  }
  */
  //uasort — Ordena un array con una función de comparación definida por el usuario
  //y mantiene la asociación de índices
  uasort($a, "ScoreCMPinv");
  return $a;
  /*
  $r = array();
  $j = 0;
  foreach($a as $k => $v) {
    $r[$j] = $v;
    $j++;
  }
  $j = 0;
  $r = array();
  foreach($a as $k) {
    if($j == 0) $r[0] = $k;
    else {
      if($k['user'] != $r[$j]['user'] || $k['site'] != $r[$j]['site']) {
	$j++;
	$r[$j] = $k;
      }
    }
  }
  return $r;
  */
}
//funcion para score ordenado
//funcio mejorado
function DBScore($contest, $verifylastmile, $hor=-1, $globalcontest='0') {

    $c = DBConnect();
    /*$r = DBExec($c, "select sitenumber as number from sitetable where contestnumber=$contest and siteactive='t'",
	      "DBScore(get site)");*/
    $r = DBExec($c, "select contestnumber as number from contesttable where contestnumber=$contest and contestactive='t'",
  	      "DBScore(get contest)");
    $n = DBnlines($r);
    if ($n == 0) {
        LOGError("Unable to get contest information. No active contest available (contest=$contest)");
        MSGError("Unable to get contest information. No active contest available. Contact an admin now!");
        exit;
    }
    $a = array();
    $resp = array();
    $whichcontests=explode(',',$globalcontest);
    for ($i=0;$i<$n;$i++) {
        $a = DBRow($r,$i);
        if(in_array($a["number"], $whichcontests) || in_array(0,$whichcontests)) {
            //contest, siteuser, 0, -1
            //devuelve el datos y el score ordenado
            //list($resp1,$data0) = DBScoreContest($contest, $a["number"], $verifylastmile, $hor);
            list($resp1,$data0) = DBScoreContest($contest, $verifylastmile, $hor);
            $resp =  array_merge($resp, $resp1);
        }
    }
    $ds = DIRECTORY_SEPARATOR;
    if($ds=="") $ds = "/";
    // recibe el número del concurso
    // devuelve una matriz, donde cada línea tiene el número de atributos (número de problema), problema (nombre del problema),
    // descfilename (nombre de archivo con descripción del problema) y descoid (objeto grande con descripción)
    $probs=DBGetProblems($contest); $nprobs=count($probs);

    $scoreitems = glob($_SESSION['locr'] . $ds . "private" .$ds . "remotescores" . $ds . "score*.dat", GLOB_NOSORT);
    //array_map — Aplica la retrollamada a los elementos de los arrays dados
    //array_multisort — Ordena varios arrays, o arrays multidimensionales
    array_multisort(array_map('filemtime', $scoreitems), SORT_NUMERIC, SORT_DESC, $scoreitems);
    /*
    foreach ($scoreitems as $fname) {
        $namear=explode('_',$fname);
        //$overloadsite=-1;
        $overloadcontest=-1;
        if(isset($namear[3]) && trim($namear[2]) != '' && is_numeric($namear[2])) $overloadcontest=$namear[2];
        $fc=file_get_contents($fname);
        if(($arr = unserialize(base64_decode($fc)))===false) {
            LOGError("File " . sanitizeText($fname) . " is not compatible");
        } else {
            if(is_array($arr)) {

	             if(isset($arr['site'])) {

	                  $site=$arr['site'];
	                  if($overloadsite>0) $site=$overloadsite;
                      //Comprueba si un valor existe en un array
	                  if(!in_array($site, $whichsites) && !in_array(0,$whichsites)) continue;
	                  $fine=1;
                      //reset — Establece el puntero interno de un array a su primer elemento
	                  reset($resp);
                      //each — Devolver el par clave/valor actual de un array y avanzar el cursor del array
	                  while(list($e, $c) = each($resp)) {
	                      if($resp[$e]['site']==$site) { $fine=0; break; }
	                  }
	                  if($fine) {
	                      list($arr,$data0) = DBScoreContest($contest, $verifylastmile, $hor, $arr);
	                      reset($arr);
	                      while(list($ee,$cc) = each($arr)) {
	                          if($site != $arr[$ee]['site']) {
		                          $arr[$ee]=null;
		                          unset($arr[$ee]);
	                           } else {
                            		// just to make the color of the other site changed to the color of the problem in this site
                            		while(list($e2,$c2) = each($arr[$ee]["problem"])) {
                            		    for($prob=0; $prob<$nprobs; $prob++) {
                            		        if($probs[$prob]['number']==$e2) {
                            		            $arr[$ee]['problem'][$e2]['color'] = $probs[$prob]['color'];
                            		            $arr[$ee]['problem'][$e2]['colorname'] = $probs[$prob]['colorname'];
                            		            break;
                            		         }
                            		     }
                            		 }
	                            }
	                        }
                    	    if(false) {

                    	        $arrori = $arr;
                    	        reset($arrori);  //cassio cassiopc
                    	        $pname = array('A','B','C','D','E','F','G','H','I','J','K');
                    	        while(list($ee,$cc) = each($arrori)) {
                    		        for($pi=0; $pi < 11; $pi++) unset($arr[$ee]['problem'][$pi+1]);
                    		        reset($arrori[$ee]["problem"]);
                    		        while(list($e2,$c2) = each($arrori[$ee]["problem"])) {
                    		            for($pi=0; $pi < 11; $pi++)
                    		            if(isset($arrori[$ee]['problem'][$e2]['name']) && trim($arrori[$ee]['problem'][$e2]['name']) == $pname[$pi]) break;
                    		            if($pi < 11) {
                    		                $arr[$ee]['problem'][$pi+1] = $arrori[$ee]['problem'][$e2];
                    		            }
                    		        }
                    	        }
                    	    }

	                        $resp = array_merge($resp, $arr);
	                     }
                	} else {

                	    // old version -- just for compatibility ---

                	    while(list($ee,$cc) = each($arr)) {
                	        $fine=1;
                	        reset($resp);
                	        while(list($e, $c) = each($resp)) {
                	            if($resp[$e]['site']==$arr[$ee]['site']) { $fine=0; break; }
                	        }
                	        if($fine==0) $arr[$ee]=null;
                	        else {
                	            // just to make the color of the other site changed to the color of the problem in this site
                	            while(list($e2,$c2) = each($arr[$ee]["problem"])) {
                		            for($prob=0; $prob<$nprobs; $prob++) {
                		                if($probs[$prob]['number']==$e2) {
                		                    $arr[$ee]['problem'][$e2]['color'] = $probs[$prob]['color'];
                		                    $arr[$ee]['problem'][$e2]['colorname'] = $probs[$prob]['colorname'];
                		                    break;
                		                }
                		             }
                	             }
                	         }
                	     }
                	     $resp = array_merge($resp, $arr);
                	     // ---- end of old version ---
                	 }
                 }
               //		MSGError("got scores from $fname");
            }
      }*/

      if (($result = ordena ($resp)) === false) {
           LOGError("Error while sorting scores (contest=$contest).");
           MSGError("Error while sorting scores. Contact an admin now!");
      }
      return $result;
}
//funcion para seleccionar la tabla runtable y answertable y saca yes si es t return true no false para globo
//modificado
function DBBalloon($contest, $user, $problem, $c=null){
    if($c==null)
        $c=DBConnect();
    if(($b=DBContestClockInfo($contest, $c))==null)
        exit;
    $blocal=$b;

    if(($ct=DBContestInfo($contest,$c))==null)
        exit;
    $t=time();
    $ta=$blocal["currenttime"];
    $tf=$b["contestduration"];
    $r=DBExec($c,"select r.rundatediff as time, a.yes as yes from ".
    "runtable as r, answertable as a where r.runanswer=a.answernumber and ".
    "r.usernumber=$user and r.runproblem=$problem and ".
    "r.contestnumber=$contest and (r.runstatus ~ 'judged' or r.runstatus ~ 'judged+') and ".
    "r.rundatediff>=0 ".
    "and r.rundatediff<=$tf ".
    "and r.rundatediffans<=$ta ".
    "order by r.rundatediff","DBBalloon(get runs)");

    $n=DBnlines($r);
    for($i=0;$i<$n;$i++){
        $a=DBRow($r,$i);
        if($a["yes"]=='t') return true;
    }
    return false;
}
//funcion para sacar la informacion de las tablas runtable, answertable, problemtable, usertable. con condiciones
//(contest,site,$verifylastmile,ta)
//mejorado
function DBRecentNews($contest, $verifylastmile, $minutes=3) {

    if (($b = DBContestClockInfo($contest)) == null)
        exit;
    if (($blocal = DBContestClockInfo($contest)) == null)
        exit;
    if (($ct = DBContestInfo($contest)) == null)
        exit;

    $t = time();
    $ta = $blocal["currenttime"];
    $taa = $ta - $minutes*60;
    if ($verifylastmile)
        $tf = $b["contestlastmilescore"];
    else {
        $tf = $b["contestduration"];
    }

    $c = DBConnect();
    /*$r = DBExec($c, "select a.yes as yes, p.problemcolor as color, p.problemcolorname as colorname, u.userfullname as userfullname, " .
	      "u.usernumber as usernumber, p.problemnumber as problemnumber, p.problemname, (r.rundatediffans>$ta) as fut, min(r.rundatediff) as time from " .
	      "runtable as r, answertable as a, problemtable as p, usertable as u where r.runanswer=a.answernumber and " .
	      "p.contestnumber=$contest and a.contestnumber=$contest and r.usernumber = u.usernumber and u.usertype='team' and " .
	      "p.problemnumber=r.runproblem and r.contestnumber=$contest and r.runsitenumber=$site and u.userenabled='t' and (not r.runstatus ~ 'deleted') and " .
	      "r.rundatediff>=$taa and r.rundatediff<=$tf and r.rundatediff<=$ta and u.contestnumber=$contest and u.usersitenumber=$site and " .
	      "((a.yes='t' and r.rundatediffans<=$ta) or (r.rundatediffans>$ta)) " .
	      "group by a.yes,p.problemcolor,p.problemcolorname,p.problemname,u.userfullname,u.usernumber,p.problemnumber,fut order by time", "DBRecentNews(get runs)");
          */
    $r = DBExec($c, "select a.yes as yes, p.problemcolor as color, p.problemcolorname as colorname, u.userfullname as userfullname, " .
      	  "u.usernumber as usernumber, p.problemnumber as problemnumber, p.problemname, (r.rundatediffans>$ta) as fut, min(r.rundatediff) as time from " .
      	  "runtable as r, answertable as a, problemtable as p, usertable as u where r.runanswer=a.answernumber and " .
      	  "r.usernumber = u.usernumber and u.usertype='team' and " .
      	  "p.problemnumber=r.runproblem and r.contestnumber=$contest and u.userenabled='t' and (not r.runstatus ~ 'deleted') and " .
      	  "r.rundatediff>=$taa and r.rundatediff<=$tf and r.rundatediff<=$ta and " .
      	  "((a.yes='t' and r.rundatediffans<=$ta) or (r.rundatediffans>$ta)) " .
      	  "group by a.yes,p.problemcolor,p.problemcolorname,p.problemname,u.userfullname,u.usernumber,p.problemnumber,fut order by time", "DBRecentNews(get runs)");

    $n = DBnlines($r);
    $a = array();
    for ($i=0;$i<$n;$i++) {
        $a[$i] = DBRow($r,$i);
        if($a[$i]["fut"]=='t' && $a[$i]["yes"]=='t') $a[$i]["yes"]='f';
    }
    return $a;
}
//contest, siteuser, 0, -1
//devuelve el dato ordenado score, el resultado de consulta en run,problem,answer,user
//mejorado en abajo
/*function DBScoreSite($contest, $site, $verifylastmile, $hor=-1, $data=null) {
    //(contest,site, c=null,msg=true)
    //retorna el resultado de un contest y sitio dado mas la info. de siterunning, currenttime,siteendeddate
    //de la tabla sitimetable;
    if (($blocal = DBSiteInfo($contest, $_SESSION["usertable"]["usersitenumber"])) == null)
        exit;
    if (($b = DBSiteInfo($contest, $site, null, false)) == null)
        $b=$blocal;
    if (($ct = DBContestInfo($contest)) == null)
        exit;

    $t = time();
    $ta = $blocal["currenttime"];
    if($hor >= 0) $ta = $hor;
    if ($verifylastmile)
        $tf = $b["sitelastmilescore"];
    else {
        $tf = $b["siteduration"];
    }
    if($data != null && is_numeric($data)) {
        if($data < $ta) $ta = $data;
        $data=null;
    }

    $data0=array();
    if($data==null) {
        $c = DBConnect();
        $resp = array();
        $r = DBExec($c, "select * from usertable where contestnumber=$contest and usersitenumber=$site and ".
		      "usertype='team' and userlastlogin is not null and userenabled='t'", "DBScoreSite(get users)");
        $n = DBnlines($r);
        //para sacar de cada usuario que cumple la condiciones de la consulta
        for ($i=0;$i<$n;$i++) {
            ////function que retorna con la modificacion del descuser separando si en caso que exista
            $a = cleanuserdesc(DBRow($r,$i));
            //$resp[usernumber-site][user]=usernumber;
            $resp[$a["usernumber"] . '-' . $site]["user"]=$a["usernumber"];
            $resp[$a["usernumber"] . '-' . $site]["site"]=$a["usersitenumber"];
            $resp[$a["usernumber"] . '-' . $site]["username"]=$a["username"];
            $resp[$a["usernumber"] . '-' . $site]["usertype"]=$a["usertype"];
            $resp[$a["usernumber"] . '-' . $site]["userfullname"]=$a["userfullname"];
            $resp[$a["usernumber"] . '-' . $site]["usershortinstitution"]=$a["usershortinstitution"];
            $resp[$a["usernumber"] . '-' . $site]["userflag"]=$a["userflag"];
            if($a["usersitename"] == '')
	           $resp[$a["usernumber"] . '-' . $site]["usersitename"]=$a["usersitenumber"];
            else
	           $resp[$a["usernumber"] . '-' . $site]["usersitename"]=$a["usersitename"];
            $resp[$a["usernumber"] . '-' . $site]["totaltime"]=0;
            $resp[$a["usernumber"] . '-' . $site]["totalcount"]=0;
            $resp[$a["usernumber"] . '-' . $site]["problem"]=array();
        }
        $r = DBExec($c, "select r.usernumber as user, p.problemname as problemname, r.runproblem as problem, ".
		    "p.problemcolor as color, p.problemcolorname as colorname, " .
		    "r.rundatediff as time, r.rundatediffans as anstime, a.yes as yes, r.runanswer as answer from " .
		    "runtable as r, answertable as a, problemtable as p where r.runanswer=a.answernumber and " .
		    "a.contestnumber=$contest and p.problemnumber=r.runproblem and p.contestnumber=$contest and " .
		    "r.contestnumber=$contest and r.runsitenumber=$site and (r.runstatus ~ 'judged' or r.runstatus ~ 'judged+') and " .
		    "r.rundatediff>=0 and r.rundatediff<=$tf and r.rundatediffans<=$ta " .
		    "order by r.usernumber, r.runproblem, r.rundatediff", "DBScoreSite(get runs)");
        $n = DBnlines($r);
        $a = array();
        for ($i=0;$i<$n;$i++) {
            $a[$i] = DBRow($r,$i);
        }
        $data0['n']=$n;
        $data0['resp']=$resp;
        $data0['a']=$a;//toda la informacion del mismo contest en run
        $data0['site']=$site;
    } else {
        $resp=$data['resp'];
        $n=$data['n'];
        $a=$data['a'];
    }

    $i=0;
    while ($i<$n) {
        if($a[$i]["anstime"] > $ta) { $i++; continue; }
        $user = $a[$i]["user"];
        $problem = $a[$i]["problem"];//runproblem
        $time = 0;
        $k = 0;
        if(!isset($resp[$user . '-' . $site])) { $i++; continue; }
        $resp[$user . '-' . $site]["user"] = $user;
        $resp[$user . '-' . $site]["site"] = $site;
        ////resp[1-1][problem][1][name]=problemname;
        $resp[$user . '-' . $site]["problem"][$problem]["name"] = $a[$i]["problemname"];
        $resp[$user . '-' . $site]["problem"][$problem]["color"] = $a[$i]["color"];
        $resp[$user . '-' . $site]["problem"][$problem]["colorname"] = $a[$i]["colorname"];
        $resp[$user . '-' . $site]["problem"][$problem]["solved"] = false;
        $resp[$user . '-' . $site]["problem"][$problem]["judging"] = false;
        $resp[$user . '-' . $site]["problem"][$problem]["time"] = 0;
        $resp[$user . '-' . $site]["problem"][$problem]["penalty"] = 0;
        $resp[$user . '-' . $site]["problem"][$problem]["count"] = 0;

        while ($i<$n && $a[$i]["anstime"] <= $ta && $a[$i]["user"]==$user && $a[$i]["problem"]==$problem && $a[$i]["yes"]!='t') {
            $time += (int) (($ct["contestpenalty"])/60);//no resolvio el problema mas penalizacion
            $k++;
            $i++;
        }

        $resp[$user . '-' . $site]["problem"][$problem]["count"] = $k;
        if ($i>=$n) break;
        if($a[$i]["anstime"] <= $ta && $a[$i]["user"]==$user && $a[$i]["problem"]==$problem && $a[$i]["yes"]=='t') {
            $timet = (int) (($a[$i]["time"])/60);
            if(!isset($resp[$user . '-' . $site]["first"]) || $timet > $resp[$user . '-' . $site]["first"])  // > means last run, < would be first run //> significa última ejecución, <sería la primera ejecución
	           $resp[$user . '-' . $site]["first"] = $timet;
            $time += $timet;
            $resp[$user . '-' . $site]["problem"][$problem]["time"] = $timet;
            $resp[$user . '-' . $site]["problem"][$problem]["penalty"] = $time;
            $resp[$user . '-' . $site]["problem"][$problem]["solved"] = true;
            $resp[$user . '-' . $site]["problem"][$problem]["count"]++;
            $resp[$user . '-' . $site]["totaltime"] += $time;
            $resp[$user . '-' . $site]["totalcount"]++;
        }
        while ($i<$n && $a[$i]["user"]==$user && $a[$i]["problem"]==$problem) {
            $i++;
        }
    }

    if($data==null) {
        //funcion para sacar la informacion de las tablas runtable, answertable, problemtable, usertable. con condiciones
        //(contest,site,$verifylastmile, $ta)
        $aa = DBRecentNews($contest, $site, $verifylastmile, $ta);
        $data0['aa']=$aa;
    } else $aa=$data['aa'];

    for($i=0; $i<count($aa); $i++) {
        if($aa[$i]["fut"]=='t') {
            //resp[usernumber-site][problem][problemnumber][judging]=true
            $resp[$aa[$i]["usernumber"] . '-' . $site]["problem"][$aa[$i]["problemnumber"]]["judging"] = true;
        }
    }
    //ordena segun la puntuacion en el score
    if (($result = ordena ($resp)) === false) {
        //Error al ordenar las puntuaciones. ¡Póngase en contacto con un administrador ahora!
        LOGError("Error while sorting scores (contest=$contest, site=$site).");
        MSGError("Error while sorting scores. Contact an admin now!");
    }
    return array($result,$data0);
}*/
function DBScoreContest($contest, $verifylastmile, $hor=-1, $data=null) {
    //(contest,site, c=null,msg=true)
    //retorna el resultado de un contest y sitio dado mas la info. de siterunning, currenttime,siteendeddate
    //de la tabla sitimetable;
    if (($blocal = DBContestClockInfo($contest)) == null)
        exit;
    if (($b = DBContestClockInfo($contest, null, false)) == null)
        $b=$blocal;
    if (($ct = DBContestInfo($contest)) == null)
        exit;

    $t = time();
    $ta = $blocal["currenttime"];
    if($hor >= 0) $ta = $hor;
    if ($verifylastmile)
        $tf = $b["contestlastmilescore"];
    else {
        $tf = $b["contestduration"];
    }
    if($data != null && is_numeric($data)) {
        if($data < $ta) $ta = $data;
        $data=null;
    }

    $data0=array();
    if($data==null) {
        $c = DBConnect();
        $resp = array();
        /*$r = DBExec($c, "select * from usertable where contestnumber=$contest and usersitenumber=$site and ".
		      "usertype='team' and userlastlogin is not null and userenabled='t'", "DBScoreSite(get users)");*/
        $sql="select distinct u.usernumber, u.username, u.usertype, u.userfullname, u.userdesc ".
            "from runtable as r, usertable as u where r.contestnumber=$contest and r.usernumber=u.usernumber ".
            "and u.usertype='team' and u.userlastlogin is not null and u.userenabled='t'";
        $r=DBExec($c,$sql,"DBScoreContest(get users)");
        $n = DBnlines($r);
        //para sacar de cada usuario que cumple la condiciones de la consulta
        for ($i=0;$i<$n;$i++) {
            ////function que retorna con la modificacion del descuser separando si en caso que exista
            $a = cleanuserdesc(DBRow($r,$i));
            //$resp[usernumber-site][user]=usernumber;
            $resp[$a["usernumber"]]["user"]=$a["usernumber"];
            //$resp[$a["usernumber"]]["site"]=$a["usersitenumber"];
            $resp[$a["usernumber"]]["username"]=$a["username"];
            $resp[$a["usernumber"]]["usertype"]=$a["usertype"];
            $resp[$a["usernumber"]]["userfullname"]=$a["userfullname"];
            $resp[$a["usernumber"]]["usershortinstitution"]=$a["usershortinstitution"];
            $resp[$a["usernumber"]]["userflag"]=$a["userflag"];

            /*if($a["usersitename"] == '')
	           $resp[$a["usernumber"] . '-' . $site]["usersitename"]=$a["usersitenumber"];
            else
	           $resp[$a["usernumber"] . '-' . $site]["usersitename"]=$a["usersitename"];*/
            $resp[$a["usernumber"]]["totaltime"]=0;
            $resp[$a["usernumber"]]["totalcount"]=0;
            $resp[$a["usernumber"]]["problem"]=array();
        }
        $r = DBExec($c, "select r.usernumber as user, p.problemname as problemname, r.runproblem as problem, ".
		    "p.problemcolor as color, p.problemcolorname as colorname, " .
		    "r.rundatediff as time, r.rundatediffans as anstime, a.yes as yes, r.runanswer as answer from " .
		    "runtable as r, answertable as a, problemtable as p where r.runanswer=a.answernumber and " .
		    "p.problemnumber=r.runproblem and " .
		    "r.contestnumber=$contest and (r.runstatus ~ 'judged' or r.runstatus ~ 'judged+') and " .
		    "r.rundatediff>=0 and r.rundatediff<=$tf and r.rundatediffans<=$ta " .
		    "order by r.usernumber, r.runproblem, r.rundatediff", "DBScoreContest(get runs)");
        $n = DBnlines($r);
        $a = array();
        for ($i=0;$i<$n;$i++) {
            $a[$i] = DBRow($r,$i);
        }
        $data0['n']=$n;
        $data0['resp']=$resp;
        $data0['a']=$a;//toda la informacion del mismo contest en run
        //$data0['site']=$site;
    } else {
        $resp=$data['resp'];
        $n=$data['n'];
        $a=$data['a'];
    }

    $i=0;
    while ($i<$n) {
        if($a[$i]["anstime"] > $ta) { $i++; continue; }
        $user = $a[$i]["user"];
        $problem = $a[$i]["problem"];//runproblem
        $time = 0;
        $k = 0;
        if(!isset($resp[$user])) { $i++; continue; }
        $resp[$user]["user"] = $user;
        //$resp[$user]["site"] = $site;
        ////resp[1-1][problem][1][name]=problemname;
        $resp[$user]["problem"][$problem]["name"] = $a[$i]["problemname"];
        $resp[$user]["problem"][$problem]["color"] = $a[$i]["color"];
        $resp[$user]["problem"][$problem]["colorname"] = $a[$i]["colorname"];
        $resp[$user]["problem"][$problem]["solved"] = false;
        $resp[$user]["problem"][$problem]["judging"] = false;
        $resp[$user]["problem"][$problem]["time"] = 0;
        $resp[$user]["problem"][$problem]["penalty"] = 0;
        $resp[$user]["problem"][$problem]["count"] = 0;

        while ($i<$n && $a[$i]["anstime"] <= $ta && $a[$i]["user"]==$user && $a[$i]["problem"]==$problem && $a[$i]["yes"]!='t') {
            $time += (int) (($ct["contestpenalty"])/60);//no resolvio el problema mas penalizacion
            $k++;
            $i++;
        }

        $resp[$user]["problem"][$problem]["count"] = $k;
        if ($i>=$n) break;
        if($a[$i]["anstime"] <= $ta && $a[$i]["user"]==$user && $a[$i]["problem"]==$problem && $a[$i]["yes"]=='t') {
            $timet = (int) (($a[$i]["time"])/60);
            if(!isset($resp[$user]["first"]) || $timet > $resp[$user]["first"])  // > means last run, < would be first run //> significa última ejecución, <sería la primera ejecución
	           $resp[$user]["first"] = $timet;
            $time += $timet;
            $resp[$user]["problem"][$problem]["time"] = $timet;
            $resp[$user]["problem"][$problem]["penalty"] = $time;
            $resp[$user]["problem"][$problem]["solved"] = true;
            $resp[$user]["problem"][$problem]["count"]++;
            $resp[$user]["totaltime"] += $time;
            $resp[$user]["totalcount"]++;
        }
        while ($i<$n && $a[$i]["user"]==$user && $a[$i]["problem"]==$problem) {
            $i++;
        }
    }

    if($data==null) {
        //funcion para sacar la informacion de las tablas runtable, answertable, problemtable, usertable. con condiciones
        //(contest,site,$verifylastmile, $ta)

        $aa = DBRecentNews($contest, $verifylastmile, $ta);
        $data0['aa']=$aa;
    } else $aa=$data['aa'];

    for($i=0; $i<count($aa); $i++) {
        if($aa[$i]["fut"]=='t') {
            //resp[usernumber-site][problem][problemnumber][judging]=true
            $resp[$aa[$i]["usernumber"]]["problem"][$aa[$i]["problemnumber"]]["judging"] = true;
        }
    }
    //ordena segun la puntuacion en el score
    if (($result = ordena ($resp)) === false) {
        //Error al ordenar las puntuaciones. ¡Póngase en contacto con un administrador ahora!
        LOGError("Error while sorting scores (contest=$contest, site=$site).");
        MSGError("Error while sorting scores. Contact an admin now!");
    }
    return array($result,$data0);
}
// eof
?>
