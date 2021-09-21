<?php
$corfundo = "#e0e0d0";
$corfrente = "#000000";
$corfundo2 = "#dfdfdf";
$cormenu = "#dfdfdf";
?>
div#popupnew {
position:absolute;
left:50%;
top:17%;
margin-left:-202px;
font-family:'Raleway',sans-serif
}
div#normal {
width:100%;
height:100%;
opacity:.95;
top:0;
left:0;
display:none;
position:fixed;
background-color:#313131;
overflow:auto
}

A.menu {font-family:Verdana, Arial, Helvetica, sans-serif; text-decoration:none; font-size:12pt; border: 1px solid <?php echo $corfundo?>}
A.menu:hover {background-color:<?php echo $cormenu?>; border-bottom:1px solid #555555; border-right:1px solid #555555;border-top:1px solid white;border-left:1px solid white}


<?php for($i=1;$i<999;$i++) echo "table.sitehide$i .sitegroup$i { display: none; }\n";//para esconder ?>
