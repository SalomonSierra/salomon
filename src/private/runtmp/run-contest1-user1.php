<!-- 1632361484 --> <?php exit; ?>	0	lechuga	33F404
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
  <td>308030201</td>
  <td>67</td>
  <td>A</td>
  <td>C++11</td>
  <td class="text-success">YES - Accepted <img alt="lechuga" width="15" src="/salomon/balloons/09f7dde6e1d62020b2a6420ea7f8dcc0.png" /></td>
<td><a href="../filedownload.php?oid=16560&filename=bUR2THFVZEdRWlhCYlh1U05Kb0E1akFnZGQ3VFQ3U25WWXpWaUNhazcvVTYxeXFxV3Z0VTFxSkVzcWtoR0ZqQklFbTF6bjhVMU5uUUlWbER3TGdNbkJTOXNZLzgzaFBBTFlRR1l5ajk1WTA9&check=4776c3132e43a7885f67803771d7940eaf34138c49ff93348adbba62d48ec96f#toolbar=0">print.cpp</a>&nbsp;&nbsp;<a href="#" class="btn btn-primary" style="font-weight:bold" onClick="window.open('../filewindow.php?oid=16560&filename=YVRhem8rb1BBYzM4WGo0RU5rK1lzOUtnd3IxOHNIV1Qxb210MUhrOG9OeFVDQ21xcko1RFBxSFVyeTQzbzZhWWhIcE1RN1Fnc1orRVRIYlNNb1BtZ0dQbGx0VnZ0VndFWmdub01uUVFreXc9&check=4776c3132e43a7885f67803771d7940eaf34138c49ff93348adbba62d48ec96f#toolbar=0', 'View - SOURCE', 'width=680,height=600,scrollbars=yes,resizable=yes')">Ver Codigo</a>
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
