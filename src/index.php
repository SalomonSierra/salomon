<?php
include 'header.php';
?>
<header>
  <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
	<ol class="carousel-indicators">
	  <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
	  <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
	  <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
	</ol>
	<div class="carousel-inner" role="listbox">
	  <!-- Slide One - Set the background image for this slide in the line below
	  .contenedor:hover .imagen {-webkit-transform:scale(1.3);transform:scale(1.3);}
.contenedor {overflow:hidden;}
-->
	  <div class="carousel-item active" style="background-image: url('images/informatica.jpg')">
		<div class="carousel-caption d-none d-md-block">
		  <h3>INFRAESTRUCTURA BLOQUE B</h3>
		  <p>UNSXX</p>
		</div>
	  </div>
	  <!-- Slide Two - Set the background image for this slide in the line below -->
	  <div class="carousel-item" style="background-image: url('images/icpc.jpg')">
		<div class="carousel-caption d-none d-md-block">
		  <h3>ICPC</h3>
		  <p>MUNDIAL</p>
		</div>
	  </div>
	  <!-- Slide Three - Set the background image for this slide in the line below http://placehold.it/1900x1080-->
	  <div class="carousel-item" style="background-image: url('images/bloqueA.jpg')">
		<div class="carousel-caption d-none d-md-block">
		  <h3>INGENIERIA INFORMATICA</h3>
		  <p>UNSXX</p>
		  <a href="#" class="btn btn-outline-warning">ver bibliografia</a>
		  <a href="https://www.facebook.com/Ingenieria-Informatica-UNSXX-754225731289017" class="btn btn-outline-primary">facebook</a>
		  <a href="#" class="btn btn-outline-primary">twiter</a>
		</div>
	  </div>
	</div>
	<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
	  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
	  <span class="sr-only">Previous</span>
	</a>
	<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
	  <span class="carousel-control-next-icon" aria-hidden="true"></span>
	  <span class="sr-only">Next</span>
	</a>
  </div>
</header>



	<div class="container">

		<br><br><br>
		<div class="row">
			<div class="col-lg-4 mb-4">
				<img class="img-fluid" src="images/salomon.png"/>
			</div>
			<div class="col-lg-8 mb-4">
				<h1 class="mt-4 mb-3 font-italic">JUEZ VIRTUAL SALOMON</h1>
				Es un juez virtual para programacion competitiva, desarrollado como proyecto de grado para la carrera Ing. Informatica por el Unv. Fabian Sierra A.

		        <iframe width="560" height="315" src="https://www.youtube.com/embed/L8SHEDxY3UE" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

			</div>

		</div>
		<!--<a href="../doc/index.php">doc</a>-->
		<iframe src="https://docs.google.com/forms/d/e/1FAIpQLScMISgpOFbfu76iKhGhHGR9mn74TNm9KMM6nqyserHh1IGd4Q/viewform?embedded=true" width="700" height="520" frameborder="0" marginheight="0" marginwidth="0">Cargando…</iframe>




	</div>


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
