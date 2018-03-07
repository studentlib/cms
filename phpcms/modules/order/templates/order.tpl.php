<html>
<head>
<title>活动管理</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="statics/js/calendar/calendar.js"></script>
<script type="text/javascript" src="statics/js/calendar/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/jscal2.css"/>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/border-radius.css"/>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/win2k.css"/>
<style type="text/css">
body{font-family:tahoma Verdana;font-size:14px;}
*{
    font-size: 12px;
    font: sans-serif;
}
* {
 margin:0;
 padding:0;
}
table {
 font-family:Verdana, Arial, Helvetica, sans-serif;
 font-size:14px;
 margin:20px;
 border-collapse:collapse;
}
</style>

<script type="text/javascript">
var config={};
$(document).ready(function(){
     $.post('?m=order&c=order&a=loadChannels',{},function(data){
     config=data;
     for(var x in config)
     {
 	   var ch=config[x];
 	   $('#channel').append('<option value="'+x+'">'+ch['name']+'</option>');
     }
     $('#channel').change();
     },'json');
    
    $('#channel').change(function(){
       $('#server').contents().remove();
       var servers=config[$(this).val()];
       for(var x in servers['servers'])
       {
            var server=servers['servers'][x];
            $('#server').append('<option value="'+x+'">'+server+'</option>');
       }
    });
   $('#query').click(function(){
	    if($('#account').val()=='')
	    {
		    return $('#account').focus();
	    }
	    var condition=$('#condition').val();
	    var data={};
	    data.channel=$('#channel').val();
	    data.server=$('#server').val();
        switch(parseInt(condition))
        {
        case 1:
            data.account=$('#account').val();
            break;
        case 2:
        	data.uid=$('#account').val();
            break;
        case 3:
            data.order_id=$('#account').val();
            break;
            default:break;
        }
        $.post('?m=order&c=order&a=query',data,function(data){
       	    $('#header ~ tr').remove();
            if(data.ret!=0)
            {
                alert(data.msg);
            }
            for(var x in data.result)
            {
                var line='<tr>';
                var order=data.result[x];
                for(var y in order)
                {
		if(y=='billno')continue;
                    if(y=='url')continue;
                    line+='<td>'+order[y]+'</td>';
                }
                line+='</tr>';
                $('#res').append(line);
            }
            $('#his_header ~ tr').remove();
            for(var x in data.his_orders)
            {
                var line='<tr>';
                var horder=data.his_orders[x];
           
                for(var y in horder)
                {
                    if(y=='url')continue;
                   //    alert(horder[y]); 

                    line+='<td>'+horder[y]+'</td>';
                }
                line+='</tr>';
                $('#his_res').append(line);
            }
         },'json');
    });
});
</script>
</head>
<body>
<div align="center">
<form action="">
<fieldset>
<legend>查询条件</legend>
<label>选择渠道</label>
<select name="channel" id="channel">

</select>
<label>选择服务器</label>
<select name="server" id="server">

</select>
<label>请输入</label>
<select name="condition" id="condition">
<option value="2">角色ID</option>
<option value="1">账号</option>
<option value="3">订单号</option>
</select>
<input name="account" id="account" size="50" />
<input type="button" name="query" id="query" value="查询" />  
</fieldset>
</form>

</div>
<div align="left">
<form id="edit" action="">
<fieldset>
<legend>查询结果</legend>
<table id="res" border="2px" bordercolor="green">
<tr id="header">
<th>流水ID</th><th>渠道</th><th>服务器编号</th><th>角色ID</th><th>订单号</th><!--<th>平台支付流水号</th>--><th>充值金额</th><th>平台提交订单时间</th><th>购买物品ID</th><th>产品ID(APPSTORE)</th>
<<<<<<< HEAD
<th>发起时间</th><th>处理状态</th>
=======
<th>发起时间</th><th>处理状态</th><th>苹果交易号</th>
>>>>>>> dea4140... 20180307
</tr>

</table>
</fieldset>
<fieldset>
<legend>历史订单</legend>
<table id="his_res" border="2px" bordercolor="green">
<tr id="his_header">
<<<<<<< HEAD
<th>订单号</th><th>账号</th><th>角色ID</th><th>金额</th><th>服务器编号</th><th>渠道</th><th>小票</th><th>物品ID</th><th>创建时间</th><th>处理时间</th>
=======
<th>订单号</th><th>账号</th><th>角色ID</th><th>金额</th><th>服务器编号</th><th>渠道</th><th>小票</th><th>物品ID</th><th>创建时间</th><th>处理时间</th><th>苹果交易号</th>
>>>>>>> dea4140... 20180307
</tr>

</table>
</fieldset>
</form>
</div>


</body>
</html>
