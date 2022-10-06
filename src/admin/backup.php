
<?php
require('header.php');
if(isset($_FILES["import"]) && isset($_POST["Submit"]) && $_FILES["import"]["name"]!=""){

    $file="../../tools/importdb.sh";
    $ex = escapeshellcmd($file);

    //tenemos la ruta
    require_once('../db.php');
    @include_once('../version.php');

    DBDropDatabase();

    DBCreateDatabase();


    $text=shell_exec("./".$file." ".$_FILES["import"]["tmp_name"]);
    //echo "(".$text.")";
    if(!$text)
        exit;
    else{
        //echo "importado db";
        MSGError("SE IMPORTÓ LA BASE DE DATOS");
        ForceLoad("backup.php");
    }

}

?>

<div class="container">
    <div class="row py-5 bg-secondary mt-5 mx-5">
        <div class="col-6 p-5">
            <label class="text-white"for="">Para exportar DB click en el Boton</label><br>

            <a href="../filedownload0.php" class="btn btn-success">Exportar DB</a>
        </div>
        <div class="col-6">
            <form method="POST" action="backup.php" enctype="multipart/form-data">
                <div class="from-group">
                  <label class="text-white" for="import">Archivo para importar DB .sql</label>
                  <input type="file" name="import" id="import" class="form-control" value="">
              </div><br>
                <button type="submit" name="Submit" class="btn btn-primary">Importar DB</button>
                <label class="text-warning">Nota. Si esta en funcionamiento el auto juez, debe finalizar para importar db.</label>
            </form>
        </div>

    </div>

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
