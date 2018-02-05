<html>
<head>
<title>账号查询</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<style type="text/css">
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
tr{

}
</style>

<script type="text/javascript">
$(document).ready(function(){
	$('#search').click(function(){
		$('#error').html('');
	    if($('#account').val()==''&&$('#uname').val()=='')
	    {
		    return $('#error').html('请输入账号或角色名');
	    }
	    $('#res').html('');
	    var post={};
	    post.sid=$('#servers').val();
	    post.account=$('#account').val();
	    post.uname=$('#uname').val();
	    $.post('?m=gm&c=account&a=search',post,function(data){
	        if(data['ret']==0)
	        { 
		 //       $('#res').html(data['uid']);
			var arr=data.uid;
			var tr='';
			var table="<table id='head' border='2'  bordercolor='green' ><tr><td>区服</td><td>uid</td><td>账号</td><td>角色</td></tr></table>";
			$('#res').html(table);
		 	for( var x in arr){
				var tr="<tr><td>"+x+"</td>";
				var arr1=arr[x];
				for(var y in arr1){
				tr+="<td>"+arr1[y]+"</td>";
				}
				tr+="</tr>";
			        $('#head').append(tr);
		 	}
	        }else{
		        $('#res').html(data['msg']);
	        }
		},'json');
    });
    $('#servers').change(function(){
        $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
           location.reload();
        });
     });
});
</script>
</head>
<body>
<div style="float:none;" align="left">
<form action="">
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
<div align="left" style="width: 400px;">
<form action="">
<fieldset>
<legend>账号查询</legend>
<label>账号:</label>
<input type="text" id="account" value="" />
<br/>
<label>角色:</label>
<input type="text" id="uname" value=""/>
<br/>
<input type="button" id="search" value="查询" />
<span id="error" style="color: red;"></span>
</fieldset>
</form>
</div>
<fieldset style="width: 700px;">
<legend>查询结果:</legend>
<div id="res">
</div>
</fieldset>
</div>
</body>
</html>
