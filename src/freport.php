<?php
//para sacar ejecuciones realizadas
function DBRunReport($contest) {
	$c = DBConnect();


	$sql = "select r.runnumber as number, u.usernumber as un, u.username as user, r.rundatediff as timestamp, " .
                                "p.problemname as problem, l.langname as language, a.runanswer as answer, a.yes as yes " .
                             "from runtable as r, problemtable as p, problemcontesttable pc, langtable as l, answertable as a, usertable as u " .
                             "where r.contestnumber=$contest and r.contestnumber=pc.contestnumber and " .
                                    "r.runproblem=p.problemnumber and pc.problemnumber=pc.problemnumber and " .
				    "l.langnumber=r.runlangnumber and a.answernumber=r.runanswer and pc.data='' and " .
				    "(r.runstatus = 'judged' or r.runstatus = 'judged+') and " .
				    "u.usernumber=r.usernumber and " .
			 	    "u.usertype='team'";

	$xdados = array();
	$xuser = array();
	$xuserfull = array();
	$xuseryes = array();
	$xproblem = array();
	$xproblemyes = array();

	$xusername = array();

	$r = DBExec($c, "select u.usernumber as un, u.username as name, u.userfullname as fullname ".
	"from usertable as u, runtable as r where r.contestnumber=$contest and u.usernumber=r.usernumber and usertype='team' and userlastlogin is not null and userenabled='t'", "DBRunReport(get users)");
	$n = DBnlines($r);
	for ($i=0;$i<$n;$i++) {
		$a = DBRow($r,$i);
		$xusername[$a['un']] = $a['name'];
		$xuserfull[$a['name']] = $a['fullname'];
	}
	ksort($xusername);
	$xcolor = array();

	$pr = DBGetProblems($contest);

	for($i=0; $i<count($pr); $i++) {
		
	  $xproblem[$pr[$i]['problem']]=0;
	  $xproblemyes[$pr[$i]['problem']]=0;
	  $xcolor[$pr[$i]['problem']]=$pr[$i]['color'];
	}

	$xlanguage = array();
	$xlanguageyes = array();
	$xanswer = array();
	$xtimestamp = array();
	$xtimestampyes = array();
	$xpa = array();
	$xpl = array();
	$xla = array();
	$xup = array();

	$r = DBExec($c, $sql, "DBRunReport(get runs)");
	$n = DBnlines($r);

	for ($i=0;$i<$n;$i++) {
		$a = DBRow($r,$i);
		//if($_SESSION["usertable"]["usertype"] != "admin" && substr($a['user'],4,4) != substr($_SESSION["usertable"]["username"],5,4)) continue;
		$xdados[$i] = $a;
		// # para ejecuciones por team
		if(isset($xuser[$a['user']]))
			$xuser[$a['user']]++;
		else $xuser[$a['user']]=1;
		// # de ejecuciones por problema
		if(isset($xproblem[$a['problem']]))
			$xproblem[$a['problem']]++;
		else 	$xproblem[$a['problem']]=1;
		if($a['yes'] == 't') {
			if(isset($xuseryes[$a['user']]))
				$xuseryes[$a['user']]++;
			else $xuseryes[$a['user']]=1;
			$xproblemyes[$a['problem']]++;
		}
		// # of runs by language
		if(isset($xlanguage[$a['language']]))
			$xlanguage[$a['language']]++;
		else $xlanguage[$a['language']]=1;
		if($a['yes'] == 't') {
			if(isset($xlanguageyes[$a['language']]))
				$xlanguageyes[$a['language']]++;
			else $xlanguageyes[$a['language']]=1;
		}
		// # of runs by answer
		if(isset(	$xanswer[$a['answer']]))
			$xanswer[$a['answer']]++;
		else 	$xanswer[$a['answer']]=1;
		// time of the runs
		array_push($xtimestamp, $a['timestamp']);
		if($a['yes'] == 't')
			array_push($xtimestampyes, $a['timestamp']);

		// # of runs by answer by problem
		if(isset($xpa[$a['problem']][$a['answer']]))
			$xpa[$a['problem']][$a['answer']]++;
		else $xpa[$a['problem']][$a['answer']]=1;
		// # of runs by language by problem
		if(isset($xpl[$a['problem']][$a['language']]))
			$xpl[$a['problem']][$a['language']]++;
		else $xpl[$a['problem']][$a['language']]=1;
		// # of runs by answer by language
		if(isset($xla[$a['language']][$a['answer']]))
			$xla[$a['language']][$a['answer']]++;
		else $xla[$a['language']][$a['answer']]=1;
		// # of runs by problem by user
		// negative sign means team got an yes for the problem
		if(!isset($xup[$a['user']][$a['problem']]))
			$xup[$a['user']][$a['problem']]=0;
		if($xup[$a['user']][$a['problem']] < 0)
			$xup[$a['user']][$a['problem']]--;
		else {
			$xup[$a['user']][$a['problem']]++;
			if($a['yes'] == 't') $xup[$a['user']][$a['problem']] = - $xup[$a['user']][$a['problem']];
		}
	}
	ksort($xuser);
	ksort($xuseryes);
	ksort($xproblem);
	ksort($xproblemyes);
	ksort($xlanguage);
	ksort($xlanguageyes);
	ksort($xanswer);
	sort($xtimestamp);
	sort($xtimestampyes);
	//$x['problem']=xproblem
	$x = array(
		'color' => $xcolor,
		'user' => $xuser,
		'useryes' => $xuseryes,
		'username' => $xusername,
		'userfull' => $xuserfull,
		'problem' => $xproblem,
		'problemyes' => $xproblemyes,
		'language' => $xlanguage,
		'languageyes' => $xlanguageyes,
		'answer' => $xanswer,
		'timestamp' => $xtimestamp,
		'timestampyes' => $xtimestampyes,
		'pa' => $xpa,
		'pl' => $xpl,
		'la' => $xla,
		'up' => $xup);
	return $x;
}
?>
