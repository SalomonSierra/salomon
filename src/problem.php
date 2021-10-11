<?php

require('header.php');

?>
<br>
<ol class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="index.php">Inicio</a>
	</li>
	<li class="breadcrumb-item active">Problemas</li>
</ol>
<br>
<table class="table table-bordered table-hover">
	<thead>
		<tr>
			<th scope="col">Nombre</th>
			<th scope="col">Nombre Base</th>
			<th scope="col">Titulo</th>
			<th scope="col">Setter</th>
			<th scope="col">Archivo Descripcion</th>
			<th scope="col">AC</th>
			<th scope="col">Envios</th>

		</tr>
	</thead>
	<tbody>

<?php
//$prob = DBGetProblems($_GET["contest"]);
$prob = DBGetFullProblemData(true);
for ($i=0; $i<count($prob); $i++) {
	$prun=DBRunProblem($prob[$i]["number"]);
  echo " <tr>\n";
//  echo "  <td nowrap>" . $prob[$i]["number"] . "</td>\n";
  echo "  <td>" . $prob[$i]["number"];
  if($prob[$i]["color"] != "")
          echo " <img alt=\"".$prob[$i]["colorname"]."\" width=\"20\" ".
			  "src=\"" . balloonurl($prob[$i]["color"]) ."\" />\n";
  echo "</td>\n";
  echo "  <td>" . $prob[$i]["basefilename"] . "&nbsp;</td>\n";
  //$fabian="fabian";
  echo "  <td>" . $prob[$i]["fullname"] . "&nbsp;</td>\n";
  //para setter
  $userinfo=DBUserInfo($prob[$i]["user"]);
  echo "  <td>".$userinfo["username"]."</td>";
  if (isset($prob[$i]["descoid"]) && $prob[$i]["descoid"] != null && isset($prob[$i]["descfilename"])) {
    echo "  <td><a href=\"#\" class=\"btn btn-primary\" style=\"font-weight:bold\" onClick=\"window.open('filewindow2.php?".filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"])."', 'Ver - PROBLEMA', 'width=680,height=600,scrollbars=yes,resizable=yes')\">Ver Problema</a></td>\n";
  }
  else
    echo "  <td>no description file available</td>\n";
  echo "  <td>" . $prun["ac"] . "</td>\n";

  if (isset($prob[$i]["descoid"]) && $prob[$i]["descoid"] != null && isset($prob[$i]["descfilename"])) {
    //echo "  <td><a href=\"../filedownload.php?" . filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"]) .
	//	"\">" . basename($prob[$i]["descfilename"]) . "</a>&nbsp;&nbsp;<a href=\"#\" class=\"btn btn-primary\" style=\"font-weight:bold\" onClick=\"window.open('../filewindow0.php?".filedownload($prob[$i]["descoid"], $prob[$i]["descfilename"])."', 'Ver - PROBLEMA', 'width=680,height=600,scrollbars=yes,resizable=yes')\">Ver Problema</a></td>\n";
		echo "  <td>".$prun["all"]."&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<button type=\"button\" class=\"btn btn-success\" data-toggle=\"modal\"data-target=\"#loginModal\"name=\"problem2_button\">Enviar</button>";
		echo " </td>\n";
  }
  else
    echo "  <td>".$prun["all"]."</td>\n";

  echo " </tr>\n";

}
echo "</tbody></table>";
if (count($prob) == 0) echo "<br><center><b><font color=\"#ff0000\">NO HAY PROBLEMAS DISPONIBLES TODAVIA</font></b></center>";

?>





<!--PIE DE PAGINA......PAGINA........PAGINA-->
		<br>
		<font size="-5">Desarrollado por FabianS7</font>



		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

	    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

	</body>
</html>

<script language="JavaScript" src="sha256.js"></script>
<script language="JavaScript" src="hex.js"></script>

<script>
//jquery
$(document).ready(function(){
    $('#login_button').click(function(){
		var userHASH,passHASH;
		userHASH=$('#name').val();
		//retorna una cadena hexadecimal sin procesar hash
		passHASH=js_myhash(js_myhash($('#password').val())+'<?php echo session_id(); ?>');
		//document.form1.name.value= '';
		//document.form1.password.value='																';
		//a index en nuestro caso
		//document.location = 'index.php?name='+userHASH+'&password='+passHASH;//envia datos

         if(userHASH != '' && passHASH != ''){

              $.ajax({

                   url:"include/i_header.php",
                   method:"POST",
                   data: {name:userHASH, password:passHASH},
                   success:function(data)
                   {
					   if (data == 'Yes') {
						   	$('#loginModal').hide();
					   		document.location = 'index.php?name='+userHASH+'&password='+passHASH;
					   }else {

							if(data.indexOf('Incorrect password.') !== -1) {
							   // do stuff with the string
							   alert('Incorrect password.');
						   }else if(data.indexOf('Violation (Invalid IP:') !== -1){
							   alert('Violation (Invalid IP:) Admin Warned.');
						   }else{
							   alert(data);
						   }
					   }

                   }
              });
         }
         else
         {
              alert("Both Fields are required");
         }
    });


    //register
    $('#register_button').click(function(){
		 //var usersitenumber = $('#usersitenumber').val();
		 var usernumber = $('#usernumber').val();
		 var username = $('#username').val();

		 var usertype = $('#usertype').val();
		 var userenabled = $('#userenabled').val();
		 var usermultilogin = $('#usermultilogin').val();
		 var userfullname = $('#userfullname').val();
		 var useremail = $('#useremail').val();
		 var userdesc = $('#userdesc').val();
		 var userip = $('#userip').val();

		 var passwordn1 = bighexsoma(js_myhash($('#passwordn1').val()),js_myhash('fabians7'));
		 var passwordn2 = bighexsoma(js_myhash($('#passwordn2').val()),js_myhash('fabians7'));

		 var passHASH=js_myhash(js_myhash($('#passwordn1').val())+'<?php echo session_id(); ?>');
		 var userHASH=$('#username').val();

		 var changepass = $('#changepass').val();

         if(username != '' && userfullname != '' && useremail != '' && userdesc != '' && passwordn1 != '' && passwordn2 != ''){
             //alert("entro");
			 var validar= /^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/;
			 if(validar.test(useremail)){
				 if (confirm("Confirm?")) {

    				 $.ajax({

    					  url:"include/i_header.php",
    					  method:"POST",
    					  data: {usernumber:usernumber, username:username, usertype:usertype, userenabled:userenabled, usermultilogin:usermultilogin, userfullname:userfullname, useremail:useremail, userdesc:userdesc, userip:userip, passwordn1:passwordn1, passwordn2:passwordn2, changepass:changepass},

    					  success:function(data)
    					  {
    						   if(data == 'Yes'){
    							   $('#registerModal').hide();
    							   $.ajax({

    				                    url:"include/i_header.php",
    				                    method:"POST",
    				                    data: {name:userHASH, password:passHASH},
    				                    success:function(data)
    				                    {

    				 					   if (data == 'Yes') {
    				 						   	$('#loginModal').hide();
    				 					   		document.location = 'index.php?name='+userHASH+'&password='+passHASH;
    				 					   }else {
    				 					   		alert(data);
    				 					   }

    				                    }
    				               });


    						   }else{

    							   if(data.indexOf('username already in use') !== -1) {
    								  // do stuff with the string
    								  alert('username already in use');
    							   }
    							   if(data.indexOf('Passwords don\'t match.') !== -1) {
    								  // do stuff with the string
    								  alert('Passwords don\'t match.');
    							   }
    							   if(data.indexOf('Problema de actualizacion para el usuario') !== -1) {
    								  // do stuff with the string
    								  alert('The User or Email is already in Use');
    							   }
    							   if(data.indexOf('Contest no active') !== -1) {
    								  // do stuff with the string
    								  alert('Contest no active');
    								  $('#registerModal').hide();
    								  location.reload();
    							   }
    						   }

    					  }
    				 });

    		     }else{
    				 $('#registerModal').hide();
    				 location.reload();
    			 }
	 		 }else{
	 			alert("The email is not valid");
	 		 }





         }
         else
         {
              alert("Both Fields are required");
         }
    });

	//forpassword
	$('#forpassword').click(function(){
		var userHASH;
		userHASH=$('#name').val();

		 if(userHASH != ''){

			  $.ajax({

				   url:"include/i_recuperar.php",
				   method:"POST",
				   data: {name:userHASH},
				   success:function(data)
				   {
					   alert(data);
					   
				   }
			  });
		 }
		 else
		 {
			  alert("Please fill in your username or email");
		 }
	});

});


</script>
