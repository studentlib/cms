<html>
<head>
<title>测试充值平台</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
$('#sub').click(function(){
    	$.post('?m=charge&c=index&a=charge',
    	 {'roleid':$('#roleid').html(),'orderid':$('#orderid').html(),'count':$('#count').html(),'area':$('#area').html(),'ticket':$('#ticket').val()},
    	 function(){
    		location='?m=charge&c=index&a=success&count='+$('#count').html();
       });
});
	
});
</script>
</head>
<body>
<div align="left">
<form id="charge" action="">
<fieldset>
<legend>充值信息</legend>
<label>区服:</label>
<label id="area"><?php echo $area ?></label>
<br/>
<label>订单号:</label>
<label id="orderid"><?php echo $orderid ?></label>
<br/>
<label >角色ID:</label>
<label id="roleid"><?php echo $roleid ?></label>
<br/>
<label>充值金额:</label>
<label id="count"><?php echo $count ?></label>
<br/>
<input id="ticket" type="hidden"  value="<?php echo $ticket ?>">
<input id="sub" type="button"  value="提交">
</fieldset>
</form>
</div>
</body>
</html>