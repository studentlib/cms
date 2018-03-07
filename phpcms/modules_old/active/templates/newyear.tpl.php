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
		var content_get={}; 
		if('<?php echo time();?>'< '<?php echo $server;?>' && <?php echo $_SESSION['sid'];?>!='999'){
			alert("Hoạt động chưa kết thúc, không được phép thay đổi");
			return;
		}
		$("#edit :input").each(function(k,v){
		   if($(v).attr('name')=='get'){
			var idc_get=$(v).attr('id').split('_');
			   if(content_get[idc_get[1]]==undefined)
		   		{
				   content_get[idc_get[1]]={};
		   		}
		   		if(idc_get[0]=='Item'){
		   			return;
		   		}
		   		if(idc_get[0]=='Count'){
		   			return;
		   		}
		   		content_get[idc_get[1]][idc_get[0]]=$(v).val();
				return;	
		   }
			
			if($(v).attr('class')=='item')
			{
				return ;
			}
			if($(v).attr('class')=='count')
			{
				return ;
			}
			if($(v).attr('class')=='getitem')
			{
				return ;
			}
			if($(v).attr('class')=='getcount')
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
	    $('.get_item').each(function(k,v){
	    	var idc=$(v).attr('id').split('_');
	         if(content_get[idc[2]]==undefined)
	           {
	               content_get[idc[2]]={};
	           }
	         if(content_get[idc[2]]['ItemDrop']==undefined)
	         {
	        	   content_get[idc[2]]['ItemDrop']=[];
	         } 
	         var idx=$(v).val();
	         var count=$('#Count_'+idc[1]+'_'+idc[2]).val();
	         var x={};
	         x[idx]=count;
             content_get[idc[2]]['ItemDrop'].push(x);
	    });
	    $('.item').each(function(k,v){
	    	 var idc=$(v).attr('id').split('_');
	         if(content[idc[2]]==undefined)
	           {
	               content[idc[2]]={};
	           }
	         if(content[idc[2]]['CostItem']==undefined)
	         {
	        	   content[idc[2]]['CostItem']=[];
	         }    
	         var idx=$(v).val();
	         var count=$('#Count_'+idc[1]+'_'+idc[2]).val();
	         var x={};
	         x[idx]=count;
             content[idc[2]]['CostItem'].push(x);
		});
	    $('.getitem').each(function(k,v){
	    	 var idc=$(v).attr('id').split('_');
	         if(content[idc[2]]==undefined)
	           {
	               content[idc[2]]={};
	           }
	         if(content[idc[2]]['GetItem']==undefined)
	         {
	        	   content[idc[2]]['GetItem']=[];
	         }    
	         var idx=$(v).val();
	         var count=$('#GetCount_'+idc[1]+'_'+idc[2]).val();
	         var x={};
	         x[idx]=count;
             content[idc[2]]['GetItem'].push(x);
		});
	    post.sid=$('#servers').val();
	    post.content=content;
	    post.content_get=content_get;
	    post.comment=$('#comment').val();
	    post.comment_get=$('#comment_get').val();
	    $.post('index.php?m=active&c=active&a=update_newyear',post,function(data){
	        alert(data.msg);
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
       $.post('index.php?m=active&c=active&a=sync_newyear',post,function(data){
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
$i=0;
foreach ($xml_get_arr as $k1=>$v1)
{
    $i++;
	$attr_get=$v1;
	$product=$attr_get['ItemDrop'];
?>
<label>ID:</label><input type="text" name="get"  id="ID_<?php echo $attr_get['ID'];?>" value="<?php echo $attr_get['ID'];?>" style="width:50px ,font-size:15px">

<label>Sức mạnh thể xác:</label><input type="text" name="get" disabled="disabled" id="StrengthValue_<?php echo $attr_get['ID'];?>" value="<?php echo $attr_get['StrengthValue'];?>" style="width:50px">

<label>Bỏ đồ<?php echo $i?>:</label>
<input type="text" list="company" class="get_item" name="get" id="Item_<?php echo $i?>_<?php echo $attr_get['ID'];?>" 
value="<?php echo $config[array_keys($product['0'])['0']]['name'];?>" style="width:100px" onblur="isItemEexit('Item_<?php echo $i?>_<?php echo $attr_get['ID'];?>')">
<datalist id="company">
<?php  
 foreach ($config as $value) {
    echo '<option value="'.$value['name'].'" >'.$value['id'].'</option>';
}
?>
 </datalist>

<input type="text" name="get" id="Count_<?php echo $i?>_<?php echo $attr_get['ID'];?>" value="<?php echo array_values($product['0'])['0'];?>" style="width:30px">

<label>Xác suất thả:</label><input type="text" name="get" id="Probability_<?php echo $attr_get['ID'];?>" value="<?php echo $attr_get['Probability'];?>" style="width:80px">

<input type="hidden" name="get" id="Des_<?php echo $attr_get['ID'];?>" class="Des_<?php echo $attr_get['ID'];?>" value="<?php echo $attr_get['Des'];?>"><br/><br/>
<?php	
}
?>

<?php 
foreach ($xml_arr as $k=>$v)
{
    $attr=$v;
    $items=$attr['CostItem'];
    $products=$attr['GetItem'];
//     var_dump($items);var_dump($products);
?>
<div style="margin: 20px; border-style:solid; border-width:1px;border-color: gray;"> 
<label>Đổi ID</label>
<input id="ID_<?php echo $attr['ID'];?>" type="text"  disabled="disabled" value="<?php echo $attr['ID'];?>"/>
<label>V.phẩm tiêu hao</label>
<input type="text" list="company" name="" id="Items_<?php echo $x;?>_<?php echo $attr['ID'];?>" class="item" style="width: 80px"; 
value='<?php echo $config[array_keys($items['0'])['0']]['name'];?>' onblur="isItemEexit('Items_<?php echo $x;?>_<?php echo $attr['ID'];?>')">
	<datalist id="company">
	<?php  
	 foreach ($config as $value) {
	    echo '<option value="'.$value['name'].'" >'.$value['id'].'</option>';
	}
	?>
	 </datalist>
<input type="text" name="" id="Count_<?php echo $x;?>_<?php echo $attr['ID'];?>" class="count" style="width: 50px";
 value='<?php echo array_values($items['0'])['0'];?>' >

<label>V.phẩm quay ra</label>
<input type="text" list="company" name="" id="GetItem_<?php echo $x;?>_<?php echo $attr['ID'];?>" class="getitem" style="width: 80px"; 
value='<?php echo $config[array_keys($products['0'])['0']]['name'];?>' onblur="isItemEexit('GetItem_<?php echo $x;?>_<?php echo $attr['ID'];?>')">
<datalist id="company">
<?php  
 foreach ($config as $value) {
    echo '<option value="'.$value['name'].'" >'.$value['id'].'</option>';
}
?>
 </datalist>
<input type="text" name="" id="GetCount_<?php echo $x;?>_<?php echo $attr['ID'];?>" class="getcount" style="width: 50px";
 value='<?php echo array_values($products['0'])['0'];?>' >
<label>Đổi Tối đa</label>
<input id="Gets_<?php echo $attr['ID'];?>" type="text"  value="<?php echo $attr['Gets'];?>"/>
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
<input type="hidden" id="comment_get" value="<?php echo htmlspecialchars($comment_get);?>"/>
<input id="save" type="button" value="Lưu" style="width:50px;height:20px"/>
<input id="save_all" type="button" value="Đồng bộ tất cả server" style="width:120px;height:20px"/>
</fieldset>
</form>


</body>
</html>
