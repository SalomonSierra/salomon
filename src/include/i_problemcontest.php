<?php
session_start();//para iniciar session_sta
require_once("../db.php");
if(isset($_POST["id"]) && is_numeric($_POST["id"]) && isset($_POST["data"]) &&
    isset($_POST["contest"]) && is_numeric($_POST["contest"])){
    $param = array();
    //$param['usernumber']=$_POST["user"];
    $param['contestnumber']=$_POST["contest"];
    $param['problemnumber']=$_POST["id"];
    $param['data']=$_POST["data"];

    if(DBNewProblemContest($param)!==false){
        echo "yes";
    }else{
        echo "no";
    }
}else{
    echo "no";
}


?>
