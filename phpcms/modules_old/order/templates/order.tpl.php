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
<legend>Điều kiện tra</legend>
<label>Chọn kênh</label>
<select name="channel" id="channel">

</select>
<label>Chọn server</label>
<select name="server" id="server">

</select>
<label>Mời nhập</label>
<select name="condition" id="condition">
<!--<option value="1">账号</option>-->
<option value="2">Nhân vậtID</option>
<option value="3">Mã nạp</option>
</select>
<input name="account" id="account" size="50" />
<input type="button" name="query" id="query" value="Xem" />  
</fieldset>
</form>

</div>
<div align="left">
<form id="edit" action="">
<fieldset>
<legend>Kết quả</legend>
<table id="res" border="2px" bordercolor="green">
<tr id="header">
<th>Doanh thuID</th><th>Kênh</th><th>Mã server</th><th>Nhân vậtID</th><th>Mã nạp</th><th>Mã thanh toán platform</th><th>Mức nạp</th><th>Thời gian platform gửi mã nạp</th><th>Mua v.phẩmID</th><th>Sản phẩmID(APPSTORE)</th>
<th>Thời gian bắt đầu</th><th>Trạng thái xử lý</th>
</tr>

</table>
</fieldset>
<fieldset>
<legend>Lịch sử mã nạp</legend>
<table id="his_res" border="2px" bordercolor="green">
<tr id="his_header">
<th>Mã nạp</th><th>Tài khoản</th><th>Nhân vậtID</th><th>Mức nạp</th><th>Mã server</th><th>Kênh</th><th>Hóa đơn</th><th>Vật phẩmID</th><th>Thời gian tạo</th><th>Thời gian xử lý</th>
</tr>

</table>
</fieldset>
</form>
</div>


</body>
</html>