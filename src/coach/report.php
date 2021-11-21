<?php

require 'header2.php';

?>

<div class="container mt-5">
    <div class="mx-5 p-5 bg-secondary">
        <div class="row">

            <a href="#" class="btn btn-success mx-5" onClick="window.open('../report/score.php?p=2','Scoreboard','width=800,height=600,scrollbars=yes,toolbar=yes,menubar=yes,resizable=yes')">Scoreboard</a>
            <a href="#" class="btn btn-success mx-3" onClick="window.open('../report/score.php?p=0','Scoreboard','width=800,height=600,scrollbars=yes,toolbar=yes,menubar=yes,resizable=yes')">Scoreboard Detallado </a>
            <a href="#" class="btn btn-success mx-3" onClick="window.open('../report/score.php?p=0&hor=0','Scoreboard','width=800,height=600,scrollbars=yes,toolbar=yes,menubar=yes,resizable=yes')">Scoreboard Interactivo </a>
            <a href="#" class="btn btn-success mx-3" onClick="window.open('../report/score.php?p=1','Public Scoreboard','width=800,height=600,scrollbars=yes,toolbar=yes,menubar=yes,resizable=yes')">Scoreboard Retrasado</a>
        </div>
        <br>
        <div class="row">
            <a href="#" class="btn btn-primary mx-5" onClick="window.open('../report/run.php','Public Scoreboard','width=800,height=600,scrollbars=yes,toolbar=yes,menubar=yes,resizable=yes')">Lista de Ejecuciones</a>
            <a href="#" class="btn btn-primary mx-5" onClick="window.open('../report/stat.php?p=1','Public Scoreboard','width=800,height=600,scrollbars=yes,toolbar=yes,menubar=yes,resizable=yes')">Estadístíca</a>
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
