<?php
session_start();//para iniciar session_sta
require_once("../globals.php");
require_once("../db.php");
if (isset($_POST["name"]) && isset($_POST["password"])) {

    //echo $_POST["name"]." ".$_POST["password"];

    if(function_exists("globalconf") && function_exists("sanitizeVariables")){

    		$name=$_POST["name"];
    		$password=$_POST["password"];
    		// función para iniciar sesión en un usuario. Buscará un concurso activo, comprobará en qué sitio
    		// local y luego busque el usuario en el sitio local del concurso activo. Además, comprueba otros
    		// banderas, como inicios de sesión habilitados, ip correcta, si el usuario ya inició sesión, etc.
    		$usertable=DBLogIn($name, $password);
    		if(!$usertable){
    			//index.php
                echo "No";
    			//ForceLoad("index.php");
    		}else{
    			echo "Yes";
    		}

    }else{
    	//echo "<script language=\"JavaScript\">\n";
    	//Unable to load config files. Possible file permission problem in the salomon directory.
    	echo "No se pueden cargar los archivos de configuración. Posible problema de permisos de archivos en el directorio salomon.";
    	//echo "</script>\n";
    }
}else {
    //DBContestInfo retorna la informacion de la competencia en caso no retorna null

    if(isset($_POST["username"]) && isset($_POST["userfullname"]) && isset($_POST["useremail"]) && isset($_POST["userdesc"]) && isset($_POST["userip"]) &&
        isset($_POST["usernumber"]) && isset($_POST["userenabled"]) &&
        isset($_POST["usermultilogin"]) && isset($_POST["usertype"]) &&
        isset($_POST["passwordn1"]) && isset($_POST["passwordn2"])) {

    	$param['user'] = htmlspecialchars($_POST["usernumber"]);
    	//$param['site'] = htmlspecialchars($_POST["usersitenumber"]);
    	$param['username'] = htmlspecialchars($_POST["username"]);

    	$param['enabled'] = htmlspecialchars($_POST["userenabled"]);
    	$param['multilogin'] = htmlspecialchars($_POST["usermultilogin"]);
    	$param['userfull'] = htmlspecialchars($_POST["userfullname"]);
    	$param['useremail'] = htmlspecialchars($_POST["useremail"]);
    	$param['userdesc'] = htmlspecialchars($_POST["userdesc"]);
    	$param['type'] = htmlspecialchars($_POST["usertype"]);
    	$param['permitip'] = htmlspecialchars($_POST["userip"]);
    	//$param['contest'] = $ct["contestnumber"];
    	$param['changepass']='t';


        if ($_POST["passwordn1"] == $_POST["passwordn2"]) {

            //si son iguales retorna 0 si no retorna sub en resto de dos str.
            //pasa nuevopass1 datapass2
            $param['pass'] = bighexsub($_POST["passwordn1"],myhash('fabians7'));

            while(strlen($param['pass']) < strlen(myhash('fabians7')))
                $param['pass'] = '0' . $param['pass'];
            if($param['user'] != 0)
                DBNewUser($param);//funcion para actulizar o insertar un nuevo usuario segun los datos que pasa
            echo "Yes";//aceptado
        } else MSGError ("Passwords don't match.");

        //ForceLoad("user0.php");

    }else{
        echo "No existe";
    }
}

?>
