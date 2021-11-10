<?php
ob_start();
header("Expires: ".gmdate("D, d M Y H:i:s")." GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

header("Content-Type: text/html; charset=utf-8");
session_start();
require_once('../version.php');

require_once('../globals.php');
require_once('../db.php');
$runteam='run.php';

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Team</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link rel="stylesheet" href="<?php echo  $loc; ?>/Css.php" type="text/css">
        <link rel="stylesheet" href="../bootstrap4/css/modern-business.css">
<?php
//funcion retorna true o false si no existe usertable en session false si es id diferente false
//si ho hay usertable en session  FALSE
//de usuario es true su multi llogion  TRUE
//si el ip son diferentes FALSE

if(!ValidSession()){
    InvalidSession("team/index.php");////funcion para expirar el session y registar 3= debug en logtable
    ForceLoad("../index.php");//index.php
}
$_SESSION["usertable"]["contestnumber"]=0;//para ejercicios generales
$_SESSION["usertable"]["private"]='';//para ejercicios generales
$_SESSION["usertable"]["count"]=0;//para ejercicios generales
if($_SESSION["usertable"]["usertype"] != "team"){
    IntrusionNotify("team/index.php");
    ForceLoad("../index.php");//index.php
}

?>
        <script language="javascript" src="../reload.js"></script>

        </head>
        <body class="p-0" onload="Comecar()" onunload="Parar()">


            <nav class="navbar navbar-expand-md navbar-dark bg-dark">
              <div class="container">


                <button class="navbar-toggler" data-toggle="collapse" data-target="#first" type="button" >
                  <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse"id="first">

                  <a class="navbar-brand"href="index.php">

                    Salomon
                  </a>
                  <ul class="navbar-nav mr-auto">
                      <style media="screen">
      					.font:hover{
      						background:#53534F;
      						border: 1px solid #1161BO;
      						border-radius: 4px;
      						color:#EAEAE4 !important;
      					}
      				</style>

                    <li class="nav-item font"><strong> <a class="nav-link active" href="problemset.php">Problemas</a></strong> </li>
                    <li class="nav-item font"><strong> <a class="nav-link active" href="status.php">Status</a></strong> </li><!---->
                    <li class="nav-item font"><strong> <a class="nav-link active" href="contest.php">Competencias</a> </strong></li><!--index.php-->
                    <li class="nav-item font"><strong> <a class="nav-link active" href="faq.php">Faq</a></strong> </li><!--index.php-->
                    <!--<li class="nav-item"> <a class="nav-link text-primary" href="user.php">Clarifications</a> </li>
                    <li class="nav-item"> <a class="nav-link text-primary" href="problem.php">Tasks</a> </li>
                    <li class="nav-item"> <a class="nav-link text-primary" href="../index1.php">Backups</a> </li>-->
                    <!--
                    <li class="nav-item"> <a class="nav-link text-primary" href="option.php">Options</a> </li>
                    -->


                  </ul>

                </div>

                <ul class="navbar-nav d-inline-block">
                    <li class="nav-item d-inline-block dropdown">
                        <!--para username-->
                        <a class="btn btn-outline-primary dropdown-toggle mx-1" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION["usertable"]["userfullname"]; ?></a>
                        <!--nosotros estamos utilizando directo en aqui para update po ahora no...:-->
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a href="" class="dropdown-item" data-toggle="modal" data-target="#updateModal">Update</a>
                            <a href="#" class="dropdown-item">Message</a>
                        </div>
                    </li>

                    <li class="nav-item d-inline-block"> <a class="btn btn-outline-primary" href="../index.php">Logout</a> </li>

                </ul>
            </div>
        </nav>

        <!--PARA UPDATE-->
        <?php
        include '../optionlower.php';
        ?>
