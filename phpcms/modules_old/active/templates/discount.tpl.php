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
    $('#channels').change(function(){
        $.post('?m=active&c=active&a=updateChannel',{'channel':$(this).val()},function(){
           location.reload();
        });
     });
	 
	$('#save').click(function(){
		var post={};
		var content={}; 
		if('<?php echo time();?>'< '<?php echo $server;?>' && <?php echo $_SESSION['sid'];?>!='999'){
			alert("Hoạt động chưa kết thúc, không được phép thay đổi");
			return;
		} 
		$("#edit :input").each(function(k,v){
			if($(v).attr('class')=='item')
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
	    $('.item').each(function(k,v){
	    	 var idc=$(v).attr('id').split('_');
	         if(content[idc[2]]==undefined)
	           {
	               content[idc[2]]={};
	           }
	         if(content[idc[2]]['ReturnItem']==undefined)
	         {
	        	   content[idc[2]]['ReturnItem']=[];
	         }    
	         var idx=$(v).val();
	         var count=$('#Count_'+idc[1]+'_'+idc[2]).val();
	         var x={};
	         x[idx]=count;
             content[idc[2]]['ReturnItem'].push(x);
     	});
		
	    post.sid=$('#servers').val();
	    post.content=content;
	    post.comment=$('#comment').val();
	    $.post('index.php?m=active&c=active&a=update_discount',post,function(data){
	        alert(data.msg);
	    },'json');
    }); 
      
	$('#save_all').click(function(){
	   var post={};
	   post.sid=$('#servers').val(); 
	   post.servers=[];
	   var activeEndTime=<?php echo $activeTypeTime;?>;
	   $('#sservers').children('option:selected').each(function(k,v){
	   		var server=$(v).val();
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
       $.post('index.php?m=active&c=active&a=sync_discount',post,function(data){
            alert(data.msg);
        },'json');
       
	});
var itemstr='<option value="0">Không được chọn</option>';
var countstr='<option value="0">Không được chọn</option>';
for(var x in items)
{
	var item=items[x];
	itemstr+='<option value='+item['id']+'>'+item['name']+'</option>';
}
for(var i=1;i<=1000;++i)
{
	countstr+='<option value='+i+'>'+i+'</option>';
}
$('#edit .item').click(function(th){
	if($(this).children().length==1)
	{
    	$(this).html(itemstr);
    	$(this).val($(this).attr('select'));
	}
});
$('#edit .count').click(function(th){
	if($(this).children().length==1)
	{
    	
    	var cstr=countstr;
    	
    	var max=$(this).attr('max');
    	var count=$(this).attr('count');
    	var include=true;
      
        
    	for(var j=10;j<=100;j+=5)
    	{
             var xx=parseInt(max*(j*0.01));
             if(xx>1000)
             {
                 include=false;
                 cstr+='<option value='+xx+'>'+xx+'</option>';
             }
    	}
    	if(include==false)
    	{
  	   	 	cstr+='<option value='+count+'>'+count+'</option>';
    	}
    	$(this).html(cstr);
    	$(this).val($(this).attr('count'));
	}
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
<!--
<select name="channels" id="channels" style="width: 150px">
<option value="1" <?php echo $_SESSION['channel']==1?'selected="selected"':''?>>IOS正版</option>
<option value="2" <?php echo $_SESSION['channel']==2?'selected="selected"':''?>>IOS越狱</option>
<option value="3" <?php echo $_SESSION['channel']==3?'selected="selected"':''?>>安卓</option>
<option value="4" <?php echo $_SESSION['channel']==4?'selected="selected"':''?>>天机</option>
</select>
-->
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
    $mc=array();
    preg_match_all('/(\d+)/s', $v['Des'],$mc);
    $items=$v['ReturnItem'];
//     $hidden=$v['PlatformID']==$_SESSION['channel']?'':'display:none;';
?>
<div style="margin: 20px; border-style:solid; border-width:1px;border-color: gray;<?php echo $hidden;?>"> 
<input type="hidden" id="ID_<?php echo $v['ID'];?>" value="<?php echo $v['ID'];?>"/>
<label>Tiêu đề</label>
<input id="Des_<?php echo $v['ID'];?>" type="text" style="width: 100px;" disabled="disabled" value="<?php echo $v['Des'];?>"/>
<label>Kênh</label>
<input id="PlatformDes_<?php echo $v['ID'];?>" type="text" disabled="disabled" value="<?php echo $v['PlatformDes'];?>"/>
<label>Số lần</label>
<input id="Times_<?php echo $v['ID'];?>" type="text" style="width: 100px;" value="<?php echo $v['Times'];?>"/>
<input id="PlatformID_<?php echo $v['ID'];?>" type="hidden"  value="<?php echo $v['PlatformID'];?>"/>
<?php 
$selected=array();
$i=0;
foreach ($items as $k=>$va) {
$i++;
?>
<label>V.phẩm khuyến mại<?php echo $i;?></label>
<input list="company" type="text" name="" id="ReturnItem_<?php echo $i;?>_<?php echo $v['ID'];?>" class="item" style="width: 80px"; 
value='<?php echo $config[array_keys($va)[0]]['name'];?>' onblur="isItemEexit('ReturnItem_<?php echo $i;?>_<?php echo $v['ID'];?>')">
<datalist id="company">
<?php  
 foreach ($config as $value) {
    echo '<option value="'.$value['name'].'" >'.$value['id'].'</option>';
}
?>
 </datalist>
<input type="text" name="" id="Count_<?php echo $i;?>_<?php echo $v['ID'];?>" class="count" style="width: 50px";
 value=<?php echo array_values($va)[0];?> >
<?php 
}
?>
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
    echo "<option  $selected value=\"".$value['ServerType'].'" >'.$value['id'].'server '.$value['text'].'</option>';
}
?>
</select>
<input type="hidden" id="comment" value="<?php echo htmlspecialchars($comment);?>"/>
<input id="save" type="button" value="Lưu" style="width:50px;height:20px"/>
<input id="save_all" type="button" value="Đồng bộ tất cả server" style="width:120px;height:20px"/>
</fieldset>
</form>

<script>
var sets=<?php echo json_encode($select,JSON_FORCE_OBJECT);?>;

</script>
</body>
</html>
