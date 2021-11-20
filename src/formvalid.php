<script>
//jquery
(function ( a ) {
    a.fn.validCampoFabian=function(b){
        a(this).on({keypress:function(a){
           var c=a.which,
           d=a.keyCode,
           e=String.fromCharCode(c).toLowerCase(),
           f=b;
           (-1!=f.indexOf(e)||9==d||37!=c&&37==d||39==d&&39!=c||8==d||46==d&&46!=c)&&161!=c||a.preventDefault()
       }})
   }
 }( jQuery )
);
$(function(){
  //Para escribir solo letras
  $('#userfullname').validCampoFabian(' abcdefghijklmnñopqrstuvwxyzáéiou');
  $('#userdesc').validCampoFabian(' abcdefghijklmnñopqrstuvwxyzáéiou');
  //Para escribir solo numeros
  //$('#userfullname').validCampoFabian('0123456789');
});

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
		 var usernumber = $('#usernumber').val().trim();
		 var username = $('#username').val().trim();

		 var usertype = $('#usertype').val().trim();
		 var userenabled = $('#userenabled').val().trim();
		 var usermultilogin = $('#usermultilogin').val().trim();
		 var userfullname = $('#userfullname').val().trim();
		 var useremail = $('#useremail').val().trim();
		 var userdesc = $('#userdesc').val().trim();
		 var userip = $('#userip').val().trim();

		 var passwordn1 = bighexsoma(js_myhash($('#passwordn1').val().trim()),js_myhash('fabians7'));
		 var passwordn2 = bighexsoma(js_myhash($('#passwordn2').val().trim()),js_myhash('fabians7'));

		 var passHASH=js_myhash(js_myhash($('#passwordn1').val().trim())+'<?php echo session_id(); ?>');
		 var userHASH=$('#username').val().trim();

		 var changepass = $('#changepass').val().trim();


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
