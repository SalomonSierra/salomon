<?php

require('header.php');

if(isset($_GET["order"]))
$order = myhtmlspecialchars($_GET["order"]);
else $order='';
if(isset($_GET["user"]))
$user = myhtmlspecialchars($_GET["user"]);
else $user='';

if(isset($_GET["type"]))
$type = myhtmlspecialchars($_GET["type"]);
else $type='';
if(isset($_GET["ip"]))
$ip = myhtmlspecialchars($_GET["ip"]);
else $ip='';
$get="&order=${order}&user=${user}&type=${type}&ip=${ip}";
if (isset($_GET["limit"]) && $_GET["limit"]>0)
  $limit = myhtmlspecialchars($_GET["limit"]);
else $limit = 50;
$log = DBGetLogs($order,$user, $type, $ip, $limit);
?>
<br>
<table class="table table-sm table-bordered table-hover">
    <thead>
		<tr>
		 <td scope="col"><a href="log.php?order=user&limit=<?php echo $limit; ?>">Usuario #</a></td>
		 <td scope="col"><a href="log.php?order=ip&limit=<?php echo $limit; ?>">IP</a></td>
		 <td scope="col"><a href="log.php?order=type&limit=<?php echo $limit; ?>">Tipo</a></td>
		 <td scope="col">Fecha</td>
		 <td scope="col">Descripcion</td>
		</tr>
	</thead>
    <tbody>

<?php
for ($i=0; $i<count($log); $i++) {
  echo " <tr>\n";
  //echo "  <td nowrap><a href=\"log.php?site=" . $log[$i]["site"] . "&limit=$limit\">" . $log[$i]["site"] . "</a></td>\n";
  echo "  <td><a href=\"log.php?user=" . $log[$i]["user"] . "&limit=$limit\">" . $log[$i]["user"] . "</a></td>\n";
  echo "  <td><a href=\"log.php?ip=" . $log[$i]["ip"] . "&limit=$limit\">" . $log[$i]["ip"] . "</a></td>\n";
  echo "  <td><a href=\"log.php?type=" . $log[$i]["type"] . "&limit=$limit\">" . $log[$i]["type"] . "</a></td>\n";
  echo "  <td>" . dateconv($log[$i]["date"]) . "</td>\n";
  echo "  <td>" . $log[$i]["data"] . "</td>\n";

  echo "</tr>\n";
}
echo "</tbody></table>\n";

?>
<br>
<center>
<a href="log.php?limit=50<?php echo $get; ?>">50</a>
<a href="log.php?limit=200<?php echo $get; ?>">200</a>
<a href="log.php?limit=1000<?php echo $get; ?>">1000</a>
<a href="log.php?limit=1000000<?php echo $get; ?>">sin limite</a>


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
