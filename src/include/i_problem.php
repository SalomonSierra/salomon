<?php
session_start();//para iniciar session_sta
require_once("../globals.php");
require_once("../db.php");


if (isset($_POST["problemnumber"]) && is_numeric($_POST["problemnumber"]) &&
    isset($_POST["problemname"]) && $_POST["problemname"] != "") {

	if(strpos(trim($_POST["problemname"]),' ')!==false) {
		MSGError('El nombre corto del problema no puede tener espacios');
        echo "No";
	} else {

			if ($_FILES["probleminput"]["name"] != "") {

				$type=myhtmlspecialchars($_FILES["probleminput"]["type"]);
				$size=myhtmlspecialchars($_FILES["probleminput"]["size"]);
				$name=myhtmlspecialchars($_FILES["probleminput"]["name"]);
				$temp=myhtmlspecialchars($_FILES["probleminput"]["tmp_name"]);
				if (!is_uploaded_file($temp)) {
					IntrusionNotify("file upload problem.");
					ForceLoad("../index.php");//index.php
				}

			} else $name = "";

			$param = array();
			$param['number'] = $_POST["problemnumber"];
			$param['name'] = trim($_POST["problemname"]);
			$param['inputfilename'] = $name;
			$param['inputfilepath'] = $temp;
			$param['fake'] = 'f';
			$param['colorname'] = trim($_POST["colorname"]);
			$param['color'] = trim($_POST["color"]);
			//crea un nuevo problema o actuliza un problema el importa un archivo a base de datos y devuelve oid
			DBNewProblem ($_SESSION["usertable"]["usernumber"], $param);
            echo "Yes";//exito
	}

	//ForceLoad("problem.php");
}else {
    echo "No";
}





?>
