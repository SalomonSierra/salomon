<?php
//construir problema
require('header.php');
//retorna la informacion de la competencia en caso no retorna null
if(($ct = DBContestInfo(0)) == null)
	ForceLoad("../index.php");//index.php

?>

    <div class="container">

        <!--To build a problem package using standard script files, fill in the following fields.-->
        <u>Para crear un paquete de problemas utilizando archivos de secuencia de comandos estándar, complete los siguientes campos.</u>

        <form name="form1" enctype="multipart/form-data" method="post" action="problemset.php">
          <input type=hidden name="noflush" value="true" />
          <input type=hidden name="confirmation" value="noconfirm" />
          <script language="javascript">
            function conf() {
        	        var s2 = String(document.form1.probleminput.value);
        	        var s1 = String(document.form1.problemsol.value);
        			if(document.form1.fullname.value=="" || document.form1.basename=="" || document.form1.timelimit=="" || s1.length<4 || s2.length<4) {
        				alert('Sorry, mandatory fields are empty');//Lo sentimos, los campos obligatorios están vacíos
        			} else {
        				var s1 = String(document.form1.problemdesc.value);
        				var l = s1.length;
                        //
        				if(l >= 3 && (s1.substr(l-3,3).toUpperCase()==".IN" ||
        							 s1.substr(l-4,4).toUpperCase()==".OUT" ||
        							 s1.substr(l-4,4).toUpperCase()==".SOL" ||
        							 s1.substr(l-2,2).toUpperCase()==".C" ||
        							 s1.substr(l-2,2).toUpperCase()==".H" ||
        							 s1.substr(l-3,3).toUpperCase()==".CC" ||
        							 s1.substr(l-3,3).toUpperCase()==".GZ" ||
        							 s1.substr(l-4,4).toUpperCase()==".CPP" ||
        							 s1.substr(l-4,4).toUpperCase()==".HPP" ||
        							 s1.substr(l-4,4).toUpperCase()==".ZIP" ||
        							 s1.substr(l-4,4).toUpperCase()==".TGZ" ||
        							 s1.substr(l-5,5).toUpperCase()==".JAVA")) {
        					alert('Description file has invalid extension: ...'+s1.substr(l-3,3));
        				} else {
        					document.form1.confirmation.value='confirm';
        				}
        			}
             }
          </script>
          <div class="form-group row">
              <label for="fullname" class="col-sm-4 col-form-label">Nombre del problema:</label>
              <div class="col-sm-8">
                  <input type="text" name="fullname" class="form-control" id="fullname" value="" size="50" maxlength="100" />
              </div>
          </div>
          <div class="form-group row">
              <label for="basename" class="col-sm-4 col-form-label">Nombre base del problema (nombre de la clase que se espera que tenga la principal):</label>
              <div class="col-sm-8">
                  <input type="text" name="basename" class="form-control" id="basename" value="" size="50" maxlength="100" />
              </div>
          </div>
          <div class="form-group row">
              <label for="problemdesc" class="col-sm-4 col-form-label">Descripcion Archivo (PDF, txt, ...):</label>
              <div class="col-sm-8">
                  <input type="file" name="problemdesc" class="form-control" id="problemdesc" value="" size="40" />
              </div>
          </div>
          <div class="form-group row">
              <label for="probleminput" class="col-sm-4 col-form-label">Archivo de entrada del problema:</label>
              <div class="col-sm-8">
                  <input type="file" name="probleminput" class="form-control" id="probleminput" value="" size="40" />
              </div>
          </div>
          <div class="form-group row">
              <label for="problemsol" class="col-sm-4 col-form-label">Archivo de salida correcto de problema:</label>
              <div class="col-sm-8">
                  <input type="file" name="problemsol" class="form-control" id="problemsol" value="" size="40" />
              </div>
          </div>
          <div class="form-group row">
              <label for="problemsol" class="col-sm-4 col-form-label">Tiempo Limite (en seg):</label>
              <div class="col-sm-8">
                   <input type="text" name="timelimit" class="form-control" id="timelimit" value="" size="10" />
                   (opcional: use a, seguido del número de repeticiones para ejecutar)
              </div>
          </div>
          <div class="form-group row">
              <input type="submit" name="Submit5" class="btn btn-primary" value="Descargar" onClick="conf()">
              <input type="reset" name="Submit4" class="btn btn-primary" value="Limpiar">
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
<?php include '../updateform.php';?>
