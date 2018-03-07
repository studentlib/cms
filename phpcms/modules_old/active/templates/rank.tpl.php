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
			if($(v).attr('class')=='ItemReward')
			{
				return ;
			}
			if($(v).attr('class')=='count')
			{
				
				return ;
			}
		   var idc=$(v).attr('id').split('_');
		   if(content[idc[1]]==undefined)
		   {
			   content[idc[1]]={};
		   }
		  if($(v).attr('type')=='checkbox'&&!$(v).attr('checked')){
			    return;
	      }
		   content[idc[1]][idc[0]]=$(v).val();
	    });
	    $('.ItemReward').each(function(k,v){
	    	 var idc=$(v).attr('id').split('_');
	         if(content[idc[2]]==undefined)
	           {
	               content[idc[2]]={};
	           }
	         if(content[idc[2]]['ItemReward']==undefined)
	         {
	        	   content[idc[2]]['ItemReward']=[];
	         }    
	         var idx=$(v).val();
	         var count=$('#Count_'+idc[1]+'_'+idc[2]).val();
	         var x={};
	         x[idx]=count;
             content[idc[2]]['ItemReward'].push(x);
		});
	    post.sid=$('#servers').val();
	    post.content=content;
	    post.comment=$('#comment').val();
	    $.post('index.php?m=active&c=active&a=update_rank',post,function(data){
	        alert(data.msg);
	    },'json');
    }); 
      
	$('#save_all').click(function(){
	   var post={};
	   post.sid=$('#servers').val(); 
	   post.servers=[]; 
	   $('#sservers').children('option:selected').each(function(k,v){
		   if($(v).val()!=0)
		   {
			   post.servers.push($(v).val());
		   }  
	   }); 
       $.post('index.php?m=active&c=active&a=sync_rank',post,function(data){
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
<div align="center">
<form id="edit" action="">
<fieldset>
<legend>Edit sự kiện</legend>
<?php 
foreach ($xml as $k=>$v)
{
    $attr=$v;
    $items=$attr['ItemReward'];
?>
<div style="margin: 20px; border-style:solid; border-width:1px;border-color: gray;"> 
<label>Sự kiện ID</label>
<input id="ID_<?php echo $attr['ID'];?>" type="text"  disabled="disabled" value="<?php echo $attr['ID'];?>"/>
<?php 
$i=1;
foreach ($items as $k=>$va) {
$i++;
?>
<label>V.phẩm khuyến mại<?php echo $i;?></label>
<input list="company" type="text" name="" id="ItemReward_<?php echo $i;?>_<?php echo $attr['ID'];?>" class="ItemReward" style="width: 80px;" 
value='<?php echo $config[array_keys($va)[0]]['name'];?>' onblur="isItemEexit('ItemReward_<?php echo $i;?>_<?php echo $attr['ID'];?>')">
<datalist id="company">
<?php  
 foreach ($config as $value) {
    echo '<option value="'.$value['name'].'" >'.$value['id'].'</option>';
}
?>
 </datalist>
<input type="text" name="" id="Count_<?php echo $i;?>_<?php echo $attr['ID'];?>" class="count" style="width: 50px";
 value='<?php echo array_values($va)[0];?>' >

<?php 
}
?>
<label>Tiêu đề</label>
<input id="Des_<?php echo $attr['ID'];?>" type="text"  disabled="disabled" value="<?php echo $attr['Des'];?>"/>
</div>
<?php 
}
?>
</fieldset>
</form>
</div>
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
