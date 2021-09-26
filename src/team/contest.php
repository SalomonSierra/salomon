<?php

require('header.php');

?>
<!--
<br>

<ol class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="index.php">Inicio</a>
	</li>
	<li class="breadcrumb-item active">Competencias</li>
</ol>-->


<br>
<table class="table table-bordered table-hover">
	<thead>
		<tr class="d-flex">
			<th class="col-1" scope="col">ID</th>
			<th class="col-5" scope="col">Name</th>
			<th class="col-3" scope="col">Status</th>
			<th class="col-3" scope="col">Private</th>
		</tr>
	</thead>
	<tbody>

<?php
//$prob = DBGetProblemsGlobal($_SESSION["usertable"]["contestnumber"]);
$ct = DBContestInfoAll();//falta...
$ac=DBGetActiveContest();
for ($i=0; $i<count($ct); $i++) {
	list($clockstr,$clocktype)=siteclock2($ct[$i]["number"]);
	if($clocktype==-1000000000){
		echo " <tr class=\"d-flex table-secondary\">\n";
	}else{
		if($ct[$i]["private"]=='f'){
			echo " <tr class=\"d-flex table-success\">\n";
		}else{
			echo " <tr class=\"d-flex table-warning\">\n";
		}
	}
//  echo "  <td nowrap>" . $prob[$i]["number"] . "</td>\n";
  echo "  <td class=\"col-1\">" .$ct[$i]["number"]."</td>\n";
  echo "  <td class=\"col-5\"><a href=\"problem.php?contest=".$ct[$i]["number"]."\">" . $ct[$i]["name"] . "&nbsp;</a></td>\n";

  echo "  <td class=\"col-3\">" . $clockstr . "&nbsp;</td>\n";
  if($ct[$i]["private"]=='t'){
	  echo "  <td class=\"col-3 text-danger\">Privado</td>\n";
  }else{
	  echo "  <td class=\"col-3 text-success\">Publico</td>\n";
  }
  /*//$fabian="fabian";
  echo "  <td class=\"col-5\">" . $ct[$i]["fullname"] . "&nbsp;</td>\n";
  if (isset($ct[$i]["descoid"]) && $ct[$i]["descoid"] != null && isset($ct[$i]["descfilename"])) {
    echo "  <td class=\"col-3\"><a href=\"filedownload.php?" . filedownload($ct[$i]["descoid"], $ct[$i]["descfilename"]) .
		"\">" . basename($ct[$i]["descfilename"]) . "</a>&nbsp;&nbsp;<a href=\"#\" class=\"btn btn-primary\" style=\"font-weight:bold\" onClick=\"window.open('filewindow.php?".filedownload($ct[$i]["descoid"], $ct[$i]["descfilename"])."', 'Ver - PROBLEMA', 'width=680,height=600,scrollbars=yes,resizable=yes')\">Ver Problema</a></td>\n";
  }
  else
    echo "  <td class=\"col-2\">no description file available</td>\n";
	*/
  echo " </tr>\n";
}
echo "</tbody></table>";
if (count($ct) == 0) echo "<br><center><b><font color=\"#ff0000\">NO HAY COMPETENCIAS</font></b></center>";

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
