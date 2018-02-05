<html>
<head>
<title>账号查询</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="statics/js/calendar/calendar.js"></script>
<script type="text/javascript" src="statics/js/calendar/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/jscal2.css"/>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/win2k.css"/>
<style type="text/css">
table {
 font-family:Verdana, Arial, Helvetica, sans-serif;
 font-size:14px;
 margin:20px;
 border-collapse:collapse;
}
div{
        margin:0px;
        padding:0px;
};
</style>
<script type="text/javascript">

$(document).ready(function(){
   Calendar.setup({
       weekNumbers: '0',
       inputField : 'st',
       trigger    : 'st',
       dateFormat: '%Y-%m-%d',// %H:%M:%S
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
        console.log(dtstr);
        $('#st').val(dtstr);
    }  


    $('#servers').change(function(){
        $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
           location.reload();
        });
     });
    $('#buy').click(function(){
      
        if($('#userid').val()==null)
        {
        	return $('#userid').focus();
        }
        if($('#itemid').val()==null)
        {
            return $('#itemid').focus();
        }
        var pdata={};
        pdata.sid=$('#servers').val();
        pdata.uid=$('#userid').val();
        pdata.itemid=$('#itemid').val();
        $.post('?m=gm&c=vip&a=buy',pdata,function(data){
            alert(data['msg']); 
        },'json');
     });

    $('#search').click(function(){
        if($('#acc').val()=='')
        {
            return $('#acc').focus();
        }
        $.getJSON('?m=gm&c=gm&a=getInfo&id='+$('#acc').val(),function(data){
        console.log(data);
	$('#information').html('name:'+data.name+',账号:'+data.acount); 
	  if(data['msg']==undefined)
           {
               $('#userid').contents().remove();
               $('#userid').append('<option value="'+data['uid']+'">'+data['uid']+'</option>');
               $('#userid').attr('size',$('#userid').attr('size')+1);
           }else{
               alert(data['msg']);
           }
        });
    });

    $('#find').click(function(){
	var ddata={};
	ddata.sid=$('#servers').val();
	ddata.uid=$('#uid').val();
    ddata.st=$('#st').val();
    ddata.usern=$('#usern').val();
	$.post('?m=gm&c=vip&a=dummy',ddata,function(data){
       $('#his_dummy ~ tr').remove(); 
	   if(data.ret=='1'){alert(data.msg);exit;}
            for(var x in data.msg)
            {
               var line='<tr>';
               var horder=data.msg[x];
                for(var y in horder)
                {
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
<div>
<div style="width:300px;float:left;" align="left">
<form action="">
<fieldset><legend>输入角色ID</legend>
<input name="acc" id="acc" type="text" />
<input name="search" id="search" type="button" value="查找" >
<div id='information' style="color:red; font-size:14px"></div>
</fieldset>
<fieldset style="width: 200px;"><legend>区服列表</legend>
<select name="servers" id="servers" style="width: 150px;">
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'区 '.$value['text'].'</option>';
}
?>
</select>
</fieldset>
</form>
</div>

<div style="width:300px;float:left;clear:left;" align="left">
<form action="">
<fieldset style="width: 200px; float:left;"><legend>角色ID列表</legend>
<select name="userid" id="userid" size="<?php echo count($keys)>0?count($keys)+1:2?>" style="width: 200px;height: 800px">
<?php
foreach ($keys as $value) {
    $new=substr($value, strpos($value, ':')+1);
    echo "<option value=".$new.">$new</option>";
}
?>
</select>
</fieldset>
</form>
</div>

<div  align="left">
<fieldset><legend>购买VIP物品</legend>
<select name="itemid" id="itemid"  size="<?php echo count($pays)>0?count($pays)+1:1?>" style="width: 200px;height:100%;">
<?php
foreach ($pays as $k=>$value) {
    echo "<option value=".$k.'>'.$value['Des']."</option>";
}
?>
</select>
<input type="button" id="buy" value="购买" />


<div align="center" style="float:right;">
<form action="">
<fieldset><legend>虚拟充值查找</legend>
uid:<input name="uid" id="uid" type="text" style="width:80;"/>
操作时间:<input type="text" id="st" value="<?php echo date('Y-m-d',time());?>" style="width:80;"/>
管理员:<input name="usern" id="usern" type="text" style="width:80;"/>
<input name="find" id="find" type="button" value="查找" >
<table id="his_res" border="2px" bordercolor="green">
<tr id="his_dummy">
<th>流水id</th><th>管理员</th><th>服务器编号</th><th>角色ID</th><th>物品ID</th><th>管理员ip</th><th>发货时间</th><th>管理员邮箱</th><th>价格</th>
</tr>
</table>
</fieldset>
</form>
</div>

</fieldset>
</div>

</div>
</body>
</html>
