<!-- 1631290057 --> <?php exit; ?>	0	verde	FC6F00
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
  <td>899367601</td>
  <td>20</td>
  <td>A</td>
  <td>C++11</td>
  <td class="text-success">YES - Accepted <img alt="verde" width="15" src="/salomon/balloons/5d5b6075c41fcf6842d5a25fdd51f828.png" /></td>
<td><a href="../filedownload.php?oid=68693&filename=Nmk0eDR6clpNVUo2L2N0N2FuaW52dkt4d2tJRVp4d2lvbnpDMHhVdC9NM1JlOE5DcmxkYXhoeHR2SUJrNUo1Uk9wTUVhc0lXNU8rbEJJQ2NLRnJvMkxPM1laUlhhMUxwNmt3RkcrWXlRM289&check=975623c4a613b20cfcb38f1636b60d060727a79562da1502f161b0a8db8085cf#toolbar=0">calle.cpp</a>&nbsp;&nbsp;<a href="#" class="btn btn-primary" style="font-weight:bold" onClick="window.open('../filewindow.php?oid=68693&filename=ZEh6cDdpR0UrWDJxQTVLSXIvVkV3RVpHK0p4NkIrU2d3L2pPRXMzKzZjQWVjRGNzZFNzdHpSUHhIMDRyUS9nNTVSRzFvazZKOUxIbHdlMEg5eWdObEI1NUtQRDhlcjdYY3ppM1IwcFFNKzA9&check=975623c4a613b20cfcb38f1636b60d060727a79562da1502f161b0a8db8085cf#toolbar=0', 'View - SOURCE', 'width=680,height=600,scrollbars=yes,resizable=yes')">Ver Codigo</a>
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
