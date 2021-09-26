<!-- 1632687021 --> <?php exit; ?>	0	negro	000000
<br>
<table class="table table-hover">
 <thead>
 <tr>
  <th scope="col">Run #</th>
<th scope="col">Time</th>
  <th scope="col">Problem</th>
  <th scope="col">Language</th>
  <th scope="col">Answer</th>
  <th scope="col">File</th>
 </tr>
 </thead>
<tbody>
 <tr>
  <td>869684909</td>
  <td>243</td>
  <td>B</td>
  <td>Java</td>
  <td class="text-success">YES - Accepted <img alt="negro" width="15" src="/salomon/balloons/670b14728ad9902aecba32e22fa4f6bd.png" /></td>
<td><a href="../filedownload.php?oid=16604&filename=TlBOWGpldkxTUXpVc0hVMEx4Ump5RFNZWW91clFGMjJETFBRbzBGWGwwSDljMStlU1ZDbVE2MWd1aWJveDR0TGNvVFVrZWF1WGp3UTFZNkR2NWFjU2dXd2FJS1F6bWFBc25VMXlkRmdDSFk9&check=55a28f94ed8a4bb34f98ad98ce3025f620fbfffc2d8139c2ae8d29af37603424#toolbar=0">suma.java</a>&nbsp;&nbsp;<a href="#" class="btn btn-primary" style="font-weight:bold" onClick="window.open('../filewindow.php?oid=16604&filename=L1h5QVVjNENjVVZZVi85bkRrR1I2TEtsZGJuMm91UU51N1BZcDc2TldVcjRnRy9ISEtTV1hRdVQ3bC8xWjRvK0ZSdTFzUEo0ZXhwQXpiL1hRdWtINDViWmRiQi9BK1VtNDdySHlpbUErTVU9&check=55a28f94ed8a4bb34f98ad98ce3025f620fbfffc2d8139c2ae8d29af37603424#toolbar=0', 'View - SOURCE', 'width=680,height=600,scrollbars=yes,resizable=yes')">Ver Codigo</a>
</td>
 </tr>
</tbody>
</table>
<br><br><center><b>Para enviar un programa, simplemente complete los siguientes campos:</b></center>
<div class="container">
<form name="form1" enctype="multipart/form-data" method="post" action="run.php">
  <input type=hidden name="confirmation" value="noconfirm" />
 <div class="form-group row">
   <div class="col-sm-4">
       <div class="form-group row">
           <label class="col-sm-4 col-form-label">Problem:</label>
           <div class="col-sm-8">
               <select name="problem" onclick="Arquivo()">
<option selected value="-1"> -- </option>
<option value="1">A</option>
<option value="2">B</option>
<option value="3">C</option>
<option value="4">D</option>
	</select>
           </div>
       </div>
       <div class="form-group row">
           <label class="col-sm-4 col-form-label">Language:</label>
           <div class="col-sm-8">
               <select name="language" onclick="Arquivo()">
<option selected value="-1"> -- </option>
<option value="1">C</option>
<option value="2">C++11</option>
<option value="3">Java</option>
<option value="4">Python2</option>
<option value="5">Python3</option>
	  </select>
           </div>
       </div>
       <label class="col-form-label">Source code:</label>
  	    <input type="file" class="form-control" id="sourcefile" name="sourcefile" size="40" onclick="Arquivo()">
   </div>
   <div class="col-sm-8">
       <textarea class="form-control" id="textsource"  name="textsource" rows="10" readonly></textarea>
   </div>
 </div>
  <script language="javascript">
    function conf() {
      if (document.form1.problem.value != '-1' && document.form1.language.value != '-1') {
       if (confirm("Confirm submission?")) {
        document.form1.confirmation.value='confirm';
       }
      } else {
        alert('Invalid problem and/or language');
      }
    }
  </script>
  <center>
      <input type="submit" class="btn btn-primary" name="Submit" value="Send" onClick="conf()">
      <input type="reset" class="btn btn-primary" name="Submit2" value="Clear">
  </center>
</form>
</div>
