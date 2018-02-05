<html>
<head>
<title>活动管理</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="statics/js/calendar/calendar.js"></script>
<script src="statics/js/spin.min.js"></script>
<script src="statics/js/base-loading.js"></script>
<script type="text/javascript" src="statics/js/calendar/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/jscal2.css"/>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/border-radius.css"/>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/win2k.css"/>
<style type="text/css">
body{font-family:tahoma Verdana;font-size:14px;}
*{
    font-size: 15px;
    font: sans-serif;
}
* {
 margin:0;
 padding:2px;
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
var opts = {            
            lines: 13, // 花瓣数目
            length: 20, // 花瓣长度
            width: 10, // 花瓣宽度
            radius: 30, // 花瓣距中心半径
            corners: 1, // 花瓣圆滑度 (0-1)
            rotate: 0, // 花瓣旋转角度
            direction: 1, // 花瓣旋转方向 1: 顺时针, -1: 逆时针
            color: 'red', // 花瓣颜色
            speed: 1, // 花瓣旋转速度
            trail: 60, // 花瓣旋转时的拖影(百分比)
            shadow: false, // 花瓣是否显示阴影
            hwaccel: false, //spinner 是否启用硬件加速及高速旋转            
            className: 'spinner', // spinner css 样式名称
            zIndex: 2e9, // spinner的z轴 (默认是2000000000)
            top: '450', // spinner 相对父容器Top定位 单位 px
            left: '700'// spinner 相对父容器Left定位 单位 px
        };
var spinner = new Spinner(opts);
$(document).ready(function(){
/*    $.post('?m=order&c=order&a=loadChannels',{},function(data){
     config=data;
     for(var x in config)
     {
       var ch=config[x];
       $('#channel').append('<option value="'+x+'">'+ch['name']+'</option>');
     }
     $('#channel').change();
     },'json');
*/    
   $('#query').click(function(){
	//异步请求时spinner出现
        $("#firstDiv").text("");
        var target = $("#firstDiv").get(0);
        spinner.spin(target);
       var data={};
        data.ch=$('#channel').val();
        data.sid=$('#servers').val();
        data.st=$('#st').val();
        data.ed=$('#ed').val();
        $.post('?m=order&c=order&a=search_pay_list',data,function(data){
	$('#header ~ tr').remove();
 	//关闭spinner  
        spinner.spin();
	   if(data.ret!=1)
            {
                alert(data.msg);
            }
            for(var x in data.list)
            {
                var line='<tr>';
                var order=data.list[x];
//  		line+='<td>'+order['area']+'</td>'+'<td>'+order['AccountName']+'</td>'+'<td>'+order['roleid']+'</td>'+'<td>'+order['money']+'</td>'+'<td>'+order['time']+'</td>'+'<td>'+order['PlayerName']+'</td>'+'<td>'+order['26']+'</td>'+'<td>'+order['viplevel']+'</td>'+'<td>'+order['diamond']+'</td>'+'<td>'+order['gold']+'</td>';              
		line+='<td>'+order['area']+'</td>'+'<td>'+order['AccountName']+'</td>'+'<td>'+order['roleid']+'</td>'+'<td>'+order['money']+'</td>'+'<td>'+order['PlayerName']+'</td>'+'<td>'+order['level']+'</td>'+'<td>'+order['viplevel']+'</td>'+'<td>'+order['diamond']+'</td>'+'<td>'+order['gold']+'</td>'+'<td>'+order['time']+'</td>';
//		for(var y in order)
//                {
//                  line+='<td>'+order[y]+'</td>';
//                }
                line+='</tr>';
                $('#res').append(line);
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

    $('#servers').change(function(){
        $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
//           location.reload();
        });
     });    
});
</script>
</head>
<body>
<div id="firstDiv" class="firstDiv"> </div>
<div align="center">
<form action="">
<fieldset>
<legend>查询条件</legend>
<label>开始时间</label>
<input type="text" id="st" value="<?php echo date('Y-m-d ',time()).'00:00:00';?>"/>
<label>结束时间</label>
<input type="text" id="ed" value="<?php echo date('Y-m-d H:i:s',time());?>"/>
<label>选择渠道</label>
<select name="channel" id="channel">
<?php
    foreach ($platform as $k=>$v) {
        echo "<option  value=\"".$k.'" >'.$v.'</option>';
    }
?>
</select>
<label>选择服务器</label>
<select name="servers" size="1" id="servers">
<option value="0" selected="selected">所有区</option>
<?php 
    foreach ($servers as $value) {
       // $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
        echo "<option  value=\"".$value['ServerType'].'" >'.$value['id'].'区 '.$value['text'].'</option>';
    }
?>
</select>
<input type="button" name="query" id="query" value="查询" />  
</fieldset>
</form>

</div>
<div align="center">
<form id="edit" action="">
<fieldset>
<legend>查询结果</legend>
<table id="res" border="2"  bordercolor="green">
<tr id="header">
<!--<th>区服</th><th>账号</th><th>角色</th><th>等级</th><th>ID</th><th>金额</th><th>vip等级</th><th>fist time</th><th>last time</th><th>last￥</th><th>当前元宝</th><th>创建时间</th><th>下线时间</th>-->
<!--<th>区服</th><th>账号</th><th>ID</th><th>金额</th><th>fist time</th><th>last time</th><th>last￥</th>-->
<th>区服</th><th>账号</th><th>ID</th><th>充值金额</th><th>昵称</th><th>等级</th><th>vip等级</th><th>当前元宝</th><th>当前铜币</th><th>活跃时间</th>
</tr>

</fieldset>
</form>
</div>


</body>
</html>
