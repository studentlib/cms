<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<style type="text/css">
label{vertical-align:left;width: 180px;float:left;}
input{vertical-align:left;width: 150px;float:left;}
body{font-family:tahoma Verdana;font-size:12px;}
#maxtrix input
{
	vertical-align:left;width: 180px;float:left;
}
#maxtrix label
{
vertical-align:left;width: 180px;float:left;
}
#content{width:1000px;cellspacing:1px;border:1 ;}
</style>
<script type="text/javascript">


$(document).ready(function(){

     $('#servers').change(function(){
 	    $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
  	       location.reload();
 	 	});
      });
     $('#all').onclick(function(){
    	 $('#server').attr("checked");
         });
      
});

</script>
</head>
<body>
<div style="float:left;" >
<div style="float:left;" >
<legend>Mời chọn server</legend>
<form action="?m=account&c=vip_rank&a=search" method="post" >
<table >

    <?php foreach($servers as $kk=>$vv){?>
    <tr><th><?php echo $vv['id'].'Server-'.$vv['text'];?></th>
    <th><input type="checkbox"  class="server" name="<?php echo $kk;?>" id="server"></th></tr>
    <?php }?>
</table>
<input type="submit" id="search" value="Tìm">
</form>
</div>

<fieldset>
<legend>vipDanh sách</legend>
<table id="content"  border="1" cellspacing="0";>
<caption>vipThống kê</caption>
<tr>
<th>Server</th>
<th>0Cấp</th>
<th>1Cấp</th>
<th>2Cấp</th>
<th>3Cấp</th>
<th>4Cấp</th>
<th>5Cấp</th>
<th>6Cấp</th>
<th>7Cấp</th>
<th>8Cấp</th>
<th>9Cấp</th>
<th>10Cấp</th>
<th>11Cấp</th>
<th>12Cấp</th>
<th>13Cấp</th>
<th>14Cấp</th>
<th>15Cấp</th>
<th>16Cấp</th>
<th>17Cấp</th>
<th>18Cấp</th>
<th>19Cấp</th>
<th>20Cấp</th>
<th>21Cấp</th>
</tr>
<?php 
foreach($level as $kn=>$vn){
    //var_dump($vn);die;
    $m0=array();$m1=array();$m2=array();$m3=array();$m4=array();$m5=array();$m6=array();$m7=array();$m8=array();
    $m9=array();$m10=array(); $m11=array();$m12=array();$m13=array();$m14=array();$m15=array();$m16=array();
    $m17=array();$m18=array();$m19=array();$m20=array();$m21=array();
foreach($vn as $k1=>$v1){
    switch ($v1) {
    case 0:$m0[]=$v1;break;
    case 1:$m1[]=$v1;break;
    case 2:$m2[]=$v1;break;
    case 3:$m3[]=$v1;break;
    case 4:$m4[]=$v1;break;
    case 5:$m5[]=$v1;break;
    case 6:$m6[]=$v1;break;
    case 7:$m7[]=$v1;break;
    case 8:$m8[]=$v1;break;
    case 9:$m9[]=$v1;break;
    case 10:$m10[]=$v1;break;
    case 11:$m11[]=$v1;break;
    case 12:$m12[]=$v1;break;
    case 13:$m13[]=$v1;break;
    case 14:$m14[]=$v1;break;
    case 15:$m15[]=$v1;break;
    case 16:$m16[]=$v1;break;
    case 17:$m17[]=$v1;break;
    case 18:$m18[]=$v1;break;
    case 19:$m19[]=$v1;break;
    case 20:$m20[]=$v1;break;
    case 21:$m21[]=$v1;break;
     }
}
?>
<tr>
<th><?php echo $vn[1].'server-'.$vn[0];?></th>
<th><?php echo count($m0);?></th>
<th><?php echo count($m1);?></th>
<th><?php echo count($m2);?></th>
<th><?php echo count($m3);?></th>
<th><?php echo count($m4);?></th>
<th><?php echo count($m5);?></th>
<th><?php echo count($m6);?></th>
<th><?php echo count($m7);?></th>
<th><?php echo count($m8);?></th>
<th><?php echo count($m9);?></th>
<th><?php echo count($m10);?></th>
<th><?php echo count($m11);?></th>
<th><?php echo count($m12);?></th>
<th><?php echo count($m13);?></th>
<th><?php echo count($m14);?></th>
<th><?php echo count($m15);?></th>
<th><?php echo count($m16);?></th>
<th><?php echo count($m17);?></th>
<th><?php echo count($m18);?></th>
<th><?php echo count($m19);?></th>
<th><?php echo count($m20);?></th>
<th><?php echo count($m21);?></th>
</tr>
<?php }?>
</table>

</fieldset>
</div>
</body>
</html>

