<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="statics/js/calendar/calendar.js"></script>
<script type="text/javascript" src="statics/js/calendar/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/jscal2.css"/>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/border-radius.css"/>
<link rel="stylesheet" type="text/css" href="statics/js/calendar/win2k.css"/>
<script type="text/javascript" src="statics/config/logs.js?<?php echo time();?>"></script>
<script type="text/javascript">
$(document).ready(function(){
	$.each(logs,function(k,v){
		var kl=module_table[k]?module_table[k]:k;
		$('#module').append('<option value="'+k+'">'+kl+'</option>');
    });
    $('#module').change(function(){
		if(logs[$(this).val()])
		{
			$('#action').contents().remove();
			$('#action').append('<option value="">所有</option>');
			$.each(logs[$(this).val()],function(k,v){
				$('#action').append('<option value="'+k+'">'+v+'</option>');
			});
		}
    });
    $('#module').change();
    Calendar.setup({
        weekNumbers: '0',
        inputField : 'time',
        trigger    : 'time',
        dateFormat: '%Y-%m',
        showTime:  'true',
        minuteStep: 1,
        onSelect   : function() {this.hide();}
        });
    $('#time').keydown(function(){
		return false;
     });

    $('#search').click(function(){

    	if($('#time').val()=='')
    	{
        	return $('#time').focus();
    	}
        
	var post={};
	post.sid=$('#sid').val();
	post.time=$('#time').val();
	post.module=$('#module').val();
	post.limit=$('#limit').val();
	post.action=$('#action').val();
	post.uid=$('#uid').val();
	$.post('?m=gm&c=log&a=query',post,function(data){
		if(data.code!=undefined&&data.code!=0)
		{
			$('#msg').html(data.msg);;
		}else if(data.data){
			var res='';
			$.each(data.data,function(k,v){

				 var div='<div>';
				 $.each(v,function(ki,vi){
						if(ki=='_id')
						{
							return;
						}
					var kl=trans_table[ki]?trans_table[ki]:ki;
					var vl=vi;
					if(ki=='ACTION')
					{
						vl=logs[$('#module').val()][vi]?logs[$('#module').val()][vi]:vi;
					}
					div+=kl+':'+vl+' ';
			     });
				 div+='</div>';
				 res+=div;
			});
			$('#res').html(res);
		}
		
        },'json');
    });
});
</script>
<style type="text/css">
input{vertical-align:left;width: 150px;float:left;}
body{font-family:tahoma Verdana;font-size:12px;}
</style>
</head>
<body>
<?php 
//global $keys;
?>
<div style="width:1000px;float:left;">
<form action="">
<fieldset style="width: 800px;"><legend>区服列表</legend>
<select name="servers" id="sid" style="width: 100px;float: left;">
<?php 
foreach ($servers as $value) {
     echo '<option value="'.$value['ServerType'].'">'.$value['id'].'区 '.$value['text'].'</option>';
}
?>
</select>
<label style="float: left;" >时间</label>
<input type="text" id="time" value="<?php echo date('Y-m');?>" style="width: 80px;float: left;"/> 
<label style="float: left;" >模块</label>
<select id="module" style="width: 100px;float: left;">

</select>
<label style="float: left;">行为</label>
<select id="action" style="width: 100px;float: left;">

</select>
<label style="float: left;">角色ID</label>
<input type="text" id="uid" style="width: 120px;float: left;"/> 
<select id="limit">
<option value="500">500</option>
<option value="1000">1000</option>
<option value="2000">2000</option>
<option value="5000">5000</option>
<option value="10000">10000</option>
</select>
<input type="button" name="search" id="search" value="查询" style="width: 50px;float: right;"/>
</fieldset>
<span id="msg" style="color: red;"></span>
<fieldset>
<legend>查询结果</legend>

<div id="res">

</div>
</fieldset> 
</form>
</div>
</html>

