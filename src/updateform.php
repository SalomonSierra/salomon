<script>
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
  $('#userdescr').validCampoFabian(' abcdefghijklmnñopqrstuvwxyzáéiou');
  //Para escribir solo numeros
  $('#usernumber').validCampoFabian('0123456789');
  $('#userip').validCampoFabian('0123456789.');

  $('#userfull').validCampoFabian(' abcdefghijklmnñopqrstuvwxyzáéiou');
  $('#userdesc').validCampoFabian(' abcdefghijklmnñopqrstuvwxyzáéiou');
});


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
