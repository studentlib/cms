<html>
<head>
<title>Xem tài khoản</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#search').click(function(){
		$('#error').html('');
	    if($('#account').val()==''&&$('#uname').val()=='')
	    {
		    return $('#error').html('Nhập TK hoặc tên n.vật');
	    }
	    $('#res').html('');
	    var post={};
	    post.sid=$('#servers').val();
	    post.account=$('#account').val();
	    post.uname=$('#uname').val();
	    $.post('?m=gm&c=account&a=search',post,function(data){
	        if(data['ret']==0)
	        { 
		        $('#res').html(data['uid']);
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
<div style="width:400px;float:none;" align="left">
<form action="">
<fieldset style="width: 200px;"><legend>DS server</legend>
<select name="servers" id="servers" style="width: 150px;">
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'Server'.$value['text'].'</option>';
}
?>
</select>
</fieldset>
</form>
<div style="width:400px;float:none;" align="left">
<form action="">
<fieldset>
<legend>Xem tài khoản</legend>
<label>Tài khoản:</label>
<input type="text" id="account" value="" />
<br/>
<label>Nhân vật:</label>
<input type="text" id="uname" value=""/>
<br/>
<input type="button" id="search" value="Xem" />
<span id="error" style="color: red;"></span>
</fieldset>
<fieldset>
<legend>Kết quả:</legend>
<div id="res">

</div>
</fieldset>
</form>
</div>
</div>
</body>
</html>
