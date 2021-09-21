<!-- 1631305605 --> <?php exit; ?>	0
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
  <td>56056201</td>
  <td>0</td>
  <td>B</td>
  <td>C++11</td>
  <td class="text-primary">Not answerd yet</td>
<td><a href="../filedownload.php?oid=68861&filename=QkIvVmFqeldyYjhjY0E1MjduZ1V1dmdPN1p0bjFIUVZiSndkVnhuVFI0UXVLWk9HR2Z5ZzNXSGUwN0RQVWJya1ExZHgzVXhwU3JQN0YrWjIxUVdpemxXVWg2OU51QWwyVTk2dzdtcGtVUWs9&check=aed90bf958d269e8eae6fd545c4c115e260bf056891c9406a6af32b72af305f0#toolbar=0">suma.cpp</a>&nbsp;&nbsp;<a href="#" class="btn btn-primary" style="font-weight:bold" onClick="window.open('../filewindow.php?oid=68861&filename=aTBuT29ZZ3ZqVXpZNnU2Y1hxZ3F5c2Z0dEV5L2EvN2p0STFLZjRkY1M2bzhCdXNDenBQeUdIenBrTWNUMDRnU0x0MFd5NHkzcWZISjh3ZGxwT3QvcmUyMDRmcVFlc01UZFc1S1h6bDVPQzA9&check=aed90bf958d269e8eae6fd545c4c115e260bf056891c9406a6af32b72af305f0#toolbar=0', 'View - SOURCE', 'width=680,height=600,scrollbars=yes,resizable=yes')">Ver Codigo</a>
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
