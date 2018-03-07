<html>
<head>
<title>活动管理</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="statics/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="statics/js/calendar/calendar.js"></script>
<script type="text/javascript" src="statics/js/calendar/lang/en.js"></script>
<script type="text/javascript" src="statics/config/config.js"></script>
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
$(document).ready(function(){
    $('#servers').change(function(){
        $.post('?m=gm&c=gm&a=updateSid',{'sid':$(this).val()},function(){
           location.reload();
        });
     });
	 
	$('#save').click(function(){
		var post={};
		var content={}; 
		if("<?php echo time();?>"<"<?php echo $closetime;?>" && <?php echo $_SESSION['sid'];?>!='999'){
			alert("Hoạt động chưa kết thúc, không được phép thay đổi");
			return;
		}
		$("#edit :input").each(function(k,v){
			var idc=$(v).attr('id').split('_');
			if($(v).attr('class')=='item')
			{
				return ;
			}
			if($(v).attr('class')=='count')
			{
				return ;
			}
			if(content[idc[1]]==undefined)
			{
				content[idc[1]]={};
			}
			content[idc[1]][idc[0]]=$(v).val();
    		}); 
		    post.sid=$('#servers').val();
		    post.content=content;
		    post.comment=$('#comment').val();
		    $.post('index.php?m=active&c=active&a=update_sevenDayActive',post,function(data){
		        alert(data.msg);
			location.reload();
		    },'json');
    }); 
      
	$('#save_all').click(function(){
	   var post={};
	   post.sid=$('#servers').val(); 
	   post.servers=[]; 
	   $('#sservers').children('option:selected').each(function(k,v){
	   		var server=$(v).val();
            var activeEndTime=<?php echo $activeTypeTime;?>;
		    if(activeEndTime[server]>='<?php echo time();?>'){
		        var r=confirm('Vùng '+ server +' có các hoạt động mở, nhấn OK để ghi đè, hủy bỏ');
			  	if(r==false){
			      return true;
			    }
		    }
		   if($(v).val()!=0)
		   {
			   post.servers.push($(v).val());
		   }  
	   }); 
       $.post('index.php?m=active&c=active&a=sync_sevenDayActive',post,function(data){
            alert(data.msg);
        },'json');
	});
});
</script>
</head>
<body>
<div align="center">
<form action="">
<fieldset>
<legend>Mời chọn server</legend>
<select name="servers" id="servers" style="width: 150px">
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].' server '.$value['text'].'</option>';
}
?>
</select>

</fieldset>
</form>
</div>

<div  align="center">
<form id="edit" action="">
<fieldset>
<legend>Mở bản chỉnh sửa mẫu</legend>
<br/>
<?php
foreach ($xml_arr as $k=>$v)
{
    	if($v['ID']==1){
		$arr=array(30,45,55,65,75,80,85);
        echo '<span >';
        echo "<input id='ID_".$v['ID']."' type='hidden' value='".$v['ID']."' />";
        echo "<input id='Key_".$v['ID']."' type='hidden' value='".$v['Key']."'/>";
        echo "<span>&nbsp".$v['Des'].$str."&nbsp:&nbsp</span><select id='Value_".$v['ID']."' style='width: 60px;'>";
		foreach($arr as $j=>$i){
            $checked=$i==$v['Value']?' selected = "selected"' : '';
            echo "<option ".$checked.">".$i."</option>";
        } 
	echo "</select>";
	echo "<input id='Des_".$v['ID']."' type='hidden' value='".$v['Des']."'/>";
        echo "</span>";
	}else{
	echo '<span >';
        echo "<input id='ID_".$v['ID']."' type='hidden' value='".$v['ID']."' />";
        echo "<input id='Key_".$v['ID']."' type='hidden' value='".$v['Key']."'/>";
        echo "<input id='Value_".$v['ID']."' type='hidden' value='".$v['Value']."'/>";
        echo "<input id='Des_".$v['ID']."' type='hidden' value='".$v['Des']."'/>";
        echo "</span>";
	}
}
?>
</fieldset>
</form>
</div><br/>
<form action="">
<fieldset>
<legend>Chọn server cần đồng bộ</legend>
<select name="sservers" id="sservers" multiple="multiple" size="<?php echo count($servers); ?>"  style="width: 150px">
<option value="0">Hãy chọn</option>
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].' server '.$value['text'].'</option>';
}
?>
</select>
<input type="hidden" id="comment" value="<?php echo htmlspecialchars($comment);?>"/>
<input id="save" type="button" value="Lưu" style="width:50px;height:20px"/>
<input id="save_all" type="button" value="Đồng bộ tất cả server" style="width:120px;height:20px"/>
</fieldset>
</form>
</body>
</html>
