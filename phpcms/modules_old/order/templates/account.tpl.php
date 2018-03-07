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
       $('#server').append('<option value="0">所有</option>');
       for(var x in servers['servers'])
       {
            var server=servers['servers'][x];
            $('#server').append('<option value="'+x+'">'+server+'</option>');
       }
    });
   $('#query').click(function(){
	   var data={};
	    data.ch=$('#channel').val();
	    var sid=[];
	    server=$('#server').val();
	    if(server==0)
	    {
	    	$('#server').children('option').each(function(k,v){
	    	    if($(v).val()!=0)
	    	    {
	    	    	sid.push($(v).val());
	    	    }
		    });
	    }else{
	    	sid.push(server);
	    }
	    data.sid=sid;
	    data.st=$('#st').val();
	    data.ed=$('#ed').val();
        $.post('?m=order&c=order&a=server_account',data,function(data){
       	 $('#header ~ tr').remove();
//       	 $('#t1 ~ td').html('');
       	 
            if(data.code!=0)
            {
                alert(data.msg);
            }
            for(var x in data.result)
            {
                var line='<tr>';
                var order=data.result[x];
           
                for(var y in order)
                {
                        
                    line+='<td>'+order[y]+'</td>';
                }
                line+='</tr>';
                $('#res').append(line);
            }
            for(var y in data.total)
            {
                total=data.total[y];
                $('#'+y).text(total);
            }
         },'json');
    });
   $('#query_all').click(function(){
	   var data={};
	   var ch=[];
	   	$('#channel').children('option').each(function(k,v){
		    if($(v).val()!=0)
		    {
		    	ch.push($(v).val());
		    }
	    });
	    data.ch=ch;
	    var sid=[];
	    server=$('#server').val();
	    if(server==0)
	    {
	    	$('#server').children('option').each(function(k,v){
	    	    if($(v).val()!=0)
	    	    {
	    	    	sid.push($(v).val());
	    	    }
		    });
	    }else{
	    	sid.push(server);
	    }
	    data.sid=sid;
	    data.st=$('#st').val();
	    data.ed=$('#ed').val();
        $.post('?m=order&c=order&a=server_account_all',data,function(data){
       	 $('#header ~ tr').remove();console.log(data);
       	   // console.log(data);
            if(data.code!=0)
            {
                alert(data.msg);
            }
            for(var x in data.result)
            {
                var line='<tr>';
                var order=data.result[x];
           
                for(var y in order)
                {
                        
                    line+='<td>'+order[y]+'</td>';
                }
                line+='</tr>';
                $('#res').append(line);
            }
            for(var y in data.total)
            {
                total=data.total[y];
                $('#'+y).text(total);
            }
         },'json');
    });
   Calendar.setup({
       weekNumbers: '0',
       inputField : 'st',
       trigger    : 'st',
       dateFormat: '%Y-%m-%d %H:%M:%S',
       showTime:  'true',
       minuteStep: 1,
       onSelect   : function() {this.hide();}
       });
    Calendar.setup({
       weekNumbers: '0',
       inputField : 'ed',
       trigger    : 'ed',
       dateFormat: '%Y-%m-%d %H:%M:%S', 
       showTime:  'true',
       minuteStep: 1,
       onSelect   : function() {this.hide();}
       });


    function CurentTime(now)
    { 
       
        var year = now.getFullYear();       //年
        var month = now.getMonth() + 1;     //月
        var day = now.getDate();            //日
       
        var hh = now.getHours();            //时
        var mm = now.getMinutes();          //分
        var ss = now.getSeconds();          //秒
       
        var clock = year + "-";
       
        if(month < 10)
            clock += "0";
       
        clock += month + "-";
       
        if(day < 10)
            clock += "0";
           
        clock += day + " ";
       
        if(hh < 10)
            clock += "0";
           
        clock += hh + ":";
        if (mm < 10) clock += '0'; 
        clock += mm+":"; 
        if (ss < 10) ss += '0'; 
        clock += ss; 
        return(clock); 
    } 
function changeEnd()
{
	var dt=Date.parse($('#ed').val())+1000;
    var date=new Date();
    date.setTime(dt);
    var dtstr=CurentTime(date);
	$('#ed').val(dtstr);
}  
setInterval(changeEnd, 1000);
    
});
</script>
</head>
<body>
<?php 
?>
<div align="center">
<form action="">
<fieldset>
<legend>Điều kiện tra</legend>
<label>Bắt đầu</label>
<input type="text" id="st" value="<?php echo date('Y-m-d ',time()).'00:00:00';?>"/>
<label>Kết thúc</label>
<input type="text" id="ed" value="<?php echo date('Y-m-d H:i:s',time());?>"/>
<label>Chọn kênh</label>
<select name="channel" id="channel">
</select>
<label>Chọn server</label>
<select name="server" id="server">

</select>
<input type="button" name="query" id="query" value="Xem " />  
<input type="button" name="query_all" id="query_all" value="Xem tất cả" />  
</fieldset>
</form>

</div>
<div align="left">
<form id="edit" action="">
<fieldset>
<legend>Kết quả</legend>
<table id="res" border="2"  bordercolor="green">
<tr id="header">
<th>Mã server</th><th>Mức nạp</th><th>Số lần nạp</th><th>Số người nạp</th>
</tr>

</table>
<table id="t1" border="2" bordercolor="green">
<tr>
<th>Tổng</th><th>Mức nạp</th><th>Lượt nạp</th><th>Số người nạp</th>
</tr>
<tr>
<td>Tổng</td><td id="c_amount">0</td><td id="c_count">0</td><td id="r_count">0</td>
</tr>
</table>
</fieldset>
</form>
</div>

</body>
</html>
