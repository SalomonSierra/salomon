<?php

require 'header.php';//la cabezera

$status="status.php";
if(isset($_GET["order"]) && $_GET["order"] != "") {

    $order = myhtmlspecialchars($_GET["order"]);
	$_SESSION["runline"] = $order;
} else {

	if(isset($_SESSION["runline"]))
        $order = $_SESSION["runline"];
    else
		$order = '';
}
?>
<br>
<ol class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="index.php">Inicio</a>
	</li>
	<li class="breadcrumb-item active">Status</li>
</ol>
<!--$status = run.php-->
<form name="form1" method="post" action="<?php echo $status; ?>">
  <input type=hidden name="confirmation" value="noconfirm" />
  <br>
  <table class="table table-bordered table-hover">
     <thead>
  		<tr>
            <th scope="col"><a href="<?php echo $status; ?>?order=run">RunID #</a></th>
            <!--<th scope="col"><a href="<?php //echo $status; ?>?order=site">Site</a></th>-->
            <?php if($status == "status.php") { ?>
            <th scope="col"><a href="<?php echo $status; ?>?order=user">User</a></th>
            <?php } ?>
            <th scope="col">Submit Time</th>
            <th scope="col"><a href="<?php echo $status; ?>?order=problem">Problem</a></th>
            <th scope="col"><a href="<?php echo $status; ?>?order=language">Language</a></th>

            <th scope="col"><a href="<?php echo $status; ?>?order=answer">Answer</a></th>
  		</tr>
  	 </thead>
  	 <tbody>

<?php

//(contest,sitejudging(usersiten),''0 user(etc))
//capturamos toda la informacion acerca de todos los envios realizados de un contest
//funcion que me devuelve datos de las tablas runtable,problemtable,langtable,answertable,usertable
//y dependiendo del orden -1 es para no añadir mas a la consulta de la base de datos
//$run = DBAllRunsInSites($_SESSION["usertable"]["contestnumber"], $s["sitejudging"], $order);
$run = DBAllRuns($order);


////saca todos los username de los usuario guardando en un matriz $a[i][usersitenu-usernumber]='username'
$us = DBAllUserNames();

for ($i=0; $i<count($run); $i++) {

    echo"<td>" . $run[$i]["number"] . "</td>\n";
    if($status == "status.php") {
        if ($run[$i]["user"] != "") {
	        echo "  <td>" . $us[$run[$i]["user"]] . "</td>\n";
        }
    }
    echo "  <td>" . dateconv($run[$i]["updatetime"]) . "</td>\n";
    echo "  <td>" . $run[$i]["problemnumber"] . "</td>\n";

    if($_SESSION["usertable"]["usernumber"]==$run[$i]["user"]){
        echo "  <td><a href=\"#\" onClick=\"window.open('../filewindow.php?".filedownload($run[$i]["oid"],$run[$i]["filename"])."', 'View - SOURCE', 'width=680,height=600,scrollbars=yes,resizable=yes')\">".$run[$i]["language"] ."</a></td>\n";
    }else{
        echo "  <td>" . $run[$i]["language"] . "</td>\n";
    }

    if ($run[$i]["answer"] == "" || $run[$i]["answer"] == "Not answerd yet") {
        echo "  <td class=\"text-primary\">Waiting answer...</td>\n";
    } else {
        if($run[$i]["answer"]=="YES - Accepted"){
            echo "  <td class=\"text-success\">" . $run[$i]["answer"];
        }else{
            echo "  <td class=\"text-danger\">" . $run[$i]["answer"];
        }

        if($run[$i]['yes']=='t') {

            echo " <img alt=\"".$run[$i]["colorname"]."\" width=\"10\" ".
			    "src=\"" . balloonurl($run[$i]["color"]) ."\" />";
        }
        echo "</td>\n";
    }
    echo " </tr>\n";
}

echo "<tbody></table>";
//NO RUNS AVAILABLE
if (count($run) == 0) echo "<br><center><b><font color=\"#ff0000\">NO HAY EJECUCIONES DISPONIBLES</font></b></center>";
else {
?>


  </form>
<?php
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
		 var usersitenumber = $('#usersitenumber').val();
		 var usernumber = $('#usernumber').val();
		 var username = $('#username').val();

		 var usertype = $('#usertype').val();
		 var userenabled = $('#userenabled').val();
		 var usermultilogin = $('#usermultilogin').val();
		 var userfullname = $('#userfullname').val();
		 var userdesc = $('#userdesc').val();
		 var userip = $('#userip').val();

		 var passwordn1 = bighexsoma(js_myhash($('#passwordn1').val()),js_myhash('fabians7'));
		 var passwordn2 = bighexsoma(js_myhash($('#passwordn2').val()),js_myhash('fabians7'));

		 var passHASH=js_myhash(js_myhash($('#passwordn1').val())+'<?php echo session_id(); ?>');
		 var userHASH=$('#username').val();

		 var changepass = $('#changepass').val();

         if(username != '' && userfullname != '' && userdesc != '' && passwordn1 != '' && passwordn2 != ''){
             //alert("entro");
			 if (confirm("Confirm?")) {

				 $.ajax({

					  url:"include/i_header.php",
					  method:"POST",
					  data: {usersitenumber:usersitenumber, usernumber:usernumber, username:username, usertype:usertype, userenabled:userenabled, usermultilogin:usermultilogin, userfullname:userfullname, userdesc:userdesc, userip:userip, passwordn1:passwordn1, passwordn2:passwordn2, changepass:changepass},

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
							   //alert(data);
							   if(data.indexOf('username already in use') !== -1) {
								  // do stuff with the string
								  alert('username already in use');
							   }
							   if(data.indexOf('Passwords don\'t match.') !== -1) {
								  // do stuff with the string
								  alert('Passwords don\'t match.');
							   }
                               if(data.indexOf('Contest no active') !== -1) {
								  // do stuff with the string
								  alert('Contest no active');
								  $('#registerModal').hide();
								  location.reload();
							   }
						   }

						   /*if(data == 'existuser')
						   {
								alert("user exist");
						   }
						   else
						   {
							   if(data == 'Yes'){
								   $('#registerModal').hide();
								   location.reload();
							   }else{
								   alert(data);
							   }


								//
								//location.href="../indexs.php";

						   }*/
					  }
				 });

		     }else{
				 $('#registerModal').hide();
				 location.reload();
			 }



         }
         else
         {
              alert("Both Fields are required");
         }
    });


    //logout


});
//register

</script>
