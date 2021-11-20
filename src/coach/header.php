<?php
ob_start();
header("Expires: ".gmdate("D, d M Y H:i:s")." GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=utf-8");
session_start();

if(!isset($_POST['noflush']))
    ob_end_flush();
//$loc=$_SESSION['loc'];
//$locr=$_SESSION['locr'];
$loc=$locr="..";
$runphp="run.php";
$runeditphp="runedit.php";

require_once("$locr/globals.php");
require_once("$locr/db.php");
if(!isset($_POST['noflush'])){
    require_once("$locr/version.php");
    //html
}

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Coach</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link rel="stylesheet" href="<?php echo  $loc; ?>/Css.php" type="text/css">
<?php
//funcion retorna true o false si no existe usertable en session false si es id diferente false
//si ho hay usertable en session  FALSE
//de usuario es true su multi llogion  TRUE
//si el ip son diferentes FALSE
if(!ValidSession()){
    //MSGError('Aqui Cague');
    InvalidSession("coach/index.php");////funcion para expirar el session y registar 3= debug en logtable
    ForceLoad("$loc/index.php");//index.php
}
$_SESSION["usertable"]["contestnumber"]=0;//para ejercicios generales

if($_SESSION["usertable"]["usertype"] != "coach"){
    IntrusionNotify("coach/index.php");
    ForceLoad("$loc/index.php");//index.php
}
/*if((isset($_GET["Submit1"]) && $_GET["Submit1"] == "Transfer") ||
    (isset($_GET["Submit3"]) && $_GET["Submit3"] == "Transfe scores")){
        echo "<meta http-equiv=\"refresh\" contest=\"60\"/>";
}*/
if(!isset($_POST['noflush'])){
?>
    </head>
    <body>

        <nav class="navbar navbar-expand-md navbar-light bg-dark">
          <div class="container">


            <button class="navbar-toggler" data-toggle="collapse" data-target="#first" type="button" >
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse"id="first">

              <a class="navbar-brand text-primary"href="index.php">

                Salomon
              </a>
              <ul class="navbar-nav mr-auto">


                <li class="nav-item"> <a class="nav-link text-primary" href="contest.php">Competencia</a> </li>
                <li class="nav-item"> <a class="nav-link text-primary" href="status.php">Status</a> </li>
                <li class="nav-item"> <a class="nav-link text-primary" href="problemset.php">Problem set</a> </li>

                <!--
                <li class="nav-item"> <a class="nav-link text-primary" href="../index.php">Answers</a> </li>
                <li class="nav-item"> <a class="nav-link text-primary" href="../index.php">Misc</a> </li>
                <li class="nav-item"> <a class="nav-link text-primary" href="../index.php">Clarifications</a> </li>index.php-->




              </ul>

            </div>

            <ul class="navbar-nav d-inline-block">
                <li class="nav-item d-inline-block dropdown">
                    <a class="btn btn-outline-primary dropdown-toggle mx-1" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION["usertable"]["username"]; ?></a>
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
    <!--
    <nav class="navbar navbar-expand-md navbar-light bg-dark">
      <div class="container">


            <button class="navbar-toggler" data-toggle="collapse" data-target="#first" type="button" >
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse"id="first">

              <ul class="navbar-nav mr-auto">


                <li class="nav-item"> <a class="nav-link text-primary" href="contest.php">Tasks</a> </li>

                <li class="nav-item"> <a class="nav-link text-primary" href="../index.php">Logs</a> </li>
                <li class="nav-item"> <a class="nav-link text-primary" href="../index.php">Reports</a> </li>
                <li class="nav-item"> <a class="nav-link text-primary" href="../index.php">Backups</a> </li>


              </ul>

            </div>

        </div>
    </nav>-->
    <!--PARA UPDATE-->
    <?php
    include '../optionlower.php';
    ?>

<?php
}
?>
