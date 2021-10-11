<?php
include 'header.php';
// http://localhost/salomon/recuperar.php?u=prueba&p=173af653133d964edfc16cafe0aba33c8f500a07f3ba3f81943916910c257705
if(isset($_POST["name"]) && isset($_POST["passwordn1"]) && isset($_POST["confirmation"]) && $_POST["confirmation"]=="confirm"){

    $pass= bighexsub($_POST["passwordn1"],myhash('fabians7'));

    while(strlen($pass) < strlen(myhash('fabians7')))
        $pass = '0' . $pass;
    $verf=DBResPassword($_POST["name"],$pass);//funcion para recuperar contraseña
    if($verf){
        MSGError("Se restableció tu contraseña correctamente, inicia sesion");

    }else{
        MSGError("Error al restablecer tu contraseña");
    }
    echo "Yes";//aceptado
    ForceLoad("index.php");


}

if(isset($_GET["u"])&&isset($_GET["p"])){
    $u=DBUserInfoName($_GET["u"]);
    $pass=myhash($u["userpassword"]);
    if($pass==$_GET["p"]){
?>



	<div class="container">
        <br><br><br>
        <div class="col-6">
            <form name="form3" action="recuperar.php" method="post">
              <input type=hidden name="confirmation" value="noconfirm" />
              <input type=hidden name="name" value="<?php echo $_GET["u"]; ?>" />

              <script language="javascript">
                function conf3() {

                  document.form3.passwordn1.value = bighexsoma(js_myhash(document.form3.passwordn1.value),js_myhash('fabians7'));
         		  document.form3.passwordn2.value = bighexsoma(js_myhash(document.form3.passwordn2.value),js_myhash('fabians7'));
                  if(document.form3.passwordn2.value==document.form3.passwordn1.value){
                      if (confirm("Confirm?")) {
                          document.form3.confirmation.value='confirm';

                      }
                  }else{
                      alert("Passwords don't match.");
                  }

                }
                function conf5() {
                  document.form3.confirmation.value='noconfirm';
                }
              </script>

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
                  <input type="submit" class="btn btn-primary"name="Submit" value="Send" onClick="conf3()">&nbsp;

                  <input type="submit" class="btn btn-primary"name="Cancel" value="Cancel" onClick="conf5()">

              </div>
            </form>
        </div>


	</div>

<?php
    }else{
        echo "Error al restablecer Contraseña";
    }
}else{
    echo "Error al restablecer Contraseña no existen parametros";
}
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
