<!-- 1632749283 --> <?php exit; ?>	0	lechuga	33F404	negro	000000
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
  <td>868779007</td>
  <td>241</td>
  <td>A</td>
  <td>Java</td>
  <td class="text-success">YES - Accepted <img alt="lechuga" width="15" src="/salomon/balloons/09f7dde6e1d62020b2a6420ea7f8dcc0.png" /></td>
<td><a href="../filedownload.php?oid=16596&filename=bkZiZEJnU3ZLU1NUSGR2TFBtWHhJWkZkSzBpampmMFppMy8zbndzemgzK3BjSU5meHZLV0t3M2VjVlRPL1phN0wyU0lTWlFWNVJEdnNtRmg3V0JieWpjd1dWWmQrRnpPZkVBRXRaL0hzNTg9&check=8e749c42061b222f6bb9cd3591389f51db58737d26945b907090f13a46ef4de8#toolbar=0">print.java</a>&nbsp;&nbsp;<a href="#" class="btn btn-primary" style="font-weight:bold" onClick="window.open('../filewindow.php?oid=16596&filename=VUdVR0VUNFk5eXEzOVFKbTNlWTBLRFZMWjQ4UEsvMjRCV1k0N3M2SFRXRWVzNnFWMlpORklQVXdJTDRQY0hhaTVUQ3o2TDc2YUZnWHdjNVc4YnVjNGNhek1rbmdneTBrakZrNHk0SmFHd2s9&check=8e749c42061b222f6bb9cd3591389f51db58737d26945b907090f13a46ef4de8#toolbar=0', 'View - SOURCE', 'width=680,height=600,scrollbars=yes,resizable=yes')">Ver Codigo</a>
</td>
 </tr>
 <tr>
  <td>869001308</td>
  <td>242</td>
  <td>B</td>
  <td>Java</td>
  <td class="text-success">YES - Accepted <img alt="negro" width="15" src="/salomon/balloons/670b14728ad9902aecba32e22fa4f6bd.png" /></td>
<td><a href="../filedownload.php?oid=16600&filename=VEFySFgyWEF3TGJVdzBqUzMzTVp2VlpoMHpNVUpSaUxaamtvUzB2WVF4ajNoUU5pMEgzNE5CTlV4ZGZvdnRPSHlLUHVVb2tqNDNvTEZKT0NDMVd6MDF0U3R0ZFZDWXdhOEpwUk5mNjZqdzQ9&check=db8092319c0a865917d65611138ad4447fe50c92071a038a0d37661ec4815cd7#toolbar=0">suma.java</a>&nbsp;&nbsp;<a href="#" class="btn btn-primary" style="font-weight:bold" onClick="window.open('../filewindow.php?oid=16600&filename=aTJIN2YzeXJ3ajd0MGx5dEErbDZ2bTAyVUhRZlRvdnF2QUUwVEpMbmVEM0Z0bDdZWXIrMWNMU3I5cmVrTDYvdzNtN1lvVTVVT0JDandiZDEyOGs2cGcybk9GUHhFR2ZIY2lFbGlYVm9TdWs9&check=db8092319c0a865917d65611138ad4447fe50c92071a038a0d37661ec4815cd7#toolbar=0', 'View - SOURCE', 'width=680,height=600,scrollbars=yes,resizable=yes')">Ver Codigo</a>
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
