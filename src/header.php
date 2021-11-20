<?php
ob_start();//activa el almacenamiento en bufer de salida
//los script de php a menudo generan HTML dinamico que no debe almacenarse en la
//cache del navegador cliente o en la cache de cualquier proxy situando entre el
//sevidor y el navegador cliente. se puede obligar a muchos proies y clientes a que
//deshabiliten el almacenamiento en cache con .
header("Expires: ".gmdate("D, d M Y H:i:s")." GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

header("Content-Type: text/html; charset=utf-8");

session_start();//para iniciar session_sta
$_SESSION["loc"]=dirname($_SERVER['PHP_SELF']);//PARA CAPTURAR EL DIRECTORIO PADRE Y DONDE ESTA UBICADO
if($_SESSION["loc"]=="/") $_SESSION["loc"]="";
$_SESSION["locr"]=dirname(__FILE__);//__FILE__ te proporciona cual es ,la direccion de tu archivo diname para el PADRE
if($_SESSION["locr"]=="/") $_SESSION["locr"]="";

require_once("globals.php");
require_once("db.php");

if(!isset($_GET["name"])){//esta en validsession globals.php
	//funcion retorna true o false si no existe usertable en session false si es id diferente false
	//si aun no inicio session FALSE
	//de usuario es true su multi llogion  TRUE
	//si el ip son diferentes FALSE
	if(ValidSession()){//si la variable global name no existe realizar esto
		//realizar un update al la tabla usertable campos userssion y otros si es admin verifica el tiempo y realizar un
		//update si es menor a -600 el clocktime y limpia la carpeta problemtmp
		DBLogOut($_SESSION["usertable"]["usernumber"],$_SESSION["usertable"]["username"]=='admin');
	}
	session_unset();
	session_destroy();
	session_start();
	$_SESSION["loc"]=dirname($_SERVER['PHP_SELF']);//PARA CAPTURAR EL DIRECTORIO PADRE Y DONDE ESTA UBICADO
	if($_SESSION["loc"]=="/") $_SESSION["loc"]="";
	$_SESSION["locr"]=dirname(__FILE__);//__FILE__ te proporciona cual es ,la direccion de tu archivo diname para el PADRE
	if($_SESSION["locr"]=="/") $_SESSION["locr"]="";
}

if(isset($_GET["getsessionid"])){//por ahora no se ve
	echo session_id();
	exit;
}
ob_end_flush();//ya que el contenido del bufer es descartado despues de llamar a ob_end_flush().
require_once("version.php");



?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8"><!--en el antigo es diferente-->
		<title>Juez Salomon <?php echo $SALOMONVERSION; ?></title>

		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
		<link rel="stylesheet" href="bootstrap4/css/modern-business.css">
<?php
if(function_exists("globalconf") && function_exists("sanitizeVariables")){
	if(isset($_GET["name"]) && $_GET["name"] !="" ){

		$name=$_GET["name"];
		$password=$_GET["password"];
		// función para iniciar sesión en un usuario. Buscará un concurso activo, comprobará en qué sitio
		// local y luego busque el usuario en el sitio local del concurso activo. Además, comprueba otros
		// banderas, como inicios de sesión habilitados, ip correcta, si el usuario ya inició sesión, etc.

			//retorna la informacion de la competencia en caso no retorna null

			if(($ct=DBContestInfo(0)) == null)
				ForceLoad("index.php");//index.php

			echo "<script language=\"JavaScript\">\n";
			echo "document.location='".$_SESSION["usertable"]["usertype"]."/index.php';\n";
			echo "</script>\n";

			exit;

	}
}else{
	echo "<script language=\"JavaScript\">\n";
	//Unable to load config files. Possible file permission problem in the salomon directory.
	echo "alert('No se pueden cargar los archivos de configuración. Posible problema de permisos de archivos en el directorio salomon.');\n";
	echo "</script>\n";
}
?>
	</head>
	<body onload="document.form1.name.focus()" class="p-0"><!--para que se onload: ejecuta una funcion inmediatamente en este caso focus-->
		<!--D0D7E8-->
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="background:#C28FD9;">
		  <a class="navbar-brand" href="index.php">Salomon</a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#first" aria-controls="first" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>

		  <div class="collapse navbar-collapse" id="first">
		    <ul class="navbar-nav mr-auto">
				<style media="screen">
					.font:hover{
						background:#53534F;
						border: 1px solid #1161BO;
						border-radius: 4px;
						color:#EAEAE4 !important;
					}
				</style>
				<li class="nav-item"><strong> <a class="nav-link active font" href="status.php">Status</a></strong> </li>
		        <li class="nav-item"><strong> <a class="nav-link active font" href="problem.php">Problemas</a></strong> </li>
		        <li class="nav-item"><strong> <a class="nav-link active font" href="contest.php">Competencia</a></strong> </li>
		        <!--<li class="nav-item"> <a class="nav-link text-primary" href="ranklist.php">Ranklist</a> </li>-->

				<li class="nav-item"><strong> <a class="nav-link active font" href="faq.php">Faq</a></strong> </li>

		    </ul>
		    <form class="form-inline my-2 my-lg-0">

			  <button type="button" class="btn btn-outline-primary my-2 my-sm-0 mr-4" data-toggle="modal"data-target="#loginModal" id="buttonlogin "name="buttonlogin">Login</button>

			  <button type="button" class="btn btn-outline-success my-2 my-sm-0" data-toggle="modal"data-target="#registerModal"name="button">Register</button>

		    </form>
		  </div>


		  <!--PARA LOGIN-->



            <div class="modal fade" role="dialog" id="loginModal">
                <div class="modal-dialog">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h3 class="modal-title">Login</h3>
                      <button type="button" class="close" data-dismiss="modal" name="bu">&times;</button>
                    </div>

                    <div class="modal-body">

                      <div class="from-group">
                        <input type="text" name="name" id="name" class="form-control" value="" placeholder="Username or Email">
                      </div>

                      <br>
                      <div class="from-group">
                        <input type="password" name="password" id="password" class="form-control" value="" placeholder="Password">
                      </div>

                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-warning" id="forpassword" name="forpassword">Forget Password</button>
                      <button type="button" class="mx-5 btn btn-danger" data-dismiss="modal" name="cancel">Cancel</button>
                      <button type="submit" class="btn btn-success" id="login_button" name="login_button">Sign in</button>
                    </div>

                  </div>
                </div>

            </div>

            <!-- para registro -->

            <div class="modal fade" role="dialog" id="registerModal">
				<?php
				//DBContestInfo retorna la informacion de la competencia en caso no retorna null

				$n=DBUserNumberMax();
				?>
                <div class="modal-dialog">

                  <div class="modal-content">

                    <div class="modal-header">
                      <h3 class="modal-title">Register</h3>
                      <button type="button" class="close" data-dismiss="modal" name="bu">&times;</button>
                    </div>

                    <div class="modal-body">
						<!--<input type=hidden name="usersitenumber" id="usersitenumber" <?php //echo "value=\"" . $usite . "\""; ?> />-->
						<input type=hidden name="usernumber" id="usernumber" value="<?php echo $n; ?>" />
						<div class="from-group">
  						  	<input type="text" name="username" id="username" class="form-control" placeholder="Nombre de usuario" maxlength="20" />
                        </div>
						<br>

		  			  	<input type=hidden name="usertype" id="usertype" value="team" />
		  			  	<input type=hidden name="userenabled" id="userenabled" value="t" />
		  			  	<!--MultiLogins (los equipos locales deben establecerse en <b> No </b>):-->
		  			  	<input type=hidden name="usermultilogin" id="usermultilogin" value="t" />
						<div class="from-group">
							<input type="text" name="userfullname" id="userfullname" class="form-control" placeholder="Nombre completo"maxlength="200" />
						</div>
						<br>
						<!--PARA EMAIL-->
						<div class="from-group">
							<input type="text" name="useremail" id="useremail" class="form-control" placeholder="Email"maxlength="200" />
						</div>
						<br>
						<div class="from-group">
							<input type="text" name="userdesc" id="userdesc" class="form-control" placeholder="Universidad"maxlength="300" />
						</div>
						<br>
						<input type=hidden name="userip" id="userip" value="" />
						<div class="from-group">
							<input type="password" name="passwordn1" id="passwordn1" class="form-control" value="" placeholder="Password" size="20" maxlength="200" />
						</div>
						<br>
						<div class="from-group">
							<input type="password" name="passwordn2" id="passwordn2" class="form-control" value="" size="20" placeholder="Repetir password" maxlength="200" />
						</div>
						<input type=hidden name="changepass" id="changepass" value="t" />


                    </div>

                    <div class="modal-footer">

                      	<button type="button" class="mx-5 btn btn-danger" data-dismiss="modal" name="cancel_register">Cancel</button>
                      	<button type="submit" class="btn btn-success" id="register_button" name="register_button">Register</button>
                    </div>

                  </div>

                </div>
            </div>


		</nav>
