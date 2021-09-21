<?php
session_start();
require_once('../db.php');
require_once('../globals.php');

if(isset($_POST["username"]) && isset($_POST["userfullname"]) && isset($_POST["userdesc"]) &&
    isset($_POST["passwordo"]) && isset($_POST["passwordn"])) {
    //funcion para mofigicar algunos caracteres especiales para htmlentities sin &
    $username = myhtmlspecialchars($_POST["username"]);
    $userfullname = myhtmlspecialchars($_POST["userfullname"]);
    $userdesc = myhtmlspecialchars($_POST["userdesc"]);
    $passwordo = $_POST["passwordo"];
    $passwordn = $_POST["passwordn"];//username es no moficable
    DBUserUpdate($_SESSION["usertable"]["usernumber"],
            $_SESSION["usertable"]["username"],
            $userfullname,
            $userdesc,
            $passwordo,
            $passwordn);//funcion para actulizar usuario y modificacion de password
    //ForceLoad("option.php");
    echo "Yes";
}else{
    echo "No";
}

?>
