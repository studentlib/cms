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
.lucky label{
	font-size: 14px;
	margin-left:10px;

}
.lucky input{
	font-size: 14px;
	margin:5px;
	width:120px;
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
		var content_lu={};
		console.log(<?php echo $_SESSION['sid'];?>!='999');
		console.log("<?php echo time();?>"<"<?php echo $closetime;?>");
		if("<?php echo time();?>"<"<?php echo $closetime;?>"){
			//if(<?php echo $_SESSION['sid'];?>!='999'){
				alert("活动未结束，禁止修改");
				return;
			//}
		}
		$("#edit :input").each(function(k,v){
			var idc=$(v).attr('id').split('_');
			   if(content[idc[1]]==undefined)
			   {
				   content[idc[1]]={};
			   }
			  if($(v).attr('type')=='checkbox'){
				  if($(v).attr('checked')){
					  content[idc[1]][idc[0]]="1";
					  }else{ content[idc[1]][idc[0]]="0";}
				  return;
		      }
			 content[idc[1]][idc[0]]=$(v).val();
    	}); 
		$("#edit_lu :input").each(function(k,v){
			var idc=$(v).attr('id').split('_');
			   if(content_lu[idc[1]]==undefined)
			   {
				   content_lu[idc[1]]={};
			   }
			  if($(v).attr('type')=='checkbox'){
				  if($(v).attr('checked')){
					  content_lu[idc[1]][idc[0]]="1";
					  }else{ content_lu[idc[1]][idc[0]]="0";}
				  return;
		      }
			 content_lu[idc[1]][idc[0]]=$(v).val();
    	});
		 post.sid=$('#servers').val();
		    post.content=content;
		    post.content_lu=content_lu;
		    post.comment=$('#comment').val();
		    post.comment_lu=$('#comment_lu').val();
		    $.post('index.php?m=active&c=active&a=update_turntable',post,function(data){
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
		        var r=confirm(server+'区本活动开启,点击确定覆盖,取消忽略');
			  	if(r==false){
			      return true;
			    }
		    }
		   if($(v).val()!=0)
		   {
			   post.servers.push($(v).val());
		   }  
	   }); 
       $.post('index.php?m=active&c=active&a=sync_turntable',post,function(data){
            alert(data.msg);
        },'json');
	});
});
function isItemEexit(id){
	var post={};
	post.item=$('#'+id).val();
	$.post('index.php?m=active&c=active&a=is_item_exit',post,function(data){
		if(data.code==1){alert(data.msg);$('#'+id).val("").focus();}  
        },'json');
}
</script>
</head>
<body>
<div align="center">
<form action="">
<fieldset>
<legend>请选择服务器</legend>
<select name="servers" id="servers" style="width: 150px">
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
<div  align="center" style="float:left;width:200px;height:350px;">
<form id="edit_lu" action="">
<fieldset>
<legend>转盘概率编辑</legend>
<?php 
foreach ($xml_lu_arr as $k=>$v)
{
    $lucky=$v;
    if($lucky['ID']<7){
        $type='type="hidden"';
        $type1= "hidden";
    }else{
        $type='type="text"';
        $type1= "";
    }
    echo "<div style=margin: 20px; border-style:solid; border-width:1px;border-color: gray;>";
    echo "<input id='ID_".$lucky['ID']."' type=hidden value='".$lucky['ID']."'/>";
    echo "<input id='Key_".$lucky['ID']."' type=hidden value='".$lucky['Key']."'/>";
    echo "<div $type1 width=150px>".$lucky['Des']."</div><input id='Value_".$lucky['ID']."' $type value='".$lucky['Value']."'/>";
    echo "<input id='Des_".$lucky['ID']."' type=hidden value='".$lucky['Des']."'/>";
    echo "</div>";
?>

<?php 
}
?>

</fieldset>
</form>
</div>
<div  align="center">
<form id="edit" action="">
<fieldset>
<legend>转盘奖品</legend>
<?php 
foreach ($xml_arr as $k=>$v)
{
    $attr=$v;//var_dump($attr);
?>

<input id="ID_<?php echo $attr['ID'];?>" type="hidden" value="<?php echo $attr['ID']?>"/>
<input id="ItemBoxID_<?php echo $attr['ID'];?>" type="hidden" value="<?php echo $attr['ItemBoxID']?>"/>
<div style="margin: 20px; border-style:solid; border-width:1px;border-color: gray;"> 
<label>栏位<?php echo $attr['ID'];?></label>
<!-- <input type="text" name="" id="ItemID_<?php echo $attr['ID'];?>" class="Des" style="width: 80px";  
onblur="isItemEexit('ItemID_<?php echo $attr['ID'];?>')" value='<?php echo $config[(string)$attr["ItemID"]]['name'];?>' > -->
<input list="company" type="text" name="" id="ItemID_<?php echo $attr['ID'];?>" class="Des" style="width: 80px";  
onblur="isItemEexit('ItemID_<?php echo $attr['ID'];?>')" value='<?php echo $config[(string)$attr["ItemID"]]['name'];?>' >
<datalist id="company">
<?php  
 foreach ($config as $value) {
    echo '<option value="'.$value['name'].'" >'.$value['id'].'</option>';
}
?>
 </datalist>
<label>数量</label>
<input id="Count_<?php echo $attr['ID'];?>" name="item_sum" type="text" value="<?php echo $attr['Count']?>"/>
<label>权重</label>
<input id="Weight_<?php echo $attr['ID'];?>" name="weight" type="text" value="<?php echo $attr['Weight']?>"/>
<label>是否展示</label>
<select id="Show_<?php echo $attr['ID'];?>">
<option value="1">是</option>
<option value="0">否</option>
<?php 

if(isset($attr['Show'])){
    if ($attr['Show']==1){
        echo '<option value="'.$attr['Show'].'" selected="selected">是</option>';
    }else{
        echo '<option value="'.$attr['Show'].'" selected="selected">否</option>';
    }
}
?>
</select>
</div>
<?php 
}
?>

</fieldset>
</form>
</div>
<form action="">
<fieldset>
<legend>请选择要同步的服务器</legend>
<select name="sservers" id="sservers" multiple="multiple" size="<?php echo count($servers); ?>"  style="width: 150px">
<option value="0">请选择</option>
<?php 
foreach ($servers as $value) {
    $selected=$value['ServerType']==$_SESSION['sid']?'selected="selected"':'';
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'区 '.$value['text'].'</option>';
}
?>
</select>
<input type="hidden" id="comment" value="<?php echo htmlspecialchars($comment);?>"/>
<input type="hidden" id="comment_lu" value="<?php echo htmlspecialchars($comment_lu);?>"/>
<input id="save" type="button" value="保存" style="width:50px;height:20px"/>
<input id="save_all" type="button" value="同步所选服务器" style="width:120px;height:20px"/>
</fieldset>
</form>
</body>
</html>
