<html>
<head>
<title>账号查询</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<style>
body{font-family:tahoma Verdana;font-size:14px;}
*{
    font-size: 15px;
    font: sans-serif;
}
tr {
border:1px blue solid;
margin-right:3px;
}
table {
 font-family:Verdana, Arial, Helvetica, sans-serif;
 font-size:14px;
 margin:20px;
 border-collapse:collapse;
}
</style>
<script type="text/javascript">
$(document).ready(function(){
	$('#search').click(function(){
		$('#error').html('');
	    if($('#account').val()==''&&$('#uname').val()=='')
	    {
		    return $('#error').html('请输入id或礼品码');
	    }
	    $('#res').html('');
	    var post={};
	    post.code=$('#account').val();
	    post.uid=$('#uname').val();
	    $.post('?m=gift&c=gift&a=sel_code_id',post,function(data){
		if(data.code==110)
		{
			$('#res').append(data['msg']);
		}
		if(data.code==0)
		{
		        $('#res').append(data.str);
		}
	    },'json');
    });

});
</script>
</head>
<body>
<div style="width:400px;float:none;" align="left">
<form action="">
<fieldset>
<legend>礼品使用查询</legend>
<label>礼品码:</label>
<input type="text" id="account" value="" />
<br/>
<label>&nbspid:&nbsp</label>
<input type="text" id="uname" value=""/>
<br/>
<input type="button" id="search" value="查询" />
<span id="error" style="color: red;"></span>
</fieldset>
<fieldset>
<legend>查询结果:</legend>
<div id="res" style="color:red">
<tr><td>流水id--</td><td>礼品码--</td><td>uid</td></tr><br>
</div>
</fieldset>
</form>
</div>
</body>
</html>

