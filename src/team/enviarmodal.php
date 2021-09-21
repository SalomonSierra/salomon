<div class="modal fade" role="dialog" id="enviar<?php echo $prob[$i]["number"]; ?>">
    <div class="modal-dialog">
        <div class="modal-content">

			<form name="form_submit" id="form_submit<?php echo $prob[$i]["number"]; ?>" enctype="multipart/form-data" method="post">
                <div class="modal-header">

					<h3 class="modal-title" >Enviar Problema</h3>

					<button type="button" class="close" data-dismiss="modal" name="bu">&times;</button>
                </div>

                <div class="modal-body">
                 <span class="text-warning">El nombre de la clase para java debe ser <b><?php echo $prob[$i]["basefilename"]; ?></b></span>

				  <input type="hidden"  name="problemnumber" id="problemnumber<?php echo $prob[$i]["number"]; ?>" value="<?php echo $prob[$i]["number"]; ?>">

                  <div class="from-group">
                      <label for="language">Language: </label>

                         <select class="form-control"  name="language" id="language<?php echo $prob[$i]["number"]; ?>">
                             <?php
                             $lang=DBGetLanguages();
                             /*for ($i=0; $i < count($lang); $i++) {
                                 echo "<option value=\"".$lang[$i]["number"]."\">".$lang[$i]["name"]."</option>\n";
                             }*/
                             ?>

                             <option selected value="-1">--</option>
                             <option value="<?php echo $lang[0]["number"]; ?>"><?php echo $lang[0]["name"]; ?></option>
                             <option value="<?php echo $lang[1]["number"]; ?>"><?php echo $lang[1]["name"]; ?></option>
                             <option value="<?php echo $lang[2]["number"]; ?>"><?php echo $lang[2]["name"]; ?></option>
                             <option value="<?php echo $lang[3]["number"]; ?>"><?php echo $lang[3]["name"]; ?></option>
                             <option value="<?php echo $lang[4]["number"]; ?>"><?php echo $lang[4]["name"]; ?></option>


                         </select>

                  </div>

                  <div class="from-group">
                    <label for="probleminput">Source file:</label>
                    <input type="file" name="sourcefile" id="sourcefile<?php echo $prob[$i]["number"]; ?>" class="form-control" value="">
                  </div>
                  <div class="from-group">
                    <textarea class="form-control" id="textsource<?php echo $prob[$i]["number"]; ?>"  name="textsource<?php echo $prob[$i]["number"]; ?>" rows="10" readonly></textarea>
                  </div>

                </div>

                <div class="modal-footer">

                  <button type="button" class="mx-5 btn btn-danger" data-dismiss="modal" name="cancel">Cancel</button>
                  <button type="button" class="btn btn-success" name="Submit3" onClick="enviar(<?php echo $prob[$i]["number"]; ?>)">Enviar</button>

				  <!--<input type="submit" class="btn btn-primary" name="Submit3" value="Enviar" onClick="conf()">&nbsp;
				  -->
                </div>
            </form>
        </div>

    </div>
</div>

<script language="javascript">
//para textarea
document.getElementById('sourcefile'+"<?php echo $prob[$i]["number"]; ?>").addEventListener('change', function(e){

    var archivo = e.target.files[0];
    if (!archivo) {
      return;
    }
    var lector = new FileReader();
    lector.onload = function(e) {
      var contenido = e.target.result;
      var elemento = document.getElementById('textsource'+"<?php echo $prob[$i]["number"]; ?>");
      elemento.innerHTML = contenido;
    };
    lector.readAsText(archivo);

}, false);

</script>
