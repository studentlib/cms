<html>
<head>
<title>账号查询</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<style type="text/css">
* {
	margin: 0;
	padding: 2px;
}

table {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	margin: 20px;
	border-collapse: collapse;
}

tr {
	
}
</style>

<script type="text/javascript">
$(document).ready(function(){
	$('#search').click(function(){
		$('#error').html('');
	    if($('#oldUid').val()==''&&$('#newUid').val()=='')
	    {
		    return $('#error').html('Vui lòng nhập uid');
	    }
	    $('#res').html('');
	    var post={};
	    post.sid=$('#servers').val();
	    post.oldUid=$('#oldUid').val();
	    post.newUid=$('#newUid').val();
	    $.post('?m=gm&c=account&a=findRole',post,function(data){
		if(data.ret==1)
	        { 
			var arr=data.msg;
			var tr='';
			var table="<table id='head' border='2'  bordercolor='green' ><tr><td>Số sê ri</td><td>uid</td><td>Tài khoản</td><td>Vai trò</td></tr></table>";
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
		        alert(data.msg);
	        }	

	    },'json');
    });
    $('#switch').click(function(){
            var post={};
            post.sid=$('#servers').val();
            post.oldUid=$('#oldUid').val();
            post.newUid=$('#newUid').val();
	    	var r=confirm('Xác nhận tài khoản chuyển đổi');
            if(r==false){
                  return true;
            }
            $.post('?m=gm&c=account&a=switchRole',post,function(data){
            	if(data.ret==1)
                { 
                        var arr=data.msg;
                        var tr='';
                        var table="<table id='head' border='2'  bordercolor='green' ><tr><td>Số sê ri</td><td>uid</td><td>Tài khoản</td><td>Vai trò</td></tr></table>";
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
                        alert(data.msg);
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
	<div style="float: none;" align="left">
		<form action="">
			<fieldset style="width: 200px;">
				<legend>Danh sách dịch vụ của huyện</legend>
				<select name="servers" id="servers" style="width: 150px;">
<?php
foreach ( $servers as $value ) {
	$selected = $value ['ServerType'] == $_SESSION ['sid'] ? 'selected="selected"' : '';
	echo "<option  $selected value=\"" . $value ['ServerType'] . '" >' . $value ['id'] . 'Quận ' . $value ['text'] . '</option>';
}
?>
</select>
			</fieldset>
		</form>
		<div align="left" style="width: 400px;">
			<form action="">
				<fieldset>

					<legend>Chuyển đổi tài khoản</legend>
					<label>Cũ uid:</label> <input type="text" id="oldUid" value="" /> <br />
					<label>Mới uid:</label> <input type="text" id="newUid" value="" /> <br />
					<input type="button" id="search" value="Tra" /> <input type="button"
						id="switch" value="Chuyển đổi" style="margin: 5px 1px 3px 150px;" /> <span
						id="error" style="color: red;"></span>
				</fieldset>
			</form>
		</div>
		<fieldset style="width: 700px;">
			<legend>Kết quả truy vấn:</legend>
			<div id="res"></div>
		</fieldset>
	</div>
</body>
</html>
