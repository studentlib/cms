<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<style type="text/css">
label{vertical-align:left;width: 100px;float:left;}
input{vertical-align:left;width: 50px;float:left;}
body{font-family:tahoma Verdana;font-size:10px;}
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
     $('#all').change(function(){
	$("input[type='checkbox']").attr("checked","true"); 
     });
});

</script>
</head>
<body>

<div style="float:left;" >
<div style="float:left; padding-right:60px;">
<legend>请选择服务器</legend>
<form action="?m=account&c=vip_rank&a=search" method="post" >
<table >

    <?php foreach($servers as $kk=>$vv){?>
    <tr>
	<th style="width:150px;"><input type="checkbox"  class="server" name="<?php echo $kk;?>" id="server"> <?php echo $vv['id'].'区-'.$vv['text'];?></th>
    </tr>
    <?php }?>
</table>
<input type="checkbox"  class="all" id="all" value="全选">
<input type="submit" id="search" value="查找">
</form>
</div>

<fieldset>
<legend>vip列表</legend>
<table id="content"  border="1" cellspacing="0";>
<caption>vip统计</caption>
<tr>
<th>服务器</th>
<th>0级</th>
<th>1级</th>
<th>2级</th>
<th>3级</th>
<th>4级</th>
<th>5级</th>
<th>6级</th>
<th>7级</th>
<th>8级</th>
<th>9级</th>
<th>10级</th>
<th>11级</th>
<th>12级</th>
<th>13级</th>
<th>14级</th>
<th>15级</th>
<th>16级</th>
<th>17级</th>
<th>18级</th>
</tr>
<?php
foreach($level as $kn=>$vn){
?>
<tr>
<?php 
echo '<th>'.$vn[1].'区-'.$vn[0].'</th>';
unset($vn[1]);unset($vn[0]);
foreach($vn as $k=>$v){
 echo '<th>'.$v.'</th>';
}
?>
</tr>
<?php }?>
</table>

</fieldset>
</div>
</body>
</html>

