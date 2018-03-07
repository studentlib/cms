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
		    return $('#error').html('Vui lòng nhập id hoặc mã quà tặng');
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
<legend>Yêu cầu sử dụng quà tặng</legend>
<label>Mã quà tặng:</label>
<input type="text" id="account" value="" />
<br/>
<label>&nbspid:&nbsp</label>
<input type="text" id="uname" value=""/>
<br/>
<input type="button" id="search" value="Tra " />
<span id="error" style="color: red;"></span>
</fieldset>
<fieldset>
<legend>Kết quả truy vấn:</legend>
<div id="res" style="color:red">
<tr><td>id--</td><td>Mã quà tặng--</td><td>uid----</td><td>Hàng loạt--</td><td>Kênh</td></tr><br>
</div>
</fieldset>
</form>
</div>
</body>
</html>

