<html>
<head>
<title>生成礼包</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<style type="text/css">
body{font-family:tahoma Verdana;font-size:12px;}
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
<legend>Xuất giftcode</legend>
<label>Chọn lượt</label>
<select id="batch">
<?php foreach ($batchs as $v) {
 ?>
<option value="<?php echo $v['no']?>"><?php echo $v['no']?></option>
<?php 
}
?>
</select>
<label>Chọn kênh</label>
<select id="channel">
<?php foreach ($channels as $v) {
 ?>
<option value="<?php echo $v['channel']?>"><?php echo $v['channel']?></option>
<?php 
}
?>
</select>
<input type="button" id="export" value="Xuất"/>
</fieldset>
</form>
</div>
<div align="center">
<?php echo $pages;?>
<table border="1" >
<!-- <tr> -->
<!-- <th></th> -->
<!-- <th></th> -->
<!-- <th><input type="button" id="export" value="Xuất giftcode" /></th> -->
<!-- <th></th> -->
<!-- <th></th> -->
<!-- <th></th> -->
<!-- </tr> -->
<tr>
<th>Lượt</th>
<th>Kênh</th>
<th>Kênh chọn</th>
<th>Giftcode(Chi tiết)</th>
<th>Giftcode(Văn bản mật)</th>
<th>Lưu n.vật ID</th>
<th>Số lần sử dụng</th>
<th>Sử dụng nhiều lần?</th>
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