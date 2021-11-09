<?php
require('header.php');
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
	  <div class="carousel-item active" style="background-image: url('../images/informatica.jpg')">
		<div class="carousel-caption d-none d-md-block">
		  <h3>INFRAESTRUCTURA BLOQUE B</h3>
		  <p>UNSXX</p>
		</div>
	  </div>
	  <!-- Slide Two - Set the background image for this slide in the line below -->
	  <div class="carousel-item" style="background-image: url('../images/icpc.jpg')">
		<div class="carousel-caption d-none d-md-block">
		  <h3>ICPC</h3>
		  <p>MUNDIAL</p>
		</div>
	  </div>
	  <!-- Slide Three - Set the background image for this slide in the line below http://placehold.it/1900x1080-->
	  <div class="carousel-item" style="background-image: url('../images/bloqueA.jpg')">
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
            <img class="img-fluid" src="../images/salomon.png"/>
        </div>
        <div class="col-lg-8 mb-4">
            <h1 class="mt-4 mb-3 font-italic">SALOMON <span class="text-secondary">Team</span> </h1>
            Es un juez virtual para programacion competitiva, desarrollado como proyecto de grado para la carrera Ing. Informatica por el Unv. Fabian Sierra A.
            <iframe src="https://docs.google.com/forms/d/e/1FAIpQLScMISgpOFbfu76iKhGhHGR9mn74TNm9KMM6nqyserHh1IGd4Q/viewform?embedded=true" width="700" height="520" frameborder="0" marginheight="0" marginwidth="0">Cargando…</iframe>

        </div>
    </div>

    <a href="../doc/index.php">doc</a>


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
