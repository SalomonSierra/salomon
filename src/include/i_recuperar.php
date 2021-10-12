<?php
session_start();//para iniciar session_sta
require_once("../globals.php");
require_once("../db.php");

if(isset($_POST["name"])){

    //echo $_POST["name"]." ".$_POST["password"];
    if(function_exists("globalconf") && function_exists("sanitizeVariables")){

    		$name=$_POST["name"];

            $usertable=DBRecIn($name);

    		//$usertable=DBLogIn($name, $password);


            if(!$usertable){
    			//index.php
                echo "No existe el usuario";
    			//ForceLoad("index.php");
    		}else{
                $pas=$usertable["userpassword"];
                $link="http://localhost/salomon/recuperar.php?u=$name&p=$pas";//link para enviar a correo
                $fullname=$usertable["username"];
                $email=$usertable["useremail"];
                $mensaje="<p>Hola $fullname</p><br>".
                    "<p>Para restablecer tu contraseña haz <a href='$link'>click en este vínculo</a></p>".
                    "<br><p>Si tu no has hecho esta solicitud, ingora el presente mensaje</p>";
                /*<<<EMAIL
                <p>Hola </p>
                <p>Para restablecer tu contraseña haz <a href='$link'>click en este vínculo</a></p>
                <p>Si tu no has hecho esta solicitud, ingora el presente mensaje</p>
                EMAIL;*/
                $asunto="Restablecer Contraseña JUEZ SALOMON";
                $header="MIME-Version: 1.0"."\r\n";
                $header.="Content-type: text/html; charset=iso-8859-1"."\r\n";
                $header.= "From: juez7@salomon.com"."\r\n";
                $pos=strpos($email,"@");
                $ma=@mail($email,$asunto,$mensaje,$header);
                if($ma){
                    echo "Enviamos un link al correo ".substr($email,0,1)."***".substr($email,$pos-1,1)."".substr($email,$pos);
                }else{

                    echo "$ma no se pudo enviar al correo";
                }


    		}

    }else{
        echo "No se pueden cargar los archivos de configuración. Posible problema de permisos de archivos en el directorio salomon.";
    }

}else{
    echo "No";
}

?>
