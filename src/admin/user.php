<?php

require('header.php');

if (isset($_GET["user"]) && is_numeric($_GET["user"]) &&
    isset($_GET["logout"]) && $_GET["logout"] == 1) {
    //realizar un update al la tabla usertable campos userssion='' usersessionextra='' y otros si es
    //admin verifica el tiempo y realizar un
    //update si es menor a -600 el clocktime y limpia la carpeta problemtmp
	DBLogOut($_GET["user"]);
    ForceLoad("user.php");
}

if (isset($_GET["usernumber"]) &&
    is_numeric($_GET["usernumber"]) && isset($_GET["confirmation"])) {
    if($_GET["confirmation"]=="active"){
        if(!enabledUser($_GET["usernumber"]))
            MSGError("no se puede activar el usuario");
    }
    //DBDeleteUser si es el mismo retorna false, hace update a usertable userenabled='f' y algunos
    //campos si existe en runtable status a deleted nueva tarea old tasktable answertable problemtable
    if($_GET["confirmation"]=="delete"){
        //para eliminar el usuario
        if (!DBDeleteUser($_GET["usernumber"]))
    		MSGError("El usuario no pudo eliminar.");//La usuario no pudo ser eliminada.
    }
    ForceLoad("user.php");
}

//DBContestInfo retorna la informacion de la competencia en caso no retorna null
if(($ct = DBContestInfo(0)) == null)
        ForceLoad("../index.php");//index.php


if (isset($_POST["username"]) && isset($_POST["userfullname"]) && isset($_POST["userdesc"]) && isset($_POST["userip"]) &&
    isset($_POST["usernumber"]) && isset($_POST["userenabled"]) &&
    isset($_POST["usermultilogin"]) && isset($_POST["usertype"]) && isset($_POST["confirmation"]) &&
    isset($_POST["passwordn1"]) && isset($_POST["passwordn2"]) && isset($_POST["passwordo"]) && $_POST["confirmation"] == "confirm") {
	$param['user'] = htmlspecialchars($_POST["usernumber"]);

	$param['username'] = htmlspecialchars($_POST["username"]);

	$param['enabled'] = htmlspecialchars($_POST["userenabled"]);
	$param['multilogin'] = htmlspecialchars($_POST["usermultilogin"]);
	$param['userfull'] = htmlspecialchars($_POST["userfullname"]);
	$param['userdesc'] = htmlspecialchars($_POST["userdesc"]);
	$param['type'] = htmlspecialchars($_POST["usertype"]);
	$param['permitip'] = htmlspecialchars($_POST["userip"]);

	$param['changepass']='t';
	if(isset($_POST['changepass']) && $_POST['changepass'] != 't') $param['changepass']='f';

    $passcheck = $_POST["passwordo"];
    ////esta funcion retorna el registro de usuario y tambien si cambio o no hashpass = true
    $a = DBUserInfo($_SESSION["usertable"]["usernumber"], null, false);
    if(myhash($a['userpassword'] . session_id()) != $passcheck) {
        MSGError('Admin password is incorrect');
    } else {
        if ($_POST["passwordn1"] == $_POST["passwordn2"]) {
            //si son iguales retorna 0 si no retorna sub en resto de dos str.
            //pasa nuevopass1 datapass2
            $param['pass'] = bighexsub($_POST["passwordn1"],$a['userpassword']);
            while(strlen($param['pass']) < strlen($a['userpassword']))
                $param['pass'] = '0' . $param['pass'];
            if($param['user'] != 0)
                DBNewUser($param);//funcion para actulizar o insertar un nuevo usuario segun los datos que pasa
        } else MSGError ("Passwords don't match.");
    }
    ForceLoad("user.php");
}
//---para importacion de datos

//DBAllUserInfo seleccion la todos los usuario de la base de datos si pasa sitio de ese
//if $msite

$usr = DBAllUserInfo();

//else
//	$usr = DBAllUserInfo($_SESSION["usertable"]["contestnumber"],$_SESSION["usertable"]["usersitenumber"]);
?>
<!--PARA CREAR USUARIO-->

<a href="user.php#form_user" class="btn btn-success">Crear Usuario</a>

  <script language="javascript">
    function conf2(url) {
      if (confirm("Are you sure?")) {
        document.location=url;
      } else {
        document.location='user.php';
      }
    }
  </script>
<br>
<table class="table table-sm table-hover">
    <thead>
        <tr>
            <th scope="col">User #</th>
            <th scope="col">Username</th>
            <th scope="col">Type</th>
            <th scope="col">IP</th>
            <th scope="col">LastLogin</th>
            <th scope="col">LastLogout</th>
            <th scope="col">Enabled</th>
            <th scope="col">Multi</th>
            <th scope="col">Fullname</th>
            <th scope="col">Description</th>
            <th scope="col">Acciones</th>
        </tr>
    </thead>
    <tbody>


<?php
for ($i=0; $i < count($usr); $i++) {
      echo " <tr>\n";
      if($usr[$i]["usernumber"] != 0)
	      echo "  <td><a href=\"user.php?user=" .
		  $usr[$i]["usernumber"] . "\">" . $usr[$i]["usernumber"] . "</a>";
      else
	     echo "  <td>" . $usr[$i]["usernumber"];//para el admin
      if($usr[$i]['userenabled'] != 't' && $usr[$i]['userlastlogin'] < 1) echo "(inactive)";
      echo "</td>\n";

      //echo "  <td>" . $usr[$i]["usersitenumber"] . "</td>\n";
      echo "  <td>" . $usr[$i]["username"] . "&nbsp;</td>\n";

      echo "  <td>" . $usr[$i]["usertype"] . "&nbsp;</td>\n";
      if ($usr[$i]["userpermitip"]!="")
        echo "  <td>" . $usr[$i]["userpermitip"] . "*&nbsp;</td>\n";
      else
        echo "  <td>" . $usr[$i]["userip"] . "&nbsp;</td>\n";
      if ($usr[$i]["userlastlogin"] < 1)
        echo "  <td>never</td>\n";
      else
        echo "  <td>" . dateconv($usr[$i]["userlastlogin"]) . "</td>\n";
      if ($usr[$i]["usersession"] != "")
        echo "  <td><a href=\"javascript: conf2('user.php?logout=1&user=" .
             $usr[$i]["usernumber"] . "')\">Force Logout</a></td>\n";
      else {
        if ($usr[$i]["userlastlogout"] < 1)
          echo "  <td>never</td>\n";
        else//dateconv date — Dar formato a la fecha/hora del parametro pasado
          echo "  <td>" . dateconv($usr[$i]["userlastlogout"]) . "</td>\n";
      }
      if ($usr[$i]["userenabled"] == "t")
        echo "  <td>Yes</td>\n";
      else
        echo "  <td>No</td>\n";
      if ($usr[$i]["usermultilogin"] == "t")
        echo "  <td>Yes</td>\n";
      else
        echo "  <td>No</td>\n";
      echo "  <td>" . $usr[$i]["userfullname"] . "&nbsp;</td>\n";
      echo "  <td>" . $usr[$i]["userdesc"] . "&nbsp;</td>\n";

       if($usr[$i]["usernumber"] !=0 ){

            if($usr[$i]['userenabled'] != 't' && $usr[$i]['userlastlogin'] < 1){
                 echo " <td><div class=\"btn-group btn-group-toggle\" data-toggle=\"buttons\"><a onClick=\"conf7(".$usr[$i]["usernumber"].")\"" .
                       "')\" class=\"btn btn-warning\">Activar</a>";
                 echo "<a class=\"btn btn-secondary\" name=\"\" style=\"pointer-events: none; cursor: default; \">Actualizar</a></div>";
            }else{
                 echo " <td><div class=\"btn-group btn-group-toggle\" data-toggle=\"buttons\"><a " .
                     "')\" class=\"btn btn-danger\" onClick=\"conf4(".$usr[$i]["usernumber"].")\">Eliminar</a>";
                 echo "<a href=\"user.php?user=" .
        		  $usr[$i]["usernumber"] . "#form_user\" class=\"btn btn-primary\" name=\"\" >Actualizar</a></div>";
            }
            echo "<script language=\"javascript\">    function conf4(user) {\n";
            echo "      if (confirm('ADVERTENCIA: eliminar un usuario eliminará por completo TODO lo relacionado con él (incluidas las ejecuciones, aclaraciones, etc.).?')) {\n";
            //echo "            document.location='https://www.google.com/?hl=es'\n";
            echo "            document.location='user.php?usernumber='+user+'&confirmation=delete';\n";
            echo "      }\n";
            echo "    }</script>\n";
            echo "<script language=\"javascript\">    function conf7(user) {\n";
            echo "      if (confirm('ESTAS SEGURO DE ACTIVAR USUARIO?')) {\n";
            //echo "            document.location='https://www.google.com/?hl=es'\n";
            echo "            document.location='user.php?usernumber='+user+'&confirmation=active';\n";
            echo "      }\n";
            echo "    }</script>\n";
          //echo "  <td><a href=\"user.php?site=" . $usr[$i]["usersitenumber"] . "&user=" .
 		  //$usr[$i]["usernumber"] . "#form_user\">" . "ACTUALIZAR" . "</a>";

       }else{
 	      echo "  <td>" . $usr[$i]["usernumber"];//para el admin
       }
       //f($usr[$i]['userenabled'] != 't' && $usr[$i]['userlastlogin'] < 1) echo "(inactive)";
          echo "</td>\n";



      echo "</tr>";
}
echo "</tbody></table>\n";

unset($u);//pero aun no existe para seguridad
if (isset($_GET["user"]) && is_numeric($_GET["user"]))
  $u = DBUserInfo($_GET["user"]);
////esta funcion retorna el registro de usuario y tambien si cambio o no hashpass = true
?>

<script language="JavaScript" src="../sha256.js"></script>
<script language="JavaScript" src="../hex.js"></script>
<script language="JavaScript">
function computeHASH()
{
	document.form3.passwordn1.value = bighexsoma(js_myhash(document.form3.passwordn1.value),js_myhash(document.form3.passwordo.value));
	document.form3.passwordn2.value = bighexsoma(js_myhash(document.form3.passwordn2.value),js_myhash(document.form3.passwordo.value));
	document.form3.passwordo.value = js_myhash(js_myhash(document.form3.passwordo.value)+'<?php echo session_id(); ?>');
//	document.form3.passwordn1.value = js_myhash(document.form3.passwordn1.value);
//	document.form3.passwordn2.value = js_myhash(document.form3.passwordn2.value);
}
</script>
<!--Clicking on a user number will bring the user data for edition.<br>
To import the users, just fill in the import file field.<br>
The file must be in the format defined in the admin's manual.-->
<!--<br><br><center><b>Al hacer clic en un número de usuario, se mostrarán los datos del usuario para su edición. <br>
Para importar los usuarios, simplemente complete el campo del archivo de importación. <br>
El archivo debe estar en el formato definido en el manual de administración.</b></center>
-->
<div class="container">


    <!--FORMULARIO IMPORT OTRO FORMULARIO-->

      <br><br>
      <center>
    <!--To create/edit one user, enter the data below.<br>
    Note that any changes will overwrite the already defined data.<br>
    (Specially care if you use a user number that is already existent.)-->
    <a id="form_user"></a>
    <!--<b>Para crear / editar un usuario, ingrese los datos a continuación. <br>
    Tenga en cuenta que cualquier cambio sobrescribirá los datos ya definidos. <br>
    (Tenga especial cuidado si usa un número de usuario que ya existe).<br>
    <br>-->
    </b></center>

    <form name="form3" action="user.php" method="post">
      <input type=hidden name="confirmation" value="noconfirm" />
      <script language="javascript">
        function conf3() {
          computeHASH();
          if (confirm("Confirm?")) {
            document.form3.confirmation.value='confirm';
          }
        }

        function conf5() {
          document.form3.confirmation.value='noconfirm';
        }
      </script>

      <div class="form-group row">
          <label for="usernumber" class="col-sm-4 col-form-label">User Number:</label>
          <div class="col-sm-8">
              <input type="text" name="usernumber" id="usernumber" class="form-control" value="<?php if(isset($u)) echo $u["usernumber"]; ?>" maxlength="20" />
          </div>
      </div>
      <div class="form-group row">
          <label for="usernumber" class="col-sm-4 col-form-label">Username:</label>
          <div class="col-sm-8">
              <input type="text" name="username" id="username" class="form-control" value="<?php if(isset($u)) echo $u["username"]; ?>" maxlength="20" />
          </div>
      </div>
      <!--ICPC ID-->
      <div class="form-group row">
          <label for="" class="col-sm-4 col-form-label">Type:</label>
          <div class="col-sm-8">
                <select name="usertype">
          		<option <?php if(!isset($u) || $u["usertype"] == "team") echo "selected"; ?> value="team">Team</option>

          		<option <?php if(isset($u)) if($u["usertype"] == "coach") echo "selected"; ?> value="coach">Coach</option>


          		</select>
        </div>
      </div>
      <div class="form-group row">
          <label for="" class="col-sm-4 col-form-label">Enabled:</label>
          <div class="col-sm-8">
              <select name="userenabled">
      		  <option <?php if(!isset($u) || $u["userenabled"] != "f") echo "selected"; ?> value="t">Yes</option>
      		  <option <?php if(isset($u) && $u["userenabled"] == "f") echo "selected"; ?> value="f">No</option>
      		  </select>
        </div>
      </div>
      <!--MultiLogins (los equipos locales deben establecerse en <b> No </b>):-->
      <div class="form-group row">
          <label for="" class="col-sm-4 col-form-label">MultiLogins (local teams should be set to <b>No</b>):</label>
          <div class="col-sm-8">
              <select name="usermultilogin">
      		<option <?php if(isset($u) && $u["usermultilogin"] == "t") echo "selected"; ?> value="t">Yes</option>
      		<option <?php if(!isset($u) || $u["usermultilogin"] != "t") echo "selected"; ?> value="f">No</option>
      		</select>
        </div>
      </div>
      <div class="form-group row">
          <label for="userfullname" class="col-sm-4 col-form-label">User Full Name:</label>
          <div class="col-sm-8">
              <input type="text" name="userfullname" id="userfullname" class="form-control" value="<?php if(isset($u)) echo $u["userfullname"]; ?>" maxlength="200" />
          </div>
      </div>
      <div class="form-group row">
          <label for="userdesc" class="col-sm-4 col-form-label">User Description:</label>
          <div class="col-sm-8">
              <input type="text" name="userdesc" id="userdesc" class="form-control" value="<?php if(isset($u)) {
              if($u['usershortinstitution']!='')
                  echo '[' . $u['usershortinstitution'] .']';
              if($u['userflag']!='') {
                  echo '[' . $u['userflag'];
                  if($u['usersitename']!='') echo ',' . $u['usersitename'];
                  echo ']';
              }
              echo $u["userdesc"]; } ?>" maxlength="300" />
          </div>
      </div>
      <div class="form-group row">
          <label for="userip" class="col-sm-4 col-form-label">User IP:</label>
          <div class="col-sm-8">
              <input type="text" name="userip" id="userip" class="form-control" value="<?php if(isset($u)) echo $u["userpermitip"]; ?>" size="20" maxlength="20" />
          </div>
      </div>
      <div class="form-group row">
          <label for="passwordn1" class="col-sm-4 col-form-label">Password:</label>
          <div class="col-sm-8">
              <input type="password" name="passwordn1" id="passwordn1" class="form-control" value="" size="20" maxlength="200" />
          </div>
      </div>
      <div class="form-group row">
          <label for="passwordn2" class="col-sm-4 col-form-label">Retype Password:</label>
          <div class="col-sm-8">
              <input type="password" name="passwordn2" id="passwordn2" class="form-control" value="" size="20" maxlength="200" />
          </div>
      </div>
      <div class="form-group row">
          <label for="" class="col-sm-4 col-form-label">Allow password change:</label>
          <div class="col-sm-8">
              <select name="changepass">
      		  <option <?php if(isset($u) && $u["changepassword"]) echo "selected"; ?> value="t">Yes</option>
      		  <option <?php if(!isset($u) || !$u["changepassword"]) echo "selected"; ?> value="f">No</option>
      		  </select>
        </div>
      </div>
      <div class="form-group row">
          <label for="passwordo" class="col-sm-4 col-form-label">Admin (this user) Password:</label>
          <div class="col-sm-8">
              <input type="password" name="passwordo" id="passwordo" class="form-control" value="" size="20" maxlength="200" />
        </div>
      </div>

      <div class="form-group row">
          <input type="submit" class="btn btn-primary"name="Submit" value="Send" onClick="conf3()">&nbsp;

          <input type="submit" class="btn btn-primary"name="Cancel" value="Cancel" onClick="conf5()">

      </div>
    </form>
</div>
<!--PIE DE PAGINA......PAGINA........PAGINA-->
    <?php include '../footnote.php'; ?>



    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

</body>
</html>
<script language="JavaScript" src="../sha256.js"></script>
<script language="JavaScript" src="../hex.js"></script>
<script>

$(document).ready(function(){

     //update
     $('#update_button').click(function(){
		 var username,userdesc,userfull,passHASHo,passHASHn;
		 if($('#passwordn1').val() != $('#passwordn2').val()){
			 alert('password confirmacion debe ser igual');
		 }else{
			 if($('#passwordn1').val() == $('#passwordo').val()){
				 alert('password nuevo debe ser diferente al anterior');
			 }else{
				 username = $('#username').val();
				 userdesc = $('#userdesc').val();
				 userfull = $('#userfull').val();
				 passHASHo = js_myhash(js_myhash($('#passwordo').val())+'<?php echo session_id(); ?>');
				 passHASHn = bighexsoma(js_myhash($('#passwordn2').val()),js_myhash($('#passwordo').val()));
				 $('#passwordn1').val('                                                     ');
				 $('#passwordn2').val('                                                     ');
				 $('#passwordo').val('                                                     ');

				 $.ajax({

						  url:"../include/i_optionlower.php",
						  method:"POST",
						  data: {username:username, userdesc:userdesc, userfullname:userfull, passwordo:passHASHo, passwordn:passHASHn},

						  success:function(data)
						  {
							   //alert(data);
							   if(data.indexOf('Data updated.') !== -1)
							   {
									alert("Data updated.");
									$('#updateModal').hide();
									location.reload();
							   }
							   else
							   {
								   if (data.indexOf('Incorrect password')!== -1) {
									   alert("Incorrect password");

									   //location.href="../indexs.php";
								   }else{
									   alert(data);
								   }

							   }

						  }
				 });




			 }
		 }



     });


});
</script>
