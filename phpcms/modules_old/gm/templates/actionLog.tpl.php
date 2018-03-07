<html>
<head>
<title>生成礼包</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="statics/js/calendar/calendar.js"></script>
<script type="text/javascript" src="statics/js/calendar/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/jscal2.css"/>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/border-radius.css"/>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/win2k.css"/>
<style type="text/css">
body{font-family:tahoma Verdana;font-size:12px;}
* {
 margin:0;
 padding:0;
}
table {
 font-family:Verdana, Arial, Helvetica, sans-serif;
 font-size:12px;
 margin:20px;
 border-collapse:collapse;
}
th {
 padding:3px;
}
</style>

<script type="text/javascript">
$(document).ready(function(){
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
	$('#action').change(function(){
                var action=$('#action').val();
                var username=$('#username').val();
		var st=$('#st').val();
		var ed=$('#ed').val();
                var page=$('#page').val();
                $.get('index.php?m=gm&c=log&a=actionLog&username='+username+'&action='+action+'&st='+st+'&ed='+ed,function(data){
                        $('body').html(data);   
                        $('#action').val(action);
                        $('#username').val(username);
                        $('#st').val(st);     
			$('#ed').val(ed);
                });
        });
	$('#username').change(function(){
                var action=$('#action').val();
                var username=$('#username').val();
                var st=$('#st').val();
                var ed=$('#ed').val();
                var page=$('#page').val();
                $.get('index.php?m=gm&c=log&a=actionLog&username='+username+'&action='+action+'&st='+st+'&ed='+ed,function(data){
                        $('body').html(data);   
                        $('#action').val(action);
                        $('#username').val(username);
                        $('#st').val(st);     
                        $('#ed').val(ed);
                });
        });
        $('#find').click(function(){
                var action=$('#action').val();
                var username=$('#username').val();
                var st=$('#st').val();
                var ed=$('#ed').val();
                var page=$('#page').val();
                $.get('index.php?m=gm&c=log&a=actionLog&username='+username+'&action='+action+'&st='+st+'&ed='+ed,function(data){
                        $('body').html(data);   
                        $('#action').val(action);
                        $('#username').val(username);
                        $('#st').val(st);     
                        $('#ed').val(ed);
                });
        });
        
	$('#export').click(function(){
	    var action=$('#action').val();
            var username=$('#username').val();
            var st=$('#st').val();
            var ed=$('#ed').val();
            var page=$('#page').val();
	    var url='index.php?m=gm&c=log&a=export&username='+username+'&action='+action+'&st='+st+'&ed='+ed;
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
<label>选择管理员</label>
<select id="username">
<?php
foreach($username as $k=>$v){
	$check='';
	if(!isset($_REQUEST['username']) && $v['username']=='fuyuyy') $check='selected';
	if(isset($_REQUEST['username'])) $check='selected';
	echo '<option value="'.$v['username'].' "'.$check.'>'.$v['username'].'</option>';
}
?>
</select>
<label>选择操作</label>
<select id="action">
<option value='buy'>虚拟充值</option>
</select>
<label>起始时间:</label>
<input type="text" name="st" id="st" value="<?php echo date('Y-m-d H:i:s',time()-432000);?>" style="width: 140px;"/>
<label>截止时间:</label>
<input type="text" name="ed" id="ed" value="<?php echo date('Y-m-d H:i:s');?>" style="width: 140px;"/>

<input type="button" id="find" value="查找"/>
<input type="button" id="export" value="导出"/>
</fieldset>
</form>
</div>
<div align="center">
<br/>
<div id="pages">
<?php echo $pages;?>
</div>
<table border="1" bordercolor="green">
<tr>
<th>管理员</th>
<th>操作</th>
<th>日期</th>
<th> server服</th>
<th>虚拟充值ID</th>
<th>档位</th>
</tr>
<?php 
foreach ($log as $k=>$v)
{
$data=json_decode($v['data'],true);
?>
<tr> 
<td><?php echo $v['username']?></td>
<td><?php echo $v['action']?></td>
<td><?php echo $v['time']?></td>
<td><?php echo $data['sid'];?></td>
<td><?php echo $data['uid'];?></td>
<td><?php echo $data['itemid'];?></td>
</tr>
<?php
}
?>
</table>
</div>
</body>
</html>
