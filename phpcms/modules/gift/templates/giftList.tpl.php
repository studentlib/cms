<html>
<head>
<title>生成礼包</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<style type="text/css">
body{font-family:tahoma Verdana;font-size:15px;}
* {
 margin:0;
 padding:0;
}
table {
 font-family:Verdana, Arial, Helvetica, sans-serif;
 font-size:11px;
 margin:20px;
 border-collapse:collapse;
}
</style>

<script type="text/javascript">
$(document).ready(function(){
	$('#export').click(function(){
	    var url='index.php?m=gift&c=gift&a=exportCode&batch='+$('#batch').val()+'&channel='+$('#channel').val();
	    window.open(url);
    });
});

</script>
</head>
<body>

<div align="center">
<form action="">
<fieldset>
<legend>导出礼品码</legend>
<label>选择批次</label>
<select id="batch">
<?php foreach ($batchs as $v) {
 ?>
<option value="<?php echo $v['no']?>"><?php echo $v['no']?></option>
<?php 
}
?>
</select>
<label>选择渠道</label>
<select id="channel">
<?php foreach ($channels as $v) {
 ?>
<option value="<?php echo $v['channel']?>"><?php echo $v['channel']?></option>
<?php 
}
?>
</select>
<input type="button" id="export" value="导出"/>
</fieldset>
</form>
</div>
<div align="center">
<?php echo $pages;?>
<table border="1" >
<tr>
<th></th>
<th></th>
<th></th>
<th></th>
<th></th>
<th></th>
</tr>
<tr>
<th>批次</th>
<th>渠道</th>
<th>批次渠道</th>
<th>礼品码(明文)</th>
<th>礼品码(密文)</th>
<th>绑定角色ID</th>
<th>使用次数</th>
<th>是否多次使用</th>
</tr>
<?php 
foreach ($list as $k=>$v)
{
?>
<tr> 
<td><a href="index.php?m=gift&c=gift&a=giftList&batch=<?php echo $v['no']?>"><?php echo $v['no']?></a></td>
<td><a href="index.php?m=gift&c=gift&a=giftList&channel=<?php echo $v['channel']?>"><?php echo $v['channel']?></a></td>
<td><a href="index.php?m=gift&c=gift&a=giftList&channel=<?php echo $v['channel']?>&batch=<?php echo $v['no']?>"><?php echo $v['no']?>-<?php echo $v['channel']?></a></td>
<td><?php echo $v['code']?></td>
<td><?php echo $v['key']?></td>
<td><?php echo $v['uid']?></td>
<td><?php echo $v['usedcount']?></td>
<td><?php echo $v['repeat']?></td>
</tr>
<?php
}
?>
</table>
</div>
</body>
</html>
