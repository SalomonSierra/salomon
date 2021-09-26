<!-- 1632686900 --> <?php exit; ?>	0	lechuga	33F404
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
<td><a href="../filedownload.php?oid=16596&filename=cXl5T1VubWo0UEw2YitmTVZKZng1RlBPNDNaRkFEM2JDTkVmbUVpVnB0VFdpemtsaldQVFp3eU1uZ084Y1hrRUNsd1pNTHhjQXZsZkdEZVFxbmJ1Vi95c203ZEl4YklpQXBmVi9VNktGbUU9&check=f6104e8b06184a0dafdc383be26c41726656156ad389c3bc0ba2c816926802e3#toolbar=0">print.java</a>&nbsp;&nbsp;<a href="#" class="btn btn-primary" style="font-weight:bold" onClick="window.open('../filewindow.php?oid=16596&filename=TjkvNGZUM1Z2R0gwVFNaTWh2bmg1TUk0bzdDM3JhMGhlRk9LYWN5cCtIdzlMNm1IWC9HdVVOaGZtV2kxSjVNUHJLc3R1Ujcrb1pTZW9WVDA1UUhkcUJ4SzU1MTNUczRDajg1NFNmSDUyU2s9&check=f6104e8b06184a0dafdc383be26c41726656156ad389c3bc0ba2c816926802e3#toolbar=0', 'View - SOURCE', 'width=680,height=600,scrollbars=yes,resizable=yes')">Ver Codigo</a>
</td>
 </tr>
 <tr>
  <td>869001308</td>
  <td>242</td>
  <td>B</td>
  <td>Java</td>
  <td class="text-primary">Not answerd yet</td>
<td><a href="../filedownload.php?oid=16600&filename=YzF2L3ZCT243TEV5emxNSWxXRkxmdnVRK01tcStGandBaGNXeDM3T2EvL1IvRGhRdHhOMWszWTVqZ09DZm9ROHdKbmRRcGVSTWlwUi9nSTY1UjR4R2c1UUg3bGEvK1NsNEFZbjBUd2tJSzQ9&check=4ccc50ab34bbc79c1b1be65dc9afd302a1e652eb8b77d4662970c056aaf7dca9#toolbar=0">suma.java</a>&nbsp;&nbsp;<a href="#" class="btn btn-primary" style="font-weight:bold" onClick="window.open('../filewindow.php?oid=16600&filename=V3ZpTFhUNWZTQUI4Y0VJa0dGZ25NSlZGWXB0VXJxdlNaQ0IvZXI1bDBUQUxROGE1KzZFd2VRc1o2SmptQWlvZCtHVmNrQVc1WXRFT0t5N2pLMUJ6UThUQ00wVUZMMENPR25reXFPZTZITW89&check=4ccc50ab34bbc79c1b1be65dc9afd302a1e652eb8b77d4662970c056aaf7dca9#toolbar=0', 'View - SOURCE', 'width=680,height=600,scrollbars=yes,resizable=yes')">Ver Codigo</a>
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
