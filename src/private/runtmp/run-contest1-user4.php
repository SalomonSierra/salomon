<!-- 1631306218 --> <?php exit; ?>	0
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
  <td>59276301</td>
  <td>6</td>
  <td>A</td>
  <td>C++11</td>
  <td class="text-danger">NO - Wrong answer</td>
<td><a href="../filedownload.php?oid=68873&filename=TlNsc01tQklsMW02OUdCdkkrUFlzaFZOeERUZ09WZkUxTGpuK1V0WHJUcUpIWFdZeUlLcEh5MTlnLzVXK1hUVk5nSXlaWUhJQVZYSmc2SVlsNkI3Tld5WVRaU0F3VXNEWGY5ZmtaL052MlU9&check=47ceaabd52e1ebb78ac279ed42a27a3ef97cdf9b3c6c1131dc3c0f550b9a69a1#toolbar=0">calle1.cpp</a>&nbsp;&nbsp;<a href="#" class="btn btn-primary" style="font-weight:bold" onClick="window.open('../filewindow.php?oid=68873&filename=Z082VmY2ZXYrOTlHaW9DWGJlKzVYV2RFR041azFmbFR6bXpBSmlVdXhmUkZPWGkxWTloV291WXpBdWpaSDdCZWFpcmNadlBQMXg5Y0F6VGJKUzZsSFhUUkNvaFUyNlc3T3NpQytGZGZpN1E9&check=47ceaabd52e1ebb78ac279ed42a27a3ef97cdf9b3c6c1131dc3c0f550b9a69a1#toolbar=0', 'View - SOURCE', 'width=680,height=600,scrollbars=yes,resizable=yes')">Ver Codigo</a>
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
